# F-11 private workload and energy check-ins

**Requirement:** F-11 (Should)
**Status:** Owner accepted for the local development milestone on 2026-10-17; integrated on `main` at `c83f664` as of 2026-10-25. Hosted release verification remains pending.
**Owner decision:** Approved on 2026-10-17.

## Approved product behavior

- A student may optionally record both a workload rating and an energy rating.
- Each rating is a whole number from 1 to 5. Workload uses 1 for very light and 5 for very heavy; energy uses 1 for very low and 5 for very high.
- A student may save multiple check-ins on the same day. The server records the current application-local date and time; students cannot edit the timestamp.
- Students may edit the two ratings or permanently delete a saved check-in after a confirmation step.
- Entries remain stored until the student deletes them. There is no automatic expiry.
- No free-text notes are collected.
- The student sees a private summary of check-ins from the last 7 application-local calendar days. It counts entries where workload is rated 4–5 and energy is rated 1–2, without interpreting the ratings or changing the study plan.
- Aralytics may show separate private counts for the selected period: high workload (4–5), low energy (1–2), and entries where both occur together. This is deterministic counting, not model training, assessment, prediction, or plan adjustment.

## Privacy and security

- Check-ins belong to one student and are visible only through that student's authorized routes. Every read and write is scoped to the authenticated owner.
- Individual check-in values and timestamps are excluded from admin views, admin aggregates, search, printed reports, and exports. Aralytics may display owner-scoped aggregate counts on screen only.
- Use session and student-role route filters, CSRF-protected POST writes, framework validation, and escaped rendered values.
- Do not log check-in values or use real student check-ins in development or verification.
- These ratings are optional self-reported context, not a health assessment or a claim about grades or learning outcomes.

## Acceptance criteria

1. A student can save a check-in with both ratings in the range 1–5; multiple entries can be saved on the same date.
2. The student sees only their own entries, with recorded application-local timestamps and both ratings.
3. The owner can edit ratings without changing the recorded timestamp and can delete an entry after confirmation.
4. Invalid and missing ratings are rejected server-side; an unconfirmed deletion does not remove the entry.
5. Signed-out and admin users cannot access student check-in routes; one student cannot read, edit, or delete another student's entry.
6. Individual check-in values do not appear in admin pages, topic search, Aralytics, or print output. The separate private Aralytics section shows only aggregate counts for the signed-in owner and selected period; print output excludes it.
7. Responsive and keyboard checks cover the check-in form, saved list, edit form, and delete confirmation.
8. The owner sees a private last-7-days summary with the total check-in count and the count of entries rated high workload (4–5) and low energy (1–2); empty periods are explained. The summary is descriptive, owner-scoped, and absent from admin, search, print, and export views.
9. Student Aralytics shows owner-scoped counts for high workload, low energy, and their co-occurrence in the selected period. It shows an empty state when no check-ins exist and never changes the study plan.
10. Student Aralytics shows paired workload/energy bars for rating counts 1–5 in the selected period, with a matching numeric table for accessibility. The responsive chart and table are omitted from print, admin, search, and exports.

## Verification record

- PHPUnit: passed, 56 tests and 500 assertions with coverage disabled.
- PHP syntax: all changed PHP files passed `php -l`. JavaScript syntax: `tests/e2e/responsive.spec.js` passed `node --check`.
- Dependency audit: `composer audit --locked` found no advisories. Composer proceeded without its unwritable cache after clearing injected proxy variables for that process. No separate PHP style tool is configured.
- Browser: Playwright passed 2/2 tests using the synthetic local student account. Check-in pages fit 320, 400, 768, and 1280 px widths; browser CRUD completed and removed its synthetic entry; the rating controls passed a keyboard-focus check. Mobile (390 px) and desktop (1280 px) screenshots were visually reviewed.
- Authorization/privacy: feature tests cover invalid ratings, CSRF, signed-out/admin denial, cross-owner read/write denial, edit/delete confirmation, and exclusion of raw check-in values from admin, topic search, and printed reports. Follow-up checks also verify owner-scoped aggregates and chart counts; Playwright confirms the private chart and table are excluded from print.
- Database: `CreateCheckIns` migration applied to the isolated local `talas_review` database; `migrate:status --all` reports batch 4. No production deployment or hosted database check was performed.
- Follow-up verification of the accepted weekly summary and Aralytics chart: focused PHPUnit passed (11 tests, 107 assertions), including recent-period filtering, owner scoping, weekly co-occurrence count, and exact chart-table distributions. Playwright passed 2/2 tests with the synthetic local student account; it verified the chart and numeric table at 400px and 1280px, then confirmed print hides both while retaining the session report. Hosted behavior and production release verification remain outstanding.

No production deployment is included.
