# Testing and evaluation evidence

This is an inventory of tests and recorded outcomes. Historical counts below are quoted from project records and may refer to different source snapshots and test configurations. The latest full local PHPUnit run was executed on 2026-11-02 against the current uncommitted worktree.

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
| Local full PHPUnit after role-denial header fix 2026-11-01 | 103 tests / 1,031 assertions passed; focused role-denial case 1 test / 8 assertions | Local test behavior including the direct 403 response-header path | Live host behavior, ISO quality results, or deployment acceptance |
| Local full PHPUnit 2026-11-02 | 119 tests / 1,313 assertions passed with PHP 8.2.12 and PHPUnit 10.5.66; SQLite `:memory:`; `--no-coverage` | Current pre-commit worktree feature/unit behavior, including authentication consent and recovery paths, public legal pages, and current Aralytics report assertions | Hosted email/migration/cache behavior, manual browser acceptance, clean committed release, or ISO quality results |
| R9 manual checks reported by owner 2026-11-02 | Cross-account write-isolation and successful account deletion reported passed; phpMyAdmin shows progression migration batch 15 and zero legacy 500-XP rows | Owner-reported checks on r9 and supplied database query output | Independent reproduction, exact manual routes/data/evidence artifacts, deletion negative/rollback cases, or full r9 file parity |
| R9 System Outputs screenshots supplied 2026-11-02 | Seven distinct Edge screenshots: dashboard/progression, Focus explanation, Plan, Timer, Aralytics, print preview, and Guide; one print-preview image was duplicated | Representative visible outputs from the owner-confirmed r9 build | Screenshot files are still only in the conversation, not in the evidence folder; they do not prove workflow correctness or performance. Data's synthetic/owner-record status and full release parity are not independently confirmed |
| Local Playwright 2026-11-02 | 2 E2E tests passed; 1 topic-form scenario skipped because the synthetic review account has no subject to select | Login and responsive student pages at the configured widths against the isolated review database; app-authored migrations were applied there | Topic form interaction without a selectable subject, all requirements, full device matrix, production behavior |
| Local Playwright 2026-10-31 | 3 E2E tests passed; details in acceptance plan | Listed responsive CRUD flows against isolated synthetic data | All requirements, full device matrix, production behavior |
| Candidate package checks | Stale r6 ZIP inventory/hash; r7 clean-source ZIP inventory/hash and PHP lint of all 1,036 packaged PHP files | r7 package contents and syntax from source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3`; hash is in `deployment.md` | Complete uploaded-file parity, live HTTP status/header matrix or host security acceptance |
| Composer audit | `composer audit --locked` passed 2026-11-01 after clearing injected proxy variables for that process | Locked project dependencies had no reported advisories at audit time | Host-specific package parity or later dependency changes |
| Local Composer checks 2026-11-02 | `composer validate --strict` passed; `composer audit --locked` could not reach Packagist because the configured proxy at `127.0.0.1:9` refused the connection | Composer metadata is valid; dependency audit status remains the 2026-11-01 historical result | Current advisory status could not be independently refreshed |
| Live r4 smoke | Public marker, manifest JSON, service worker MIME/scope; homepage no overflow at 390/768/1365; login overflow at 320/400 | Narrow r4 browser observations | r5 appearance, private routes, authenticated flows or complete offline behavior |
| Live r7 content smoke | Fresh marker, signed-out Focus redirect, branded path/error fallbacks, manifest, worker and offline page | Brave content-level review, 2026-11-01 | Exact HTTP statuses/headers, actual offline behavior, cross-owner access and full file parity |
| Owner-reported live checks | Focus/Plan/Timer (2026-10-31); Aralytics/print (2026-11-01); signed-out Focus redirect and Back behavior (2026-11-01) | Those narrowly described manual journeys, attributed to owner | Exact data, full acceptance matrix, all roles, or independent reproductions |
| Host private path observations | `/app/Config/Paths.php` showed provider 403; `/.env` displayed offline page | Reported visible outcomes only | Exact application deny-rule result; `/.env` HTTP status (do not repeat per deployment note) |

For details, dates and procedure statuses, use `docs/acceptance-verification-plan.md`, `docs/deployment.md`, feature story files and `docs/ai-use-log.md`. This audit did not execute or alter tests/results.

For the next student and IT-professional sessions, use the draft [r9 evaluation session guide](evaluation-session-guide.md) with the [ISO/IEC 25010 run sheet](iso-iec-25010-evaluation-run-sheet.md). The guide is a preparation aid only; the protocol, participant materials, criteria, and data handling still require owner/adviser approval.

## Manual acceptance still required

The acceptance plan lists the approved F-01–F-16 and S-01–S-10 cases. Among the material open items are complete A/B owner isolation, registration edge limits, password recovery and account deletion, topic validation, multi-date and overlapping plans, Timer interruptions and session bounds, check-in exclusion from admin/print, Aralytics period reconciliation, admin aggregate reconciliation, keyboard/accessibility/print coverage, PWA cache inspection, path-by-path host denials, raw security headers, and safe production errors. Record revision, environment, date, tester, migration state, browser/device, result, evidence location and defects for every execution.

**R9 owner update (2026-11-02):** The owner reports that cross-account write isolation and the successful account-deletion flow passed on r9. Broader A/B cases and deletion negative/rollback paths remain open unless covered by those checks.

## ISO/IEC 25010:2023 evaluation

**Status: NOT VERIFIED / pending.** The project objective names evaluation using selected quality characteristics (Functional Suitability, Performance Efficiency, Reliability and Security). Repository notes explicitly state no evaluation protocol or results. No participant count, test sample, instrument, benchmark workload, environment controls, acceptance thresholds, raw data, statistical method, or result exists in the evidence reviewed. Automated functional tests and selected manual journeys are not substitutes for a complete quality evaluation. Obtain human approval of a protocol and report only collected results.
