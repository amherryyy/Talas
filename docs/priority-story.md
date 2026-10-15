# Story: explainable topic priority

**Status:** Accepted by the owner on 2026-10-16 after reviewing the priority page with synthetic sample topics. The scoring formula and tie rules were approved on 2026-10-15.

## User story

As a student, I can see my unfinished topics ranked by an understandable score so I can choose what to work on.

## Requirements

- F-06: show a rule-based priority list with a short explanation based on topic importance, due date, and progress.
- S-02: show only topics belonging to the authenticated student.
- F-13: keep the list readable and usable on mobile and desktop.

## Owner-approved scoring

- Include unfinished topics only. Exclude topics marked done.
- Importance contributes 1 point for low, 2 for medium, and 3 for high.
- Due date contributes 3 points when overdue or due today, 2 points when due within the next 7 days, 1 point when due later, and 0 points when no date is set.
- Progress contributes 1 point when not started and 0 when in progress.
- Total the components and sort highest score first.
- Break equal-score ties by nearest due date first, undated topics last, then title alphabetically. If score, due date, and title are identical, use the lower topic ID as a stable final order.
- Display the score and each component as a reason; do not imply that it predicts grades or learning outcomes.

## Acceptance criteria

1. The priority route requires a signed-in student; admins and signed-out visitors cannot view it.
2. Every topic query is scoped to the authenticated owner's subjects.
3. The list excludes done topics and shows an empty state when no unfinished topics remain.
4. Unit tests cover every score component, date boundaries, sorting, and the empty/done-only cases.
5. Feature tests cover owner isolation, role access, explanations, and completed-topic exclusion.
6. Playwright responsive checks include the page at 320, 400, 768, and 1280 CSS pixels.

## Verification record

- Implementation: owner-scoped model query, pure scoring service, student priority route and view.
- Automated checks: full PHPUnit suite passed (26 tests, 248 assertions); PHP syntax checks passed for changed PHP files; Composer audit passed with no advisories. Playwright passed both tests using a synthetic student account.
- Browser checks: the authenticated subject list, topic search/filter, and priority pages passed at 320, 400, 768, and 1280 CSS pixels, with no horizontal overflow. Owner visual review and acceptance remain pending.

## Verification update (2026-10-16)

- Full PHPUnit suite: passed, 26 tests and 248 assertions.
- PHP syntax checks: passed for all changed PHP files.
- Composer audit: attempted with `--locked`; could not connect to Packagist through the restricted network, so this run is unverified. The previously recorded 2026-10-15 audit reported no advisories.
- Browser automation: Playwright could not start its worker (`spawn EPERM`). No synthetic account credentials were configured, and the available browser-control inventory was empty, so the authenticated visual check could not be repeated.
- Manual review URL: the local server returned HTTP 500 because the isolated Talas MariaDB instance could not start; MariaDB reported its `mysql.db` privilege table corrupt. No repair or reinitialization was attempted.
- At the time of this verification update, owner visual review and acceptance were pending; visual acceptance was recorded below after review.

## Temporary review environment (2026-10-16)

- A separate disposable MariaDB instance is running at `127.0.0.1:3308` under ignored `.tools/`; it does not use or modify the original `data-talas` directory or `.env`.
- All application, Shield, and Settings migrations are applied. A synthetic student account, one subject, and four sample topics were created for review.
- The login and registration pages return HTTP 200; the unauthenticated priority route redirects to login (302). Using the synthetic registration session, the priority route returned HTTP 200, displayed three unfinished topics, and excluded the completed topic.
- The local review page is available at `http://127.0.0.1:8080/student/priority`.
- Owner visual acceptance: accepted on 2026-10-16.
