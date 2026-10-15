# Story: subject and topic search and filtering

**Status:** Accepted by the owner on 2026-10-16 after implementation verification and the previously recorded keyboard and desktop/narrow-screen review.

## Story

As a student, I can find my subjects and topics and narrow topic results using their saved fields.

## Requirements

- `F-05`: search and filter a student's own subjects and topics.
- `S-02`: results, filter options, and counts are scoped to the authenticated owner.
- `S-03`: search values use framework query building and rendered values are escaped.
- `S-04`: search/filter forms use CSRF protection.
- `F-13`: controls remain usable on responsive and keyboard-accessible pages.

## Confirmed product details

- Search subject names on the subject list.
- Search topic titles and their parent subject names on a global topic list.
- Topic filters include subject, importance, progress, and due-date state.
- Search and filter forms use POST so search terms are not placed in URLs; CSRF remains enabled.
- A topic is overdue when its date is before the app's current date and it is not done. Upcoming means today or later and not done. Undated means no date. Done topics appear in unfiltered results and the undated filter, but not upcoming or overdue results.
- Filter counts show only the current student's matching records.

## Acceptance criteria

1. A student can search their subject names and their topic titles/subject names.
2. A student can combine subject, importance, progress, and due-date filters.
3. Results and counts contain only the authenticated student's records, including when search terms match another student's data.
4. Search terms do not appear in URLs; POST search requests require CSRF.
5. No-match states and clear-filter actions are understandable and usable.
6. Invalid search and filter submissions show an accessible error message; valid submissions announce their result count.
7. Feature tests cover subject search, topic search by title and parent subject, combined filters, date-state behavior, cross-owner isolation, signed-out/admin denial, and CSRF.
8. Browser review covers narrow and desktop layouts plus keyboard/focus behavior.

## Verification plan

- Run focused subject/topic feature tests and the full PHPUnit suite.
- Run PHP syntax checks and locked dependency audit; inspect the patch with `git diff --check`.
- Manually verify search, each filter, combined filters, clear behavior, no-match states, owner isolation, keyboard use, and responsive layout.

## Verification record

- PHPUnit: passed, 21 tests and 205 assertions, using the in-memory SQLite test connection.
- PHP syntax: passed for the changed PHP routes, controllers, models, views, and feature tests.
- `git diff --check`: passed; Git reported only existing LF-to-CRLF conversion notices.
- Locked dependency audit: not rerun for F-05 because Composer files were unchanged; the most recent audit on 2026-10-15 found no advisories.
- Browser: owner reports keyboard review passed and confirmed the desktop/narrow-screen subject and topic pages looked good. Accessible alerts for malformed/unsupported search and filter values and result-count status are implemented on both search pages. The Playwright suite covers these authenticated views when synthetic student credentials are supplied locally.
- Feedback update verification: the full PHPUnit suite passed with these changes included (26 tests, 248 assertions); PHP syntax checks and `git diff --check` passed. The authenticated Playwright page check needs locally configured synthetic-account credentials.

## Boundaries

- Do not add priority scoring, plan generation, analytics, reports, or recommendations in this story.
- Use existing subject/topic fields; do not add schema fields or JavaScript dependencies.
- Keep all lookups owner-scoped in the database query, not by filtering a broader result set in PHP.

## Verification update (2026-10-16)

- Full PHPUnit suite: passed, 26 tests and 248 assertions.
- PHP syntax checks: passed for all changed PHP files.
- `git diff --check`: passed; Git emitted only the existing LF-to-CRLF notices.
- Composer audit: attempted with `--locked`; could not connect to Packagist through the restricted network, so this run is unverified. The previously recorded 2026-10-15 audit reported no advisories.
- Browser automation: Playwright could not start its worker (`spawn EPERM`). The available browser-control inventory was empty. The owner's previously recorded desktop/narrow-screen and keyboard review remains the available manual evidence.
