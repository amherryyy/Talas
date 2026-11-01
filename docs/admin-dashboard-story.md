# F-12 admin dashboard analytics

**Requirement:** F-12 (Should)
**Status:** Five aggregate totals accepted for the local milestone on 2026-10-17. The owner approved the additional metrics and charts on 2026-11-01. Current local implementation is not yet deployed or verified against the hosted database.

## Approved scope

The server-rendered admin dashboard presents system-wide student aggregates:

- All-time student account, subject, topic, saved study-session, and saved study-minute totals.
- Current saved daily-plan row count. Plans are replaced in place, so this is the number of plan records currently saved, not a count of plan-generation actions.
- Mean, median, and middle-50% range (25th to 75th percentile) of saved session lengths, shown only when at least five distinct students contributed. These describe recorded session lengths, not time spent in the app, study mastery, or total study time.
- Recorded student visit days over the last 30 application-local calendar dates, including today. These are rows in `student_streak_days`; they are not page views or a full feature-usage measure.
- Program-selection completion percentage, derived from `users.academic_program_selected_at`. It is available only after the academic-program schema migration is applied.
- Weekly recorded visit-day counts and distinct active-student counts across the last eight complete application-local weeks. A week is shown only when at least five distinct students contributed; both series use the same withheld weeks.
- New student-account counts by month for the last six months, including the current month to date.
- Current topic-status snapshot: for each status, the percentage of students with at least one topic who currently have at least one topic in that status. A student can appear in multiple rows. A status percentage is shown only when at least five students contribute; this is self-reported current state, not learning progress or an assessment.
- Workspace setup coverage: the percentage and count of student accounts with at least one saved subject, topic, plan, or study session. These are independent saved-record milestones, not a chronological funnel. A milestone is shown only when at least five students have it and at least five do not.
- Planning patterns for the last 30 application-local calendar dates: students with a saved plan, median plan-days per planner, median planned minutes and blocks per plan-day, and median share of available time allocated. First calculate each student's own average, then give each student equal weight in the displayed medians. These are saved plans, not completed study time or plan adherence. Results require at least five planners and five non-planners.
- Upcoming workload snapshot: among students with unfinished topics, the percentage who have at least one overdue, due-within-seven-days, later-due, or undated unfinished topic. The groups overlap because one student can have topics in multiple date groups. Each percentage requires at least five students with and without that date group; this is a current snapshot, not a forecast.

The two charts are inline SVG with readable axes, accessible title/description, and an expandable numeric table. They use the existing design tokens and CSS; no chart library or runtime dependency is introduced. A print action opens the browser's print dialog with a paper layout that omits navigation and screen controls while preserving aggregate metrics, chart data tables, privacy notes, and withheld-value labels.

## Privacy and calculation rules

- Student-group accounts are included; any account also in the admin group is excluded from every student aggregate.
- Daily visit, registration, weekly activity, and saved-session summaries with fewer than five contributing students are withheld. A period with no registrations is shown as zero; a period with one to four registrations is hidden.
- The topic-status snapshot is system-wide and contains no student rows or identities. Each status is withheld when fewer than five students contribute; the note explains that statuses overlap and are a current snapshot.
- Workspace coverage uses saved records only and withholds a milestone unless both the contributing and non-contributing groups have at least five students.
- Planning metrics use the last 30 application-local calendar dates and are calculated per student before aggregation, preventing students with many plan days from dominating the median. The planning summary is withheld unless both planner and non-planner groups contain at least five students.
- Due-date workload uses unfinished topics only; each date bucket is based on distinct student contributors, suppressed unless at least five students have and five do not have a topic in that date group, and explicitly labeled as overlapping.
- Program-selection completion is shown only when the student population is at least five and both the selected and pending groups each contain at least five accounts. No program-level breakdown is available.
- The dashboard sends aggregate values only. It does not show names, emails, account-level rows, individual study records, program breakdowns, exports, or drill-down links.
- Optional workload and energy check-ins remain private and are excluded from all admin calculations.
- No page-view, feature-adoption, error, plan-completion, or adherence instrumentation is collected. Plan items do not store a completion event, so completion rates cannot be reconstructed reliably.
- Topic progress is stored as current state only. No transition history is collected, so the dashboard cannot show topic completion rates over time or infer learning outcomes.

## Acceptance criteria

1. Only an authenticated admin with `admin.access` can load `/admin`; student and signed-out requests remain denied.
2. Counts and calculations are performed server-side from student-owned records; the view receives aggregates only.
3. Accounts also assigned to the admin group are excluded.
4. Trend bins apply the five-student minimum and clearly label withheld values; the onboarding percentage applies the minimum to both completion groups.
5. Charts have accessible names/descriptions and a tabular data equivalent; the dashboard is responsive and sign-out remains CSRF-protected.
6. Print output contains the same aggregate-only metrics, chart tables, privacy notes, and suppression labels, with navigation and interactive controls omitted.
7. Setup and planning values use the stated cohort minimums and date window; planning medians give each student equal weight.
8. No student identities, record details, private check-ins, downloadable exports, or drill-down paths are exposed.

## Verification record

- The original five-total implementation passed focused and full PHPUnit runs in October 2026; see the historical entry in [`ai-use-log.md`](ai-use-log.md).
- For the expanded implementation, the focused admin dashboard suite passed 4 tests and 52 assertions; the full PHPUnit suite passed 109 tests and 1,121 assertions using SQLite. Both runs disabled coverage because no code-coverage driver is installed.
- PHP syntax checks passed for the service, dashboard, chart partial, and feature test. `git diff --check` passed; Git emitted only its existing LF-to-CRLF conversion notices.
- On 2026-11-01, the owner approved descriptive weekly active-student counts, session median and middle-50% range, and the current topic-status snapshot. The local implementation uses existing saved data, applies minimum-group withholding, and adds no event collection. PHP syntax checks and `git diff --check` passed; browser rendering and automated tests were not run in this turn.
- On 2026-11-01, the owner requested a print feature. The dashboard now opens the browser print dialog and uses print-specific styling to hide navigation and controls, keep the aggregate charts and numeric tables visible, and include the privacy note and snapshot timestamp. Syntax and diff checks passed; print preview was not visually reviewed.
- On 2026-11-01, the owner requested additional in-scope analytics. The dashboard now includes saved-record setup coverage, per-student planning medians from the last 30 days, and an overlapping unfinished-topic due-date snapshot. These use existing data only and apply five-student suppression. PHP syntax and diff checks passed; automated and browser checks were not run.
- Rendered browser review remains pending because the browser access host was unavailable. Hosted schema/runtime checks are also pending.
- The onboarding completion metric remains unavailable on any database where the academic-program migration has not been applied. No host data or files were changed as part of this story.
- Nothing has been staged, committed, pushed, merged, or deployed.
