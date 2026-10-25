# F-12 admin dashboard aggregates

**Requirement:** F-12 (Should)
**Status:** Accepted by the owner for the local milestone on 2026-10-17; integrated on `main` at `c83f664` as of 2026-10-25. Hosted deployment checks remain pending.
**Owner decision:** The owner approved the recommended aggregate cards on 2026-10-17.

## Approved scope

The admin dashboard displays five totals across student accounts:

- Number of student accounts.
- Number of subjects.
- Number of topics.
- Number of saved study sessions.
- Total minutes in saved study sessions.

The counts include records owned by student-group accounts. Accounts also assigned to the admin group are excluded, matching the exclusive admin route policy. The dashboard does not show names, emails, per-student rows, record details, check-in values or patterns, charts, exports, search, or drill-down links. It has no date filter.

## Acceptance criteria

1. Only an authenticated admin with `admin.access` can load `/admin`; student and signed-out requests remain denied.
2. Each card shows the aggregate total for student accounts and their subjects, topics, saved study sessions, or saved study minutes.
3. The server calculates each aggregate from database queries; no identity or record-level data is sent to the view.
4. Admin-group accounts are excluded from the student account and activity totals, including accounts assigned to both groups.
5. No individual names, emails, study details, or private check-in entries/patterns appear; there are no drill-down paths.
6. The dashboard remains readable at mobile and desktop widths, exposes descriptive card labels to assistive technology, and keeps sign-out CSRF-protected. On narrow phone screens, the five cards use a compact two-column grid to reduce vertical scrolling.

## Verification record

- Focused admin dashboard feature tests passed: 3 tests and 27 assertions. The full PHPUnit suite passed: 52 tests and 458 assertions, using in-memory SQLite. PHPUnit reported no code-coverage driver; coverage was disabled for the passing full-suite run.
- PHP syntax checks passed for the admin controller, aggregate service, feature test, and dashboard view. No separate PHP style tool is configured in the repository. `git diff --check` passed.
- `composer audit --locked` reported no security advisories. Composer proceeded without its unwritable local cache after clearing injected proxy variables for the audit process.
- Playwright passed the student responsive journey at 320, 400, 768, and 1280 px, including a 403 when the student requested `/admin`.
- The owner signed into the local admin account and viewed `/index.php/admin` in Brave at an emulated 440 CSS-pixel phone width. The page showed all five aggregate cards in the compact two-column layout and the privacy note; no names, account identities, or individual records were displayed.
- No schema changes or hosted database/deployment checks were made.
- The owner reported excessive scrolling on narrow screens, then reviewed the two-column layout and confirmed it is better before accepting F-12 for the local milestone. Hosted deployment checks remain pending.
