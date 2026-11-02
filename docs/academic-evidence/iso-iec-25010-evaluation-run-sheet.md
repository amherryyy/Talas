# ISO/IEC 25010:2023 evaluation run sheet — Duno r9 candidate

**Target:** Owner-confirmed upload of `duno-full-release-20261101-r9` on 2026-11-02.
**Package SHA-256:** `3B87F3EFEF361BA245DA15C1FDC3030993FC0FF5372C5941C04D70EE69CE4736` (local r9 archive).
**Source commit:** Unknown; provenance has not been recorded.
**Target caveat:** The owner supplied the live r9 marker. Full uploaded-file parity is not independently confirmed. Do not attribute prior r8 checks to r9.

**Recheck on 2026-11-02:** The owner confirmed r9 upload and supplied the live marker. Its local archive contains 1,253 entries and embeds marker r9; the source commit is unknown. Current auth and Plan changes after the archive build remain pending and should not be presumed part of r9.

This is an execution worksheet, not a completed evaluation. All rows start **Not run in this evaluation**. Earlier checks are identified as prior records; they do not count as run-sheet results. Use alongside [the protocol](iso-iec-25010-evaluation-protocol.md), the draft [evaluation session guide](evaluation-session-guide.md), and [`docs/acceptance-verification-plan.md`](../acceptance-verification-plan.md).

## A. Run metadata — complete before testing

| Field | Value |
|---|---|
| Evaluation ID | Pending |
| Protocol revision and approval date | Pending |
| Evaluator(s) | Pending |
| Test date/time and timezone | Pending |
| Release marker response and response headers | Pending fresh capture |
| Package hash / source commit confirmation | Recorded above; deployed-file parity pending |
| Host, PHP runtime, DB engine/version | Host: InfinityFree; PHP 8.4.27 is owner-reported; exact runtime/DB capture pending |
| Migration ledger | Owner-provided phpMyAdmin output confirms `2026-11-01-000002_ScaleProgressionXpRewards`, batch 15; zero rows with `xp_awarded = 500` (2026-11-02). Other schema/engine details should be matched to the release before the evaluation run. |
| Browser/version, OS/device, viewport, network | Pending per run |
| Synthetic data profile and account IDs (pseudonyms only) | Pending; do not record credentials or real identities |
| Approved repetitions and exclusion rules | Pending owner/adviser approval |
| Approved functional/security thresholds and severity rules | Pending owner/adviser approval |
| Approved performance target and percentile | Pending owner/adviser approval |
| Evidence folder/location and retention | Pending approval; store raw evidence privately, publish sanitized evidence only |

## A1. Existing evidence to carry forward (not new evaluation runs)

The following is already recorded in `docs/acceptance-verification-plan.md` and `docs/deployment.md`. Retain its original attribution and scope. These checks concern earlier releases and do not count as r9 acceptance.

| Characteristic | Existing record | Limitation for this evaluation |
|---|---|---|
| Functional Suitability | Local PHPUnit passed 103 tests/1,031 assertions on 2026-11-01; local Playwright passed 3/3 on the isolated review DB. r5 owner reports cover Focus, Plan, Timer and Aralytics; r8 has selected auth redirect, route denial, two-account read isolation and report observations. | Local automation, r5 checks and partial r8 observations do not cover all F/S cases or prove r9 behavior or uploaded-file parity. |
| Performance Efficiency | No controlled response-time benchmark or approved latency target is recorded. | No performance result; measure under a fixed profile after criteria are approved. |
| Reliability | r8 offline PWA check reported successful offline recovery pages. A Timer POST failed offline, then manual refresh after reconnect replayed it and created session 5. | No repeated-trial reliability study. Form replay needs isolated follow-up; don't repeat against current live test data. No r9 behavior is verified. |
| Security | r8 observations report four path-specific 403s with selected headers and branded body, signed-out Focus 302, unknown route 404, and selected A/B read privacy checks. The owner reports cross-account write-isolation passed on r9. | Owner-reported r9 write check lacks route/action detail. Full route/header matrix, forged-reference cases, and remaining r9 behavior remain open. |

The latest deployment record also identifies temporary synthetic topic 7 and sessions 4–5 requiring owner cleanup. Leave those records untouched and do not use those accounts as evaluation fixtures.

## B. Functional Suitability

Use dedicated synthetic accounts and the expected outcomes in `acceptance-verification-plan.md`. Record exact inputs and outputs for numerical calculations. Do not use or alter the temporary accounts/records mentioned in the current deployment record without owner direction.

| Run ID | Requirement / existing case | Execution steps | Capture | Result |
|---|---|---|---|---|
| FS-01 | F-01 / F-01-AUTH | Test public page, synthetic registration and verification, unverified access denial, sign-in, refresh, logout, invalid/expired code and resend controls. | Each step outcome, role assigned, status/result; never capture code/password. | Not run |
| FS-02 | F-02 / F-02-ROLE | Request student/admin routes as guest, student and admin via direct URL. | Role, route, expected/actual status and redirect/denial. | Not run |
| FS-03 | F-03 / F-03-SUBJECT | As A, create/read/update/delete own subject; try B subject ID; verify saved-history deletion rule. | Before/after synthetic records, route/result, cross-owner disclosure/mutation. | Not run |
| FS-04 | F-04 / F-04-TOPIC | Create/edit/delete topics spanning importance, difficulty, progress and past/today/future/blank due dates; submit invalid and foreign references. | Expected vs saved fields, defaults, validation and owner scope. | Not run |
| FS-05 | F-05 / F-05-SEARCH | Search subject/topic terms and combine subject, importance, progress and due-state filters; repeat with B-only data. | Query inputs, result IDs/counts by pseudonym, URL behavior. | Not run |
| FS-06 | F-06 / F-06-FOCUS | Prepare known topics covering formula inputs, empty/overdue/tie cases; open Focus repeatedly. | Fixture, expected score/order/reasons, actual order/reasons, repeat consistency. | Not run |
| FS-07 | F-07 / F-07-PLAN | Generate with 15 minutes, several topics and 480 minutes; edit/reorder/remove/complete; check date navigation and scheduled conflicts/owner references. | Availability, item minutes/start-times, expected and actual totals, overlap/ownership outcome. | Not run |
| FS-08 | F-08 / F-08-TIMER | Test elapsed and preset timers, pause/resume, stop/review/discard/save, refresh and bounds; edit/delete saved synthetic session. | Timer states and whether persistence occurs only after explicit save; saved duration/reference. | Not run |
| FS-09 | F-09 / F-09-ARALYTICS | Seed known sessions/check-ins; compare today/7-day/30-day/all-time totals; review chart equivalent and print. | Hand-calculated expected values, displayed values, check-in visibility in print. | Not run |
| FS-10 | F-10 / F-10-GUIDE | Select owned subject/optional topic and each Guide goal; inspect steps and supported/unsupported outcome mapping. | Goal, expected/actual steps, source link and editorial label. | Not run |
| FS-11 | F-11 / F-11-CHECKIN | Create/edit/delete synthetic ratings as A; try B’s record and inspect search/admin/print/export surfaces. | Owner-scoped outcome and each exclusion result. Avoid reporting identifiable values. | Not run |
| FS-12 | F-12 / F-12-ADMIN | Compare admin aggregate cards to known synthetic totals; attempt student access and record-level drill-down. | Expected/actual aggregate counts, role result, identity/check-in absence. | Not run |
| FS-13 | F-13 / F-13-UI | Exercise public/auth/student/admin flows at approved viewport/device matrix; keyboard navigation, onboarding skip/replay, contextual back/cancel and print. | Browser/device/viewport, overflow, focus/labels, tour state and print observations. | Not run |
| FS-14 | F-14 / F-14-PROGRESSION | For a new synthetic student, repeat visits same day, save known session time, cross dates/missed day, submit check-in. | Expected/actual XP, streak, level/unlocks; confirm check-in adds no XP. | Not run |
| FS-15 | F-15 / F-15-PWA | Inspect manifest/worker/cache; online/offline navigation, authenticated pages and attempted write; install if supported. | Exact cache entry list, navigations, write outcome, browser/version. | Not run |
| FS-16 | F-16 | In an isolated disposable environment, delete a synthetic account with the correct password and exact `DELETE`; exercise invalid confirmation/password and a safe transaction failure. | Confirm owned records are removed atomically on success, session ends, and all records remain after rejected/failed deletion. | Not run |

### Functional summary

| Measure | Entry |
|---|---|
| Selected cases executed / selected | Pending |
| Passed / failed / not run | Pending |
| Scenario pass rate (passed ÷ executed) | Pending; report numerator and denominator |
| Requirements covered / selected | Pending |
| Critical defects and retests | Pending |

## C. Performance Efficiency

Use the approved data profile, browser/device, cache state and repetitions from the protocol. Measure the same boundary on every trial. Do not conduct load or stress testing against the live host.

| Run ID | Action | Measurement boundary | Trials / raw milliseconds | Summary / approved target result |
|---|---|---|---|---|
| PE-01 | Sign-in page load | Navigation start to response complete; separately note full render if measured | Pending | Pending |
| PE-02 | Focus ranking load | Request/navigation start to response complete; separately note render | Pending | Pending |
| PE-03 | Plan view and plan generation | Record view and generation as separate actions | Pending | Pending |
| PE-04 | Explicitly save a synthetic study session | Submit to completed server response; confirm outcome separately | Pending | Pending |
| PE-05 | Aralytics period view | Request start to completed response; note selected period/data profile | Pending | Pending |

For each action, report trial count, median, minimum, maximum and p95 only if the approved repetition count supports it. Include failed/time-out trials in the record. If no numeric target is approved, report timings descriptively and mark target attainment **Not evaluated**.

## D. Reliability

Repeated writes, interruption scenarios and data reconciliation require an isolated/disposable synthetic account or environment. Do not create probe records on live accounts without explicit owner approval and a cleanup plan.

| Run ID | Scenario | Procedure and expected invariant | Capture | Result |
|---|---|---|---|---|
| RE-01 | Repeated core workflow | Repeat approved subject/topic, plan, explicit-session-save and summary-reload flows using fixed synthetic input. | Trial count; completion/failure by step; persisted/reloaded values and duplicates. | Not run |
| RE-02 | Persistence consistency | Save known plan/session data, reload pages and reconcile visible totals against synthetic source rows. | Missing, duplicate, mislinked or incorrect records. | Not run |
| RE-03 | Timer interruption | In isolated context, refresh/close/discard before explicit save; verify no session is created. | Browser action and database/UI result. | Not run |
| RE-04 | Offline write recovery | In isolated context, attempt a synthetic write offline, reconnect without manually replaying, inspect for queued/duplicate persistence. | Request result, network/cache behavior, persisted state before/after reconnect. | Not run |
| RE-05 | Recovery / error behavior | Trigger only approved safe errors in a disposable context; navigate/retry as specified. | Failure response, recovery outcome, data integrity and disclosures. | Not run |

**Known prior observation, not a new run:** `docs/deployment.md` reports that an offline Timer POST failed, then a manual refresh after reconnect replayed the form and created session 5. Review browser resubmission behavior. Do not repeat this against the existing live test account; use a separately approved disposable context. The deployment record also notes temporary synthetic data needing owner cleanup.

## E. Security

Use acceptance-plan security cases. The protocol forbids repeating the inconclusive `/.env` probe. For each test record the route, pseudonymous role, expected/actual outcome, HTTP status and sanitized headers/body classification. Do not store secrets or sensitive response bodies.

| Run ID | Requirement / case | Scope | Prior r8 evidence (not a new run) | New result |
|---|---|---|---|---|
| SE-01 | S-01 / SEC-CSRF and F-02-ROLE | Authenticated role boundaries and state-changing request protection | Selected role routes and some headers reported in `deployment.md`; full matrix not recorded. | Not run |
| SE-02 | S-02 / SEC-OWNER | A/B read, edit, delete, count and forged foreign references for subject/topic/session/plan | Owner reports r9 cross-account write-isolation passed; exact operations/evidence not recorded. Other reads/counts/exports and forged foreign references remain unverified. | Partially reported passed |
| SE-03 | S-03 / SEC-INPUT | Invalid boundaries, harmless HTML-shaped input, output escaping and query safety | Source/test evidence exists; no full r8 input matrix recorded. | Not run |
| SE-04 | S-04 / SEC-CSRF | Missing/invalid/valid CSRF and session/cookie settings over HTTPS | Not fully captured for r8. | Not run |
| SE-05 | S-05 / SEC-SECRETS | Repository/release artifact/log review for secrets or real records | r8 ZIP inventory excludes root `.env` and Composer root manifests; no comprehensive evidence scan recorded here. | Not run |
| SE-06 | S-06 / SEC-PATHS | Approved private paths only; verify HTTP status and sanitized denial response | Four paths reportedly returned 403 with branded fallback and headers; see exact path list in `deployment.md`. `/.env` must not be repeated. | Not run in this evaluation; prior report only |
| SE-07 | S-07 / SEC-PRIVACY | Check-in owner scope and absence from admin/search/print/export | Selected report and check-in privacy observations reported; full surface matrix incomplete. | Not run |
| SE-08 | S-08/S-09 / SEC-ERRORS | Safe production error and DB-error disclosure behavior | Branded 403 and clean unknown-route 404 reported; induced DB error not verified. | Not run |
| SE-09 | S-09 / SEC-HEADERS | Header set on public/auth/student/admin/error routes | Selected r8 403 and marker responses reported. Full route/header matrix not captured. | Not run |
| SE-10 | S-10 / SEC-CACHE | Dynamic page no-store and cache behavior across auth/logout; static asset policy | Marker and selected 403 no-store reported; complete dynamic-page matrix not recorded. | Not run |

### Security summary

| Measure | Entry |
|---|---|
| Selected cases executed / selected | Pending |
| Passed / failed / not run | Pending |
| Confirmed findings by severity | Pending |
| Unauthorized disclosure/mutation/persistence | Pending; target zero, subject to approved criteria |
| Remediated and retested findings | Pending |

## F. Final evaluation summary

| Characteristic | Executed cases / denominator | Summary measure | Approved criterion | Outcome | Limitations |
|---|---|---|---|---|---|
| Functional Suitability | Pending | Pass rate and requirement coverage | Pending approval | Not evaluated | Pending |
| Performance Efficiency | Pending | Per-action latency summaries and failure counts | Pending approval | Not evaluated | Pending |
| Reliability | Pending | Workflow completion and integrity discrepancies | Pending approval | Not evaluated | Pending |
| Security | Pending | Case outcomes and severity-rated findings | Pending approval | Not evaluated | Pending |

## G. Execution rule

The worksheet is ready for configuration, but not execution. First approve the protocol's thresholds, scenario subset, repetition count, browser/environment, synthetic account plan and evidence retention. The owner has confirmed the live r9 marker. Obtain explicit owner approval before write, interruption, error-injection or other state-changing checks on the live site. The release record identifies existing temporary test data, so keep it untouched and avoid using those accounts as fixtures.
