# Story: daily plan generation

**Requirement:** F-07
**Status:** Accepted by the owner on 2026-10-16. Automated verification passed; manual browser review remains unverified because browser control could not identify the active tab URL.

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

The owner approved these defaults on 2026-10-16. F-07 implementation follows them.

## Acceptance checks and evidence

- Generated duration is within the submitted available time, including zero-topic and one-topic cases.
- Reordering, removing, and duration edits remain within the time cap and persist according to the approved lifecycle.
- Invalid durations and topic IDs are rejected server-side.
- Cross-student plan and topic reads/writes are denied, including direct requests.
- Routes require student authorization and state changes require CSRF.
- Keyboard and narrow-screen browser review covers generation and adjustments. This remains unverified: Playwright could not start a worker (`spawn EPERM`), and computer-use control stopped at its browser URL safety check.
