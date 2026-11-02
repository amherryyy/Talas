# One current academic program per student account

**Status:** Original NVSU picker owner-approved 2026-11-01 and hosted schema verified. Manual school/program entry owner-requested 2026-11-02; its migration, app package, and runtime acceptance remain pending.

## Decision

Each student account stores one current academic program and school. After sign-in, a student without a saved selection can choose a college and program offered at NVSU Bayombong or use “My school or program is not listed” to enter a school and program manually. The listed program choice is grouped by college and shows only the program title. The student confirms the choice is current. It is immutable through ordinary app routes and shown read-only in Profile. A later change is handled by the project administrator.

The account-level picker groups BSCS, BSIT, and BSIS under the College of Information Technology Education (CITE), reflecting NVSU's 2025 academic realignment. Their earlier CAS course pages remain the historical sources for the 2019–2020 reference lists; this grouping does not make those lists current approved curricula. Existing program IDs are preserved so students who selected one of these programs before the realignment continue to resolve to the same program and subject scope. [NVSU CITE history](https://cite.nvsu.edu.ph/about/about-us)

`subjects.program_id` remains a separate optional association to a source-linked course list for that subject. Existing subject values are not evidence of an account-wide program and are not migrated or overwritten.

Subject entry follows the saved account program. When Duno has an NVSU subject list for that program, students can add only subjects selected from that list. For an NVSU program without a list and for a manually entered school/program, Duno shows a text field so the student can enter a subject name. Manually entered school/program choices do not claim or attach an NVSU curriculum. Subject list and search stay within the student's selected program, retaining legacy unassigned subjects while excluding rows tied to another NVSU curriculum.

Active study workflows follow the same scope: Focus, topic search and direct subject/topic routes, Plan generation and block actions, and Timer subject/topic choices accept only the selected curriculum plus legacy unassigned subjects. Existing saved sessions and Aralytics history remain available; changing the current program does not rewrite history.

## Acceptance checks

- An unselected student is redirected from every student workspace route to the selection screen; that screen remains available and can be submitted with CSRF protection.
- The server accepts only IDs in `AcademicProgramCatalog` for NVSU choices; manual mode validates school and program names and uses a reserved manual ID distinct from curriculum IDs. Both choices require the confirmation checkbox.
- The setup form lists Bayombong colleges first, filters program choices by the selected college, and excludes other campuses. The server validates both campus and college/program matching.
- The manual option switches the form to School (1–160 characters) and Program (1–180 characters); the server validates these values, saves them once, and does not treat free-text values as NVSU catalog IDs.
- The first valid selection is saved atomically only while `academic_program_selected_at` is null. Repeat direct GET/POST requests cannot replace it.
- A manually entered school/program has no attached NVSU curriculum; subject creation uses a typed subject name and the account's own subjects remain scoped to that account.
- Profile shows the saved school and title read-only; profile updates ignore forged academic program fields.
- Existing subjects, topics, and their curriculum references remain unchanged.
- A program with a sourced NVSU list requires a course ID from that program on create/update; a typed name or another program's course ID cannot bypass it.
- A program without a sourced NVSU list allows a trimmed 1–120 character subject name and rejects a forged course ID.
- Subject list/search include the selected curriculum and legacy subjects whose `program_id` is null, while excluding other curriculum IDs.
- Focus, topic search, direct subject/topic URLs, Plan topic choices and writes, and Timer form choices and submissions enforce the same curriculum scope, including legacy unassigned subjects.
- Existing saved sessions and Aralytics history remain owner-scoped and are not deleted or rewritten by program filtering.
- Admins and signed-out visitors do not gain access to student routes.

## Data and rollout

Migration `2026-11-01-000001_AddAcademicProgramToUsers` adds three nullable columns to Shield's `users` table. Nullable fields preserve old rows until each student chooses. The reviewed SQL in `docs/2026-11-01-academic-program-upgrade.sql` is retained as deployment evidence and must not be rerun against the hosted database. The upgrade adds no program to existing users and does not alter subject data.

Migration `2026-11-02-000002_AddAcademicSchoolNameToUsers` adds nullable `academic_school_name` for the school entered during manual selection and for the NVSU Bayombong choice. It preserves existing selections; students without the column's data receive the standard NVSU school label when their saved catalog program is shown. Apply the reviewed [`2026-11-02-academic-school-upgrade.sql`](2026-11-02-academic-school-upgrade.sql) once after a fresh backup and after confirming the column is absent. Never include this SQL file in the public FTP package.

Hosted migration verification: the owner confirmed a database backup was ready before the change. The preflight found no migration ledger row and none of the three columns. On 2026-11-01 the columns were added, and phpMyAdmin confirmed `academic_program_id` (`VARCHAR(80)`), `academic_program_title` (`VARCHAR(255)`), and `academic_program_selected_at` (`DATETIME`) are nullable. Migration ID 19/version `2026-11-01-000001`/batch 14 is recorded. This verifies the hosted schema only; the application code has not been uploaded or host-tested.

The existing local PHPUnit feature coverage exercises the gate, validation, immutable save, profile read-only display, forged profile input, program-specific subject selection, no-list manual entry, and cross-program rejection. The later active-workflow scope changes have only passed PHP syntax checks and `git diff --check`; their automated tests and browser review have not been run. Hosted runtime behavior remains unverified until the migration and app package are applied and checked with synthetic student accounts.
