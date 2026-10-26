# Story: first-visit student onboarding tour

**Requirement:** F-13
**State:** Implemented locally; browser review deferred until the accumulated update batch is uploaded to InfinityFree, per owner direction.
**Owner decisions:** Show a suggestive tour the first time a student enters the app; allow students who already know Duno to skip it; remember completion in the current browser only (2026-10-26).

## User story

As a new student, I can get a quick introduction to Duno's existing tools, skip it when I already know the app, and open it again later if I need a reminder.

## Product rules

- Automatically offer the tour on the first authenticated student app page in the current browser, scoped separately to the signed-in student. Do not show it to guests or admins.
- Explain the existing start page, Duno Focus, Duno Plan, Duno Timer, Aralytics, Duno Guide, and the More menu's Subjects, Topics, and private check-ins.
- Let students move forward/back, skip, or finish. Skipping and finishing both suppress automatic starts in that browser. Keep “Show app tour” available from the More menu so the tour can be replayed.
- Use local browser storage only; no profile field, migration, analytics, external service, or cross-device synchronization is added.
- If the daily optional check-in dialog is due, wait until it closes before opening the tour. Keep the tour non-blocking, keyboard operable, screen-reader labeled, and reduced-motion aware.
- Use existing navigation targets and feature descriptions; do not change study calculations, saved data, privacy, or account permissions.

## Acceptance criteria

1. The tour appears automatically once for an authenticated student in a browser, including when the first route is a secondary student page.
2. The sequence describes the start space, Focus, Plan, Timer, Aralytics, Guide, and More and highlights the matching visible card or navigation item.
3. Back, Next, Skip, Finish, keyboard focus, and Escape work without hiding or trapping the underlying app.
4. Skip and Finish persist per student in browser storage. “Show app tour” starts it again even after completion.
5. The tour waits for a due daily check-in prompt to close, so the two dialogs do not compete.
6. The tour has no effect for guest or admin users and adds no runtime dependency or schema change.
7. Reduced-motion preferences suppress scrolling and entrance animation. The tour remains usable at phone and desktop widths.

## Verification

- PHP syntax passed for the changed controllers/views/partials. JavaScript syntax passed for the tour and check-in prompt modules. `git diff --check` passed.
- The full PHPUnit suite passed: 77 tests and 717 assertions. Feature coverage confirms the account-scoped tour appears on the student dashboard/start page and stays absent for guests and admins.
- Per the owner, visual browser review is deferred until the accumulated updates are uploaded to InfinityFree.
- No upload, deployment, database migration, staging, or commit was performed for this task.
