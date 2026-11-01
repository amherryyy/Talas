# Testing and evaluation evidence

This is an inventory of tests and recorded outcomes, not a fresh test report. No tests were run for this documentation audit. Historical counts below are quoted from project records and may refer to different source snapshots and test configurations.

## Automated test inventory

- **PHPUnit / CodeIgniter:** `tests/unit/` covers priority, plans, Aralytics, progression, suggestions and helper services. `tests/feature/` covers authentication, home, admin, student dashboard, subjects, topics, plans, sessions, check-ins, streaks and local admin bootstrap. `tests/database/` and `tests/session/` contain starter/example tests. See `phpunit.dist.xml` and `tests/README.md` for configuration/instructions.
- **Playwright:** `tests/e2e/responsive.spec.js` is the located browser spec. The current project notes record a three-test responsive run across 320/400/768/1280 pixel widths against an isolated review DB and synthetic account on 2026-10-31. Logged coverage included auth/student pages, check-in CRUD, timer discard and topic CRUD. This is not full acceptance for every requirement.
- **CI:** `.github/workflows/ci.yml` uses PHP 8.2 and 8.4; validates Composer files, installs locked dependencies, PHP-lints application/tests, runs PHPUnit, and runs `composer audit --locked` on PHP 8.4. Actions are pinned to full SHAs and permissions are read-only. The docs report prior CI passes at 67 tests/612 assertions per PHP version. No PHP style tool or MySQL service is configured in CI.

## Recorded results and limits

| Evidence | Reported result | What it supports | What it does not support |
|---|---|---|---|
| CI / PR #7 records | 67 tests and 612 assertions per PHP 8.2/8.4 run; dependency audit reported no advisories at that time | Historical checks of the committed CI snapshot | Current worktree, current dependencies or every migration against MySQL |
| Feature/story AI-use records | Per-story historical PHPUnit, syntax, audit and selected Playwright results | Increment-level checks as described in `docs/ai-use-log.md` and stories | A single all-features test on the current release |
| Local full PHPUnit 2026-10-31 | 103 tests / 1,027 assertions passed, per acceptance plan | Current-at-that-time local automated behavior | Live host behavior, ISO quality results, or a clean committed snapshot |
| Local Playwright 2026-10-31 | 3 E2E tests passed; details in acceptance plan | Listed responsive CRUD flows against isolated synthetic data | All requirements, full device matrix, production behavior |
| Candidate package checks | r5 ZIP inventory/hash and PHP lint of 167 packaged PHP files; stale r6 ZIP inventory/hash and PHP lint of 1,036 packaged PHP files recorded | Package contents and PHP syntax at each build time; r6 predates the final role-denial header fix and its SHA-256 is in `deployment.md` | A current replacement package, successful upload, clean source commit, runtime behavior or host security acceptance |
| Composer audit | `composer audit --locked` passed 2026-11-01 after clearing injected proxy variables for that process | Locked project dependencies had no reported advisories at audit time | Host-specific package parity or later dependency changes |
| Live r4 smoke | Public marker, manifest JSON, service worker MIME/scope; homepage no overflow at 390/768/1365; login overflow at 320/400 | Narrow r4 browser observations | r5 appearance, private routes, authenticated flows or complete offline behavior |
| Owner-reported live checks | Focus/Plan/Timer (2026-10-31); Aralytics/print (2026-11-01); signed-out Focus redirect and Back behavior (2026-11-01) | Those narrowly described manual journeys, attributed to owner | Exact data, full acceptance matrix, all roles, or independent reproductions |
| Host private path observations | `/app/Config/Paths.php` showed provider 403; `/.env` displayed offline page | Reported visible outcomes only | Exact application deny-rule result; `/.env` HTTP status (do not repeat per deployment note) |

For details, dates and procedure statuses, use `docs/acceptance-verification-plan.md`, `docs/deployment.md`, feature story files and `docs/ai-use-log.md`. This audit did not execute or alter tests/results.

## Manual acceptance still required

The acceptance plan lists all F-01–F-15 and S-01–S-10 cases. Among the material open items are complete A/B owner isolation, registration edge limits, topic validation, multi-date and overlapping plans, Timer interruptions and session bounds, check-in exclusion from admin/print, Aralytics period reconciliation, admin aggregate reconciliation, keyboard/accessibility/print coverage, PWA cache inspection, path-by-path host denials, raw security headers, and safe production errors. Record revision, environment, date, tester, migration state, browser/device, result, evidence location and defects for every execution.

## ISO/IEC 25010:2023 evaluation

**Status: NOT VERIFIED / pending.** The project objective names evaluation using selected quality characteristics (Functional Suitability, Performance Efficiency, Reliability and Security). Repository notes explicitly state no evaluation protocol or results. No participant count, test sample, instrument, benchmark workload, environment controls, acceptance thresholds, raw data, statistical method, or result exists in the evidence reviewed. Automated functional tests and selected manual journeys are not substitutes for a complete quality evaluation. Obtain human approval of a protocol and report only collected results.
