# Story: daily plan generation

**Requirement:** F-07
**Status:** Core plan generation was accepted by the owner on 2026-10-16. The owner reports the authenticated Plan journey review is complete with no current issue on 2026-10-25. The checklist-card follow-up was requested on 2026-10-29; automated and browser checks for that change have not been run.

## User story

As a student, I can enter the study time I have today, get a plan that fits, and adjust it to suit my needs.

## Fixed requirements

- The plan must never exceed the student's entered available time.
- Validate time and referenced records on the server.
- Read and write only records owned by the signed-in student.
- Keep the rules deterministic and explainable; do not add recommendations or claims about learning outcomes.
- The student can adjust the generated plan.

## Approved MVP rules

1. The student enters available minutes for today (15–480 inclusive).
2. Build the plan from unfinished topics, using the accepted F-06 priority order.
3. Give each topic a 25-minute block in that order; shorten the last block to use the remaining time. Do not repeat a topic in the generated plan. If no unfinished topics exist, show an empty state.
4. Let the student remove a block, reorder blocks, or change a block's duration. Keep total planned time at or below available time; show remaining unplanned minutes.
5. Save one plan per student per local calendar date, so edits survive navigation and refresh. Regenerating replaces that day's plan after the student confirms.
6. On the Plan page, show a checklist progress card for that day's blocks. Checking a block marks its owner-scoped topic `done`; unchecking returns it to `in_progress`. This reuses topic progress and adds no plan-item completion column.

The checklist card presentation adapts the progress-card snippet supplied by the owner from BrewedOps Motion Library. Duno's card uses current saved plan topics and existing topic-progress values rather than sample lesson content.

The owner approved the core plan defaults on 2026-10-16. The progress-card follow-up was requested on 2026-10-29 and uses the existing topic status values.

## Acceptance checks and evidence

- Generated duration is within the submitted available time, including zero-topic and one-topic cases.
- Reordering, removing, and duration edits remain within the time cap and persist according to the approved lifecycle.
- Invalid durations and topic IDs are rejected server-side.
- Cross-student plan and topic reads/writes are denied, including direct requests.
- A progress-card change updates only the signed-in student's topic referenced by today's owned plan item; invalid or unavailable items are rejected.
- With JavaScript enabled, the progress bar and completion badge update in place. With JavaScript disabled, the normal CSRF-protected form still updates the topic and reloads the Plan page.
- Routes require student authorization and state changes require CSRF.
- Keyboard and narrow-screen browser review covers generation and adjustments. The owner reports the authenticated Plan journey review is complete with no current issue on 2026-10-25. Playwright previously could not start a worker (`spawn EPERM`); no automated browser matrix was run for that attempt.
