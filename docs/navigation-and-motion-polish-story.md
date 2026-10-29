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

## Accessible Back and Home controls

The owner clarified that Back and Home should not be repeated on every page. Public, authentication, student, and admin pages already have a brand link, menu, or role navigation panel for Home, so no extra Home button is rendered there. Student detail and form pages keep one destination-specific Back or Cancel link; the shared student shell adds no Back button, and redundant form Cancel links were removed where a matching Back link already exists. These in-content return links are styled as secondary buttons while retaining anchor semantics and their specific destinations. Error pages retain Back and Home recovery links because normal navigation may be unavailable.

Focused view assertions distinguish existing Home navigation from in-content Back links and guard against duplicate shell buttons. The owner identified duplicate controls in the curriculum catalog; the shell-level Back button and matching form Cancel links were removed where a destination-specific Back link already exists. On 2026-10-27, the owner asked to give all student return links a button appearance; the seven subject, topic, catalog, and session return links now use the existing secondary-button style. Browser review of this latest styling change is pending.

## Tour spotlight and responsive navigation follow-up

On 2026-10-27, the owner requested a clearer onboarding spotlight and a friendlier mobile/tablet experience. The tour now dims and lightly blurs the page around its current target, outlines the target with a bright ring, and moves its instruction card to the clearest available corner. It behaves as a modal dialog: background page regions become inert, keyboard focus stays within the tour, Escape closes it, and focus returns to the highlighted target. The More step explains its contents without asking students to operate the dimmed menu.

Mobile navigation labels and touch targets are larger, coarse-pointer tablet navigation has taller targets, the desktop header compresses gracefully across tablet widths, and the More control shows when the current page belongs to its section. Browser and screen-reader review of these changes remains pending.

## Login and registration welcome refinement

On 2026-10-27, the owner supplied a mobile auth reference and requested more mascot interaction. Login and registration retain Shield's routes, validation, CSRF handling, and fields, while the shared auth layout puts Duno and a concise welcome message ahead of the form on phones and beside it on wider screens. Primary submit actions are full-width pills; the alternate login/register route is a clear outlined action separated by “or.” Students can activate the mascot to see a brief bounce and one of several static encouragements. The interaction uses the existing PNG, adds no external service, and respects reduced-motion preferences. Browser review remains pending.
