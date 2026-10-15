# Story: student topic management

**Status:** Accepted by the owner on 2026-10-16 after implementation verification and the previously recorded responsive and keyboard review. F-03 is integrated into `main` through PR #1. The owner approved the visual UI on 2026-10-15 after review in Edge responsive mode at 400 x 648 and confirmed the keyboard review passed. The owner confirmed the product rules below.

## Story

As a student, I can manage topics under my subjects so I can track the work I plan to study.

## Requirements

- `F-04`: create and manage topics under a student's subjects, including optional due date, importance, and progress.
- `S-02`: every topic and parent-subject read or write is scoped to the authenticated owner.
- `S-03`: validate input on the server, escape rendered values, and use parameterized database access.
- `S-04`: state-changing forms use CSRF protection.
- `F-13`: responsive browser experience with keyboard support.

## Confirmed product details

- A topic has a required title up to 120 characters. Duplicate titles under one subject are allowed.
- Due date is optional and date-only; past dates are allowed so overdue topics can be represented.
- Importance uses `low`, `medium`, or `high`.
- Progress uses `not_started`, `in_progress`, or `done`.
- A topic cannot be deleted while a saved study session references it. The future session migration must use a restrictive foreign key as the race-safe backstop, and the UI explains the restriction.
- The current topics migration restricts deletion of a subject while topics reference it. When the study-session schema is introduced, it must use a restrictive foreign key from its optional `topic_id` to `topics.id`; the application also checks the `study_sessions.topic_id` reference before deletion.
- New topics default to `medium` importance and `not_started` progress. Both values remain editable.

## Acceptance criteria

1. An authenticated student can list topics within their own subject and view each topic.
2. A student can create, edit, and delete a topic under a subject they own.
3. Every query and mutation scopes the topic through its parent subject and authenticated owner; a guessed foreign subject/topic ID cannot disclose or change another student's data.
4. Topic titles, optional date-only due dates, importance values, and progress values are validated server-side; rendered titles are escaped.
5. Create, update, and delete requests require CSRF protection. Signed-out users and admins cannot use student topic routes.
6. Deletion is blocked while saved sessions reference the topic; once sessions are implemented, the database constraint also prevents accidental cascade deletion.
7. Tests cover CRUD, invalid fields, CSRF, signed-out/admin denial, cross-owner subject/topic references, and the session-reference deletion guard.
8. Manual browser review covers desktop and narrow layouts plus keyboard/focus behavior. Any device-emulation or hosting limitation is recorded.

## Boundaries

- Do not add search/filter (F-05), priority calculations (F-06), planning, timer/session management, analytics, suggestions, or check-ins in this story.
- Keep topic records private to the owning student; use only synthetic data in development and tests.
- Implement through CodeIgniter migrations, framework validation, CSRF, escaping, and owner-scoped model/query patterns.

## Verification plan

- Apply the migration to the isolated local MySQL/MariaDB database and in-memory SQLite test database; verify the optional SQL `DATE` field and server-side enum-value validation.
- Run the relevant feature suite, PHP syntax/style checks, dependency audit, and `git diff --check`.
- Verify direct URLs and cross-owner subject/topic IDs with two synthetic students.
- Review the topic list and forms at desktop/narrow widths and with keyboard navigation.

F-03 was integrated into `main` through PR #1 before F-04 implementation began on its own story branch.

## Verification update (2026-10-16)

- Full PHPUnit suite: passed, 26 tests and 248 assertions, including topic validation, owner-scoped access, and priority rules.
- PHP syntax checks: passed for all changed PHP files.
- `git diff --check`: passed; Git emitted only the existing LF-to-CRLF notices.
- Composer audit: attempted with `--locked`; could not connect to Packagist through the restricted network, so this run is unverified.
- Browser: Playwright could not start its worker (`spawn EPERM`). The available browser-control inventory was empty, so no direct manual browser session was possible in this environment. The owner's previously recorded narrow-screen and keyboard review remains the available manual evidence.
- Migration application: the isolated local MySQL/MariaDB migration was already recorded as applied; it was not reapplied during this verification pass.
