# Navigation, motion, and safe asset caching follow-up

**Status:** Implementation merged through PR #12; local browser and owner review remain pending.

## Owner request

The owner asked to make the full Duno navigation easier to follow and faster to use, visually separate cards, buttons, and links, add restrained animation, make Duno react when the student levels up, animate the mascot in the daily energy/workload check-in, and apply caching where useful.

## Changes

- Added Duno Guide to desktop and mobile student navigation. The desktop navigation stays visible while scrolling, marks the current section, and closes its More menu when users click outside, press Escape, or follow a menu link.
- Rebalanced the dashboard tool cards into a clear two-row, asymmetric grid with stronger spacing, a colored edge for each tool, and distinct hover/focus feedback. Existing primary and secondary button styles remain consistent; action links gain more visible hover feedback.
- Added an owner-scoped level-up message when saved study sessions or session edits cross a level threshold. The notice lists any Aralytics insights unlocked by that change. It uses the existing session progression rules; check-ins never award XP.
- Added a short mascot entrance motion when the check-in prompt and level-up dialog open. Existing reduced-motion rules still suppress animation.
- Added `duno_asset()` to append a file modification timestamp to static asset URLs. Apache cache headers apply only to public static file types for seven days. Server-rendered student pages are not page-cached.
- Reduced history processing for dashboard progression by calculating lifetime XP, saved-session count, and distinct study days with owner-scoped database aggregates, while loading only the six-month session window needed for progression charts. Session saves now use a direct lifetime-minute aggregate for level checks.
- Scoped date-based Aralytics session reads to the six-month progression window (the summaries still select the requested period) and private check-in reads to the selected period. The all-time session report still loads the full session history as before.

## Security and privacy

XP transitions use the signed-in student's existing owner-scoped study-session rows. The modal is populated only from flash data produced after a successful session save/update. Check-in values are not read, copied, or included. Caching is limited to public static assets; dynamic HTML containing private study data remains uncached by application page caching.

## Verification record

- PHPUnit: **Passed**, 73 tests and 677 assertions on local PHP 8.2.12 with the `intl` and SQLite extensions enabled for the run (rechecked 2026-10-26).
- PHP syntax: **Passed**, 138 PHP files under `app`, `tests`, and `scripts` linted (rechecked 2026-10-26).
- JavaScript syntax: **Passed** for `student-navigation.js` and `level-up.js` (rechecked 2026-10-26).
- Composer validation: **Passed** with `--strict`. Locked dependency audit with `--ignore-unreachable`: **No advisories reported**; Packagist was unreachable, so online advisory sources could not be refreshed (rechecked 2026-10-26).
- Hosting package: **Built and inspected**; expected `htdocs` root entries and private-path deny rule are present, `.env` is absent, and the ZIP includes the hidden root `.htaccess`.
- Local browser review: **Blocked**. The isolated MariaDB process started but the review connection was denied access to the configured database, and the local homepage returned HTTP 500. Playwright stopped before running tests because Windows denied its worker process with `spawn EPERM`. The review database, browser tests, and manual phone/desktop visual checks remain outstanding.
- InfinityFree deployment and live smoke checks: **Pending**. Host support for optional `mod_expires` and `mod_headers` directives remains unverified.
- Owner review: pending.

## Status

Merged to `main` through PR #12. Verification was refreshed locally on 2026-10-26; browser review could not proceed for the reasons above. No files were staged, committed, pushed, or deployed during the follow-up.
