# One current academic program per student account

**Status:** Owner-approved 2026-11-01; hosted schema migration applied and verified 2026-11-01; app package and runtime acceptance remain pending.

## Decision

Each student account stores one current academic program. After sign-in, a student without a saved selection chooses a college and then a program offered at NVSU Bayombong. The program list is grouped by college and shows only the program title, avoiding repeated campus and college text. The student confirms it is current. The selection is immutable through ordinary app routes and is shown read-only in Profile. A later change is handled by the project administrator.

`subjects.program_id` remains a separate optional association to a source-linked course list for that subject. Existing subject values are not evidence of an account-wide program and are not migrated or overwritten.

Subject entry follows the saved account program. When Duno has an NVSU subject list for that program, students can add only subjects selected from that list. When no list is available, Duno shows a text field so the student can enter a subject name. Subject list and search stay within the student's selected program, retaining legacy unassigned subjects while excluding rows tied to another NVSU curriculum.

Active study workflows follow the same scope: Focus, topic search and direct subject/topic routes, Plan generation and block actions, and Timer subject/topic choices accept only the selected curriculum plus legacy unassigned subjects. Existing saved sessions and Aralytics history remain available; changing the current program does not rewrite history.

## Acceptance checks

- An unselected student is redirected from every student workspace route to the selection screen; that screen remains available and can be submitted with CSRF protection.
- The server accepts only IDs in `AcademicProgramCatalog` and requires the confirmation checkbox.
- The setup form lists Bayombong colleges first, filters program choices by the selected college, and excludes other campuses. The server validates both campus and college/program matching.
- The first valid selection is saved atomically only while `academic_program_selected_at` is null. Repeat direct GET/POST requests cannot replace it.
- Profile shows the saved title read-only; profile updates ignore forged academic program fields.
- Existing subjects, topics, and their curriculum references remain unchanged.
- A program with a sourced NVSU list requires a course ID from that program on create/update; a typed name or another program's course ID cannot bypass it.
- A program without a sourced NVSU list allows a trimmed 1–120 character subject name and rejects a forged course ID.
- Subject list/search include the selected curriculum and legacy subjects whose `program_id` is null, while excluding other curriculum IDs.
- Focus, topic search, direct subject/topic URLs, Plan topic choices and writes, and Timer form choices and submissions enforce the same curriculum scope, including legacy unassigned subjects.
- Existing saved sessions and Aralytics history remain owner-scoped and are not deleted or rewritten by program filtering.
- Admins and signed-out visitors do not gain access to student routes.

## Data and rollout

Migration `2026-11-01-000001_AddAcademicProgramToUsers` adds three nullable columns to Shield's `users` table. Nullable fields preserve old rows until each student chooses. The reviewed SQL in `docs/2026-11-01-academic-program-upgrade.sql` is retained as deployment evidence and must not be rerun against the hosted database. The upgrade adds no program to existing users and does not alter subject data.

Hosted migration verification: the owner confirmed a database backup was ready before the change. The preflight found no migration ledger row and none of the three columns. On 2026-11-01 the columns were added, and phpMyAdmin confirmed `academic_program_id` (`VARCHAR(80)`), `academic_program_title` (`VARCHAR(255)`), and `academic_program_selected_at` (`DATETIME`) are nullable. Migration ID 19/version `2026-11-01-000001`/batch 14 is recorded. This verifies the hosted schema only; the application code has not been uploaded or host-tested.

The existing local PHPUnit feature coverage exercises the gate, validation, immutable save, profile read-only display, forged profile input, program-specific subject selection, no-list manual entry, and cross-program rejection. The later active-workflow scope changes have only passed PHP syntax checks and `git diff --check`; their automated tests and browser review have not been run. Hosted runtime behavior remains unverified until the migration and app package are applied and checked with synthetic student accounts.
