# Story: student subject management

**Status:** Accepted by the project owner on 2026-10-15 on `feature/F-03-subject-management`. The owner approved the proposed product defaults by replying `+` on 2026-10-15. All migrations are applied to the isolated local MySQL-compatible database. The full PHPUnit suite passes, including SQLite-backed subject tests. The owner accepted the documented browser-review limitation: narrow layout was approximated with browser zoom; a phone viewport was not separately emulated.

## Story

As a student, I can manage my own subjects so I can organize the topics and study work I will add later.

## Requirements

- `F-03`: create, view, edit, and delete a student's own subjects.
- `S-02`: every record read and write is scoped to the authenticated owner.
- `S-03`: validate input on the server, escape rendered values, and use parameterized database access.
- `S-04`: state-changing forms use CSRF protection.

## Acceptance criteria

1. An authenticated student can list and view their subjects.
2. A student can create a subject and edit or delete a subject they own.
3. Every query and mutation includes the authenticated owner scope.
4. Guessing another student's subject ID cannot show or change that subject; the response does not disclose whether a foreign subject exists.
5. Invalid input is rejected server-side, displayed values are escaped, and create/update/delete forms are CSRF-protected.
6. A signed-out user and an admin cannot use student subject routes.
7. Automated checks cover the permitted student flow and denied signed-out, admin, and cross-owner cases before the story is accepted.

## Boundaries

- Use CodeIgniter migrations and framework validation/model/query facilities.
- Do not add topic management, search/filter, priority logic, admin access, or real student data in this slice.
- Keep database credentials and local environment files out of source control.
- Database-backed behavior must be verified against an isolated synthetic test database; never target the hosted database.

## Confirmed product details

Owner-approved defaults:

- A subject has one required name; no description or other fields in this slice.
- Duplicate subject names are allowed for the same student.
- Deletion is blocked while a subject has topics, rather than cascading deletion and erasing related work. The future topics migration must use a restrictive foreign key as the race-safe backstop; the UI explains that the student must move or remove those topics first.

The PHPUnit configuration uses an in-memory SQLite test connection, but no subject schema existed when this story started; check that migration and foreign-key behavior match MySQL/MariaDB as well.

## Verification plan

- Migration applied successfully to the isolated local MySQL/MariaDB environment; the automated suite also applies the app migrations to its in-memory SQLite test database.
- Feature coverage for create/list/view/edit/delete, validation, CSRF, role denial, and two-owner isolation; full suite passed (11 tests, 56 assertions).
- PHP syntax checks and dependency audit: results recorded in the AI-use log and final task handoff.
- Manual browser review covered the subject list, add form, and subject detail at desktop size; browser zoom to 250% was used as a narrow-width approximation, with keyboard navigation and visible focus checks on the list and add form. A phone device viewport was not separately emulated.

The owner accepted this story on 2026-10-15 with the browser-review limitation recorded above.
