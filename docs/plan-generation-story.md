# Story: daily plan generation

**Requirement:** F-07
**Status:** Core plan generation was accepted by the owner on 2026-10-16. The owner reports the authenticated Plan journey review is complete with no current issue on 2026-10-25. The checklist-card follow-up was requested on 2026-10-29. The mobile agenda follow-up was approved on 2026-10-29; its tests and browser checks have not yet been run.

## User story

As a student, I can enter the study time I have today, get a plan that fits, and adjust it to suit my needs.

## Fixed requirements

- The plan must never exceed the student's entered available time.
- Validate time and referenced records on the server.
- Read and write only records owned by the signed-in student.
- Keep the rules deterministic and explainable; do not add recommendations or claims about learning outcomes.
- The student can adjust the generated plan.

## Approved MVP rules

1. The student enters available minutes for the selected local date (15–480 inclusive); the page opens on today.
2. Build the plan from unfinished topics, using the accepted F-06 priority order.
3. Give each topic a 25-minute block in that order; shorten the last block to use the remaining time. Do not repeat a topic in the generated plan. If no unfinished topics exist, show an empty state.
4. Let the student remove a block, reorder blocks, or change a block's duration. Keep total planned time at or below available time; show remaining unplanned minutes.
5. Save one plan per student per local calendar date, so edits survive navigation and refresh. Regenerating replaces that day's plan after the student confirms.
6. On the Plan page, show a checklist progress card for that day's blocks. Checking a block marks its owner-scoped topic `done`; unchecking returns it to `in_progress`. This reuses topic progress and adds no plan-item completion column.
7. On phones, provide a week-strip agenda for the selected date. Day navigation reads and writes the same owner-scoped daily plans; it does not replace or fork the desktop planner.
8. A planned block may have a nullable start time. A chosen start time on plan generation lays out its generated blocks sequentially by duration. Existing blocks without a time stay visibly unscheduled until the student schedules them.
9. From the mobile add sheet, a student may add one of their unfinished topics or assign a start time to an existing unscheduled block. Added blocks must fit the day's available-minute cap and may not overlap another scheduled block. The existing topic uniqueness and owner-scope rules remain in force; arbitrary meetings, locations, attendees, and non-study calendar events are outside Duno Plan.
10. The current-time marker appears only for the selected app-local date that is today. It is positioned relative to scheduled blocks; older blocks without a start time are grouped as unscheduled.

The checklist card presentation adapts the progress-card snippet supplied by the owner from BrewedOps Motion Library. Duno's card uses current saved plan topics and existing topic-progress values rather than sample lesson content.

The owner approved the core plan defaults on 2026-10-16. The progress-card follow-up was requested on 2026-10-29 and uses the existing topic status values.

The owner approved the mobile agenda adaptation on 2026-10-29. It uses `study_plan_items.start_time` for real scheduled blocks and keeps that field nullable so existing plans retain their meaning. The database migration adds only this nullable column. The owner applied the one-time hosted upgrade [`2026-10-29-plan-agenda-upgrade.sql`](2026-10-29-plan-agenda-upgrade.sql) and confirmed migration 16/batch 11 and the column; existing rows were not assigned invented times.

## Acceptance checks and evidence

- Generated duration is within the submitted available time, including zero-topic and one-topic cases.
- Reordering, removing, and duration edits remain within the time cap and persist according to the approved lifecycle.
- Invalid durations and topic IDs are rejected server-side.
- Cross-student plan and topic reads/writes are denied, including direct requests.
- A progress-card change updates only the signed-in student's topic referenced by today's owned plan item; invalid or unavailable items are rejected.
- The phone agenda can move across weeks and dates, and shows each selected day's owner-scoped blocks; desktop generation and editing continue to use today's plan.
- A mobile add rejects a topic from another student, duplicate topics, over-capacity durations, invalid times, and overlaps. Previously unscheduled blocks can be scheduled without changing their topic or duration.
- Mobile topic progress uses the same saved `done`/`in_progress` status as the desktop checklist; the now line appears only on the app-local current date.
- With JavaScript enabled, the progress bar and completion badge update in place. With JavaScript disabled, the normal CSRF-protected form still updates the topic and reloads the Plan page.
- Routes require student authorization and state changes require CSRF.
- Keyboard and narrow-screen browser review covers generation and adjustments. The owner reports the authenticated Plan journey review is complete with no current issue on 2026-10-25. Playwright previously could not start a worker (`spawn EPERM`); no automated browser matrix was run for that attempt.
