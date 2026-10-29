# Story: study progression and unlockable Aralytics

**Requirement:** F-14 (Should) and F-09 (Must)
**State:** Prior progression accepted 2026-10-25; streak extension in Verification after rule approval on 2026-10-26.
**Owner decision:** Keep core Aralytics available from the start; unlock optional additional insights with XP. Add a once-per-app-local-calendar-day student visit reward and streak (2026-10-26).

## User story

As a student, I can see steady progress from the study time I choose to save, earn milestone badges, and unlock additional ways to review my activity.

## Product rules

- Earn 1 study XP for each minute in a saved study session. Editing or deleting sessions recalculates this study XP from the current saved records.
- Award 5 additional XP on the first authenticated student app visit for each app-local calendar day. A student visit on the next consecutive local date increments the streak; a visit after one or more missed dates starts a new one-day streak. Reopening or refreshing the app on the same date does not add XP.
- Store one owner-scoped activity row per local date with a unique owner/date key so simultaneous requests cannot award twice. Admin visits and private check-ins do not count.
- Levels are deterministic: Level 1 Fresh Start (0 XP), Level 2 Finding Your Rhythm (120 XP), Level 3 Building Momentum (300 XP), Level 4 Steady Scholar (600 XP), and Level 5 Long-Haul Learner (1,200 XP).
- If a student edits or deletes a session, XP and level are recalculated from the saved records. This keeps the display explainable and avoids a separate XP ledger.
- Core Aralytics stays available at every level. Additional owner-scoped insights unlock at these thresholds:
  - Level 2 / 120 XP: daily saved study minutes across the last 30 days.
  - Level 3 / 300 XP: saved minutes grouped by weekday across the last 30 days.
  - Level 4 / 600 XP: comparison of saved minutes in the latest and preceding 30-day periods.
  - Level 5 / 1,200 XP: saved minutes by calendar month for the latest six months.
- The dashboard shows current level, total XP with its study/streak sources, progress to the next level, unlocked insights, milestone badges, and the current streak with an animated fire icon. Reduced-motion preferences suppress the animation. Locked insight cards state the level and XP requirement.
- Study-time badges and Aralytics study-day counts are calculated only from saved study records: first saved session, 120 saved minutes, five distinct study dates, 600 saved minutes, and 1,200 saved minutes. Streak XP can unlock levels but does not unlock study-time badges by itself.
- Check-in values do not contribute to XP, levels, badges, or unlocks. Keep them private and separate from progression.
- Study progression and streaks use the signed-in student's owner-scoped session and daily-visit records. Do not include check-in values in progression, admin totals, or print output.
- Charts and comparisons are descriptive counts of saved activity. They do not predict performance or claim learning outcomes.
- A migration stores one daily visit record per student/date. No queue, cron, external service, or new runtime dependency is required.

## Acceptance criteria

1. The student dashboard and Aralytics show the same owner-scoped XP and level based on saved study minutes.
2. Saving, editing, or deleting a session updates displayed XP and unlock state from the current saved records.
3. Existing core Aralytics periods, totals, subject/topic breakdowns, and printable report remain available at every level.
4. Each bonus insight is rendered only when the signed-in student's current level meets its threshold; otherwise its card shows the unlock level and remaining XP.
5. The 30-day rhythm view has an accessible numeric day-by-day alternative; weekday and monthly charts expose labels and exact saved-minute values.
6. Period boundaries use the configured application timezone and every session used in a calculation belongs to the signed-in owner.
7. Check-ins have no effect on XP, levels, badges, or unlocks; private check-in values remain excluded from admin and print views.
8. Milestone badges reflect the saved owner-scoped data and display clear earned or in-progress status without relying on color alone.
9. Layouts remain usable by keyboard and at phone widths. Progress cards and bonus insights do not appear in the printable study report.
10. A first student visit awards exactly 5 XP on an app-local calendar date; same-day requests award no more. Consecutive calendar dates increase the streak, a missed date resets it, other students' visits do not affect it, and admin visits/check-ins award no streak XP.
11. The dashboard fire icon animates on a newly awarded streak day, has a useful text equivalent, and respects reduced motion.

## Streak extension verification (2026-10-26)

- PHPUnit passed: 76 tests and 704 assertions across the full suite. Focused checks include one award per app-local date, consecutive dates even across midnight, reset after a missed date, distinct owner records, admin exclusion, updated XP totals after session edits/deletes, and check-ins not earning streak XP.
- PHP syntax passed for 143 files under `app`, `tests`, and `scripts`; JavaScript syntax passed for the existing navigation and level-up modules. `git diff --check` passed. Composer validation passed; the locked audit reported no advisories, but Packagist was unreachable.
- Feature tests applied the new migration to in-memory SQLite and checked the rendered dashboard reward, flame icon, and student/admin route behavior. A temporary SQLite browser server ran all migrations, but Shield rejected the synthetic browser-review registration with its personal-information password rule. Per the owner's direction, authenticated browser review is deferred until the accumulated updates are uploaded to InfinityFree; the MariaDB setup also denied access and Playwright could not start a worker (`spawn EPERM`).
- The owner-approved migration is present locally but has not been applied to the isolated MariaDB review database or hosted database. The updated initial schema contains 16 tables and 10 migration rows for a new empty database; never import it into an existing database.
- No files were staged, committed, pushed, or deployed.

## Verification status

- PHP and JavaScript syntax checks and `git diff --check` are required before review.
- Focused unit/feature coverage passes for thresholds, unlocks, local period boundaries, six-month zero filling, session edits/deletes, check-ins earning no XP, and owner-scoped totals. Four targeted files passed: 25 tests and 311 assertions using the isolated in-memory SQLite test configuration.
- The dashboard and Aralytics were manually reviewed at phone and desktop widths at Level 1, plus Aralytics at Levels 2–5 using temporary synthetic sessions. All four unlock transitions and locked/unlocked card counts matched the thresholds at each level; all states fit 400px and 1280px without horizontal overflow. Temporary sessions were deleted through the app's confirmation flow, and the synthetic account was verified back at its original 1 XP / one-minute baseline. Screenshots are in ignored `.tools/ui-review-20261025/`. The owner accepted thresholds, level names, badge names, and unlock descriptions on 2026-10-25. Local review setup is recorded in [`database-setup.md`](database-setup.md).
- Owner accepted the thresholds, level names, badges, and unlock descriptions on 2026-10-25. Do not deploy until the project owner's separate release authorization and release checks are complete.
