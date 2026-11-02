# Draft ISO/IEC 25010:2023 evaluation protocol

**Status:** Draft for project owner/adviser review; not an evaluation result.
**Purpose:** Define a repeatable evaluation of Duno against the four quality characteristics named in the project objective: Functional Suitability, Performance Efficiency, Reliability, and Security.
**Requirements baseline:** Owner-approved main SRS `docs/requirements.md` (F-01–F-16 and S-01–S-10), approved 2026-11-02, plus the approved story decisions linked from it.
**Evaluation target:** Owner-confirmed r9 upload, corresponding to local archive `build/duno-full-release-20261101-r9.zip` (SHA-256 `3B87F3EFEF361BA245DA15C1FDC3030993FC0FF5372C5941C04D70EE69CE4736`). The owner supplied the live marker `duno-full-release-20261101-r9` on 2026-11-02. Full uploaded-file parity is not verified, and the archive's clean source commit is unknown. Do not attribute prior r8 observations to r9.

**Artifact and provenance note:** The r9 archive contains 1,253 entries and embeds marker r9; the owner confirmed the matching live marker. Its source provenance and complete file parity are not independently confirmed. The current working tree also contains later uncommitted changes that are not part of the archived r9 candidate. The deployment notes identify those newer changes as pending.

This protocol uses the four project-selected quality characteristics as evaluation categories. It does not claim certification or full conformance to every part of ISO/IEC 25010:2023. The selected measures and acceptance thresholds require project owner/adviser approval before results are collected.

## 1. Evaluation questions

| Characteristic | Evaluation question |
|---|---|
| Functional Suitability | Do the selected Duno functions produce the correct and complete outcomes required by the approved F-01–F-16 baseline for the tested scenarios? |
| Performance Efficiency | How long do selected user actions and server responses take under a documented environment and data profile, and do they remain within an approved target? |
| Reliability | Can selected workflows be completed repeatedly without unexpected failures, lost/duplicated records, unauthorized persistence, or incorrect recovery behavior? |
| Security | Do the selected authentication, authorization, privacy, validation, session, cache, error and deployment controls resist the defined unauthorized or invalid actions without disclosing protected information? |

## 2. Decisions required before execution

Complete and approve these fields with the project owner/adviser. Values are intentionally not invented.

| Decision | Approved value / record |
|---|---|
| Evaluated release marker and source/package identity | `duno-full-release-20261101-r9`; package SHA-256 `3B87F3EFEF361BA245DA15C1FDC3030993FC0FF5372C5941C04D70EE69CE4736`; source commit unknown. Owner confirmed upload and supplied the matching live marker; full file parity **not verified**. Previous r8 observations are historical only. |
| Environment and URL (production, staging, or isolated local) | InfinityFree live host, HTTPS, app domain recorded in `docs/deployment.md`; deployment notes report PHP 8.4.27. Fresh host/runtime identity for this evaluation: **pending capture**. |
| Database engine/version and migration ledger state | Owner-provided phpMyAdmin output on 2026-11-02 confirms progression migration `2026-11-01-000002` in batch 15 and zero remaining 500-XP rows. Exact hosted DB engine/version and a fresh environment capture remain **pending**. |
| Browser(s), OS/device(s), screen sizes and network conditions | Brave and Edge are present in existing owner-reported r8 observations; exact browser versions, OS/device, viewport and network condition for each r9 evaluation run: **pending capture**. |
| Evaluation dates, evaluator roles and any external participants | **Pending** |
| Scenario/task sample and synthetic dataset profile | **Pending** |
| Repetitions per scenario and stopping rule | **Pending** |
| Performance target(s), measurement boundary and acceptable percentile | **Pending** |
| Functional/security pass criteria and severity handling | **Pending** |
| Consent, privacy, retention and evidence storage arrangements | **Pending** |
| Adviser-required instrument/statistical treatment or reporting format | **Pending** |

### Suggested defaults to approve or revise

These are practical project targets proposed to make the protocol executable. They are **not ISO-prescribed limits**, are not yet adviser-approved, and must not be represented as approved criteria until the owner/adviser confirms them.

| Decision | Suggested default |
|---|---|
| Scope | Cover F-01–F-16 and S-01–S-10 using the existing acceptance cases; report each requirement not exercised. |
| Test environment | Use a local isolated copy of the r9 artifact/schema with synthetic accounts for all writes, errors and privacy tests. The owner has confirmed the live r9 marker; keep live checks read-only unless the owner explicitly approves a dedicated synthetic account and cleanup plan. |
| Browser/viewports | Edge and Brave on the available desktop setup; 390×844 and 1365×768 CSS pixels, plus 320px width for auth overflow. Record actual browser/OS versions and do not imply a representative device sample. |
| Performance repetitions | One separately recorded warm-up then 10 measured trials per action. Report median, min, max, and failures; do not report p95 from this small sample. |
| Performance targets | ≤3.0 seconds to response-complete for Focus, Plan, and session-save requests; ≤5.0 seconds for Aralytics. These are proposed project targets for the fixed test profile, not standard limits. Also report page render time separately where measured. |
| Reliability repetitions | Repeat each selected critical workflow 10 times in the isolated environment. Require 10/10 expected completions and zero data-integrity, duplicate-save, or owner-isolation discrepancies to pass this proposed check. This sample does not establish a population reliability rate. |
| Functional criteria | Every selected case must meet its recorded expected result; any failure remains visible in results. Do not calculate a project-wide pass claim if critical cases fail or are unrun. |
| Security criteria | Zero confirmed unauthorized disclosure, mutation, or persistence; zero open Critical/High findings at completion. Record all lower-severity findings and disposition. |
| Participants/statistics | No human participants or satisfaction survey in this technical protocol. Use descriptive counts and timing summaries only. Add a separate approved method if the course requires participant ratings or expert review. |
| Evidence handling | Keep raw logs/screenshots in a private local folder; include only sanitized excerpts in repository/paper. Never record passwords, verification codes, real personal data, or private check-in values. |

If these values are accepted, replace “suggested” with the approval date/authority and copy the values into the run metadata before test execution. Otherwise record the revised values and rationale.

Recommended default is to use synthetic accounts/data and a non-production environment for destructive, fault-injection, invalid-input and security checks. If only the live service is available, first approve a safe non-destructive scope; do not run destructive or load tests on live student data.

## 3. Target and controls

1. Select one release and preserve its marker, package hash or other available identity, source commit (if available), deployment date and relevant configuration. A marker alone does not prove file parity.
2. Record the corresponding migration status and verify that the app version is compatible with the schema. Do not run migrations as part of this evaluation unless a separate approved deployment task requires it.
3. Prepare synthetic student accounts A and B and a separate synthetic admin, with known subjects, topics, study sessions, plans and optional check-ins. Record the fixture values without passwords, verification codes or real personal information.
4. Keep browser, PHP, database, host, network and cache state consistent within each set of performance/reliability trials. Record environmental variation instead of hiding it.
5. Use the procedures in `docs/acceptance-verification-plan.md` as the functional/security scenario source. Select and identify the subset before execution; do not claim untested requirements.
6. Collect only evidence needed to establish outcomes. Sanitize screenshots, logs and response captures; exclude credentials, codes, real student identifiers and check-in values that identify a person.

## 4. Measures and procedures

### 4.1 Functional Suitability

**Procedure:** Run approved acceptance scenarios with known input values. At minimum, select representative and academically critical paths across authentication/roles, subject/topic ownership, Focus ranking, Plan limits, Timer explicit save, Aralytics totals/print, private check-ins, admin aggregates and PWA behavior if F-15 is included in the evaluation scope. The full case list is `acceptance-verification-plan.md`.

**Record per scenario:** requirement/case ID; setup and input fixture; expected result; observed result; pass/fail/not-run; defect severity/reference; release/environment; evidence reference.

**Measures:**

- Scenario pass rate = passed executed scenarios ÷ all executed scenarios × 100. Report the numerator and denominator; do not count Not run as passed.
- Requirement coverage = requirement IDs with at least one executed scenario ÷ requirement IDs selected for evaluation × 100. List excluded IDs.
- For numeric functions, record expected and actual values and whether they match exactly, such as minutes, counts, date boundaries, ranking order, plan capacity and XP.
- For access/privacy cases, record allowed/denied outcome and whether any protected value was disclosed or changed.

**Proposed pass rule (approval required):** Each selected critical expected behavior passes; no unresolved critical defect remains; the overall scenario pass rate meets the threshold approved in Section 2. Report requirement coverage alongside pass rate so a small passing subset cannot imply full coverage.

### 4.2 Performance Efficiency

**Procedure:** Run a preselected set of ordinary user workflows against the declared release and a documented synthetic data profile. Candidate actions: sign-in page load, Focus ranking page load, Plan view/generation, save a study session, and Aralytics period view. Exclude stress/load testing unless specifically approved for a safe isolated environment.

**Measure boundaries:** Prefer browser-observed navigation/request start to response completion for server requests, and record full page/action completion separately where client rendering is material. State which value is reported; do not mix server response time with page-load time.

**Measures:**

- Elapsed time in milliseconds for each measured action, per trial.
- Median and 95th percentile for each action only when the approved repetition count supports that summary; include trial count, minimum and maximum as context. Do not omit failures/timeouts from the report; count and describe them separately.
- Optional resource measures only if obtainable without changing production behavior: transfer size and browser memory/CPU observation. Label tool and measurement scope.
- Failure/timeout count and rate for the measured actions.

**Controls and limits:** Record host load/time, browser/device, connection type, viewport, cache state, data profile and action sequence. Separate cold and warm/cache conditions if both are measured. Do not invent an acceptable latency threshold after seeing results. Compare against the owner/adviser-approved target from Section 2. If no numeric target is approved, report descriptive timings and mark target attainment **not evaluated**.

### 4.3 Reliability

**Procedure:** Repeat selected core workflows using fixed synthetic inputs and a preapproved repetition count. Include create/read/update/delete where safe, plan generation and editing, explicit Timer save, period summary reload, and recovery from a controlled browser refresh or network interruption. Conduct interruption/failure injection only in isolated test data/environments. Do not deliberately interrupt live transactions or production connectivity.

**Measures:**

- Workflow completion rate = completed trials with all expected persisted outcomes ÷ all executed trials × 100.
- Unexpected application failure count/rate, including unhandled error pages, failed requests and timeouts; classify failures by step.
- Data-integrity discrepancy count: missing, duplicated, incorrectly linked, out-of-owner-scope or incorrectly calculated records found after reload/reconciliation.
- Recovery outcome for each approved interruption: expected retry/recovery behavior, any data loss/duplication, and whether an unsaved Timer session was incorrectly persisted.
- Availability/downtime only if monitoring period, probe interval, endpoint and treatment of scheduled maintenance are approved in Section 2. Otherwise do not claim availability percentages.

**Proposed pass rule (approval required):** No unresolved data-integrity or cross-owner discrepancy; every critical workflow meets its approved completion target; recovery behavior matches the requirement. State the exact number of trials and limits. A small manual sample is not a statistical reliability guarantee.

### 4.4 Security

**Procedure:** Execute the approved security cases in `acceptance-verification-plan.md` with synthetic users A/B/admin and the declared target. Use direct URL and form requests; test only the specified benign invalid inputs and access boundaries. Do not use real records, credentials, destructive payloads or unapproved scanning tools.

**Minimum requirement coverage:**

| Security area | Requirement links | Evidence to capture |
|---|---|---|
| Authentication and role boundary | S-01, F-01, F-02 | Route, test role, expected/observed access outcome; no secret values |
| Owner isolation and nested references | S-02, S-07 | A/B ownership scenario, operation and result; confirm no read/change/attach across owners |
| Validation, escaping and query safety | S-03 | Input category, validation outcome and rendered result; avoid executable payloads or sensitive data |
| CSRF and session settings | S-04 | Invalid/missing vs valid token outcome; sanitized cookie attributes and HTTPS context |
| Secret/data handling | S-05 | Reviewed paths/artifacts, method and revision; never include secret values in evidence |
| Direct private-path access | S-06 | Exact approved path and raw HTTP status/body classification, excluding sensitive response content. Follow `deployment.md`; do not repeat its explicitly prohibited `/.env` probe. A provider-generated page must be attributed to the provider and not treated as proof of a specific application rule. |
| Check-in privacy | S-07 | A/B, admin, search, print and export visibility outcomes using synthetic values |
| Safe production errors | S-08, S-09 | Disposable synthetic failure, status and sanitized response; confirm no stack/SQL/config disclosure |
| Headers and cache behavior | S-09, S-10 | Sanitized response headers for selected routes; compare dynamic pages with documented public asset caching |

**Measures:** number of selected security cases passed/failed/not run; count and severity of confirmed findings; number of unauthorized disclosure, mutation or persistence events (target should be zero, subject to owner/adviser confirmation); remediation state and retest result. A configuration review alone is not a runtime security pass.

**Safety:** Do not perform penetration testing, brute force, load generation or probing outside the approved list. Use a disposable/staging environment for induced errors and malformed requests. Stop and preserve minimal evidence if any real data, secret, cross-user content or unexpected production state is exposed; notify the project owner through the approved project channel (the evaluator does not send external messages on their behalf).

## 5. Evidence and results record

Create one row per executed case/trial. Store evidence in a project-approved private location; only sanitized outputs should enter the academic repository.

| Field | Entry |
|---|---|
| Evaluation ID / date / evaluator | Pending |
| Approved protocol revision | Pending |
| Release marker / package hash / commit | Pending |
| Environment / runtime / DB / migration state | Pending |
| Browser/device/network/data profile | Pending |
| Requirement and case ID | Pending |
| Procedure and input fixture reference | Pending |
| Expected result / observed result | Pending |
| Raw measure(s), unit and numerator/denominator | Pending |
| Result: Pass / Fail / Not run / Not applicable | Pending |
| Defect ID/severity and retest link | Pending |
| Sanitized evidence location | Pending |
| Limitations/deviations | Pending |

Keep unedited raw measurement records separate from calculated summaries. Record any excluded trial and the reason. Do not delete failures from the denominator without a documented, preapproved exclusion rule.

## 6. Analysis and reporting plan

- Use descriptive summaries: counts, denominators, proportions/percentages, and measured timing summaries appropriate to the agreed sample. For each percentage, state numerator and denominator.
- Show results separately by characteristic and requirement/case, then summarize major defects and limitations. Avoid one blended score unless the adviser approves a defensible weighting method before testing.
- Use no inferential statistical claim unless the sampling design and method are approved and the data support it. Do not interpret functional pass rates as user satisfaction or learning gains.
- Compare observations only with acceptance criteria approved before execution. If no threshold exists, report the measured result without a pass claim for target attainment.
- Separate local, staging and production results. A result on one host/revision does not establish behavior on another.
- State missing cases, failed runs, host constraints, sample limits, measurement uncertainty and any differences between the tested artifact and submitted/live artifact.

## 7. Approval and completion gate

Before collecting evaluation data, the project owner/adviser should approve the Section 2 values, the selected case set, thresholds and any participant involvement. If human participants will rate or use the product, obtain the required consent and course/institution approval before recruitment, define the instrument and data handling, and do not infer these requirements from this draft.

Evaluation status stays **Pending** until execution records and analysis are complete. This document is a protocol draft and contains no evaluation findings.

## Related repository evidence

- Requirements baseline: `docs/requirements.md`.
- Case procedures: `docs/acceptance-verification-plan.md`.
- Current traceability and gaps: `docs/academic-evidence/requirements-traceability.md`.
- Historical automated/manual evidence: `docs/academic-evidence/testing-evidence.md`, `docs/ai-use-log.md`, and feature stories.
- Release and host constraints: `docs/deployment.md` and `docs/academic-evidence/deployment-evidence.md`.
