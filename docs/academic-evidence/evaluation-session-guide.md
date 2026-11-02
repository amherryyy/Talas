# Duno r9 evaluation session guide

**Status:** Draft preparation aid. Obtain project owner/adviser approval of the protocol, participant materials, consent/privacy handling, sample, and acceptance criteria before recruiting participants or collecting evaluation data.

**Target:** Owner-confirmed Duno r9, marker `duno-full-release-20261101-r9`. The owner reports r9 was uploaded/extracted. Package SHA-256 recorded locally: `3B87F3EFEF361BA245DA15C1FDC3030993FC0FF5372C5941C04D70EE69CE4736`. Full deployed-file parity and source commit remain unverified. Do not treat later uncommitted local changes as part of r9.

**Purpose:** Provide repeatable, low-risk sessions for student and IT-professional evaluators and capture evidence for Section 7, Results and Findings. This guide is not an ethics approval, consent form, formal ISO/IEC 25010 evaluation result, or substitute for the protocol in [`iso-iec-25010-evaluation-protocol.md`](iso-iec-25010-evaluation-protocol.md).

## 1. Before scheduling participants

- [ ] Adviser/project owner approves the evaluation protocol, participant approach, instrument, pass criteria, data retention, and reporting format.
- [ ] Select the exact environment. Prefer an isolated copy seeded only with synthetic records. If using the hosted r9 site, confirm that evaluation accounts and visible records contain no real student data and do not expose other users' data.
- [ ] Verify the page shows the r9 marker and record the date/time, timezone, browser/version, operating system, device, viewport, network, host/PHP/DB details available, and migration state.
- [ ] Create or verify dedicated synthetic student A, student B, and admin accounts. Do not give participants production credentials or account recovery codes.
- [ ] Seed a small known dataset with expected Focus order, plan totals, timer session, and Aralytics values. Keep a private fixture sheet that contains no real personal data or passwords.
- [ ] Confirm test accounts can be reset between participants without modifying any real user's records. Arrange cleanup before creating records.
- [ ] Prepare the institution-approved information/consent material and a participant code scheme. Do not collect names in result sheets unless the approved method requires them.
- [ ] Confirm every evaluator is 18 or older. Duno's Terms set adult-only eligibility; evaluation participation needs its own adviser-approved information and consent process.
- [ ] Keep raw screenshots/logs in an access-restricted location. Add only sanitized copies to the paper or repository.

If any account displays data that may belong to another person, stop the session, do not capture or copy the data, and notify the project owner. Do not perform load/stress tests, database changes, account deletion, or other destructive security probes on the live service.

## 2. Student session

Use the same task wording and seeded dataset for each student. Let the participant work without coaching unless safety or access requires intervention. Record completion time and assistance, but do not impose a pass/fail threshold until it is approved.

| Task | Participant instruction | Capture | Related requirements |
|---|---|---|---|
| ST-01 Find a next topic | Open Focus, select an unfinished topic, and explain what the displayed score is based on. | Completion, time, assistance, expected/observed rank and explanation; sanitized screenshot if approved. | F-05, F-06 |
| ST-02 Make a plan | Use the provided available study time to generate or review today's plan. | Input minutes, expected/actual block total, unfinished time, and any confusion. | F-07 |
| ST-03 Use the Timer | Start a session, stop/review it, and decide whether to save it. | Timer state, elapsed time, explicit save/discard choice, and saved result. Use only the assigned synthetic account. | F-08 |
| ST-04 Review Aralytics | Open the selected period and find total time, session count, and the subject/topic breakdown. | Compare displayed values with the fixture sheet; record any mismatch. | F-09 |
| ST-05 Try Duno Guide | Choose a study goal and review the suggested steps and any linked outcome. | Selected goal, observed steps, source/label clarity, and participant comments. | F-10 |
| ST-06 Optional check-in | If the approved protocol includes it, submit or skip the optional workload/energy check-in. | Whether the optional nature is clear and whether the participant can skip. Do not record personal ratings in the academic results. | F-11, S-07 |

Do not ask participants to delete accounts or enter personal study information during these tasks.

## 3. IT-professional review

Provide the professional with the same release/environment facts, approved SRS, and a pseudonymized account set. Limit live-host activity to non-destructive, pre-approved checks.

| Task | Review focus | Capture |
|---|---|---|
| IT-01 Role boundaries | Guest, student, and admin route access using assigned accounts and approved URLs. | Route/role, expected and observed denial/allow result, and sanitized status/headers when available. |
| IT-02 Owner isolation | Review the owner-reported r9 A/B test record. Repeat only approved non-destructive cases; use an isolated environment for writes or forged references. | Exact object/route, account pseudonyms, expected/observed access, and whether any data was disclosed or changed. |
| IT-03 Privacy/output review | Check that optional check-ins remain private and do not appear in admin aggregates or the print output. | Surface checked, expected/observed presence, and sanitized evidence. |
| IT-04 Deployment/security review | Review approved path-denial, session/cache, safe-error, migration, and release-provenance evidence. Do not repeat the prohibited `/.env` probe. | Evidence reviewed, gaps, severity rationale, and recommendation. |

Do not run destructive account deletion, database edits, load tests, or unapproved security probes on the hosted database. The owner reports the r9 account-deletion success flow and progression migration; those are existing owner-reported results, not tasks for evaluators to repeat on shared accounts.

## 4. Per-task result record

Use one row per participant/task. Use anonymous participant codes and account pseudonyms only.

| Field | Entry |
|---|---|
| Evaluation/session ID; participant code and role | Pending |
| Date/time and timezone | Pending |
| Release marker/environment; browser/version; device/viewport | Pending |
| Task ID and synthetic fixture ID | Pending |
| Expected result | Pending |
| Observed result | Pending |
| Outcome (completed / completed with assistance / not completed / blocked) | Pending |
| Duration and assistance given | Pending; define timing boundaries first |
| Issue or participant comment (de-identified) | Pending |
| Evidence filename/reference and storage location | Pending |

Do not convert comments into satisfaction scores or pass rates unless the approved instrument and analysis method define how to do so. Report denominators and unrun/blocked tasks separately.

## 5. Performance run

Use the approved performance section of [`iso-iec-25010-evaluation-protocol.md`](iso-iec-25010-evaluation-protocol.md). Before collecting results, agree on the data profile, warm-up, repetitions, measurement boundary, and targets. The protocol's sample latency thresholds are proposed values, not approved or ISO-prescribed targets.

For each approved action (candidate actions: Focus load, Plan view/generation, explicit session save, and Aralytics period view), keep raw trials with timestamp, elapsed milliseconds, success/failure/timeout, cache state, and environment. Report median/min/max; use p95 only if the approved sample size supports it. Do not run load/stress tests against the live host.

## 6. Section 7 evidence checklist

- **System Outputs:** selected sanitized screenshots with figure number, feature, release, date/browser, and a one-sentence explanation. The owner supplied seven distinct Edge screenshots of r9 on 2026-11-02; see [`testing-evidence.md`](testing-evidence.md). The images are currently conversation attachments and need to be saved to the approved private evidence location before manuscript insertion.
- **Testing Results:** the dated automated run, current Playwright run if performed, and the task result tables above. Distinguish local tests from r9 live owner reports and participant evaluation. Never relabel historical results as current.
- **Performance Evaluation:** approved protocol and targets, environment/data profile, raw timing table, summary statistics, failure counts, interpretation, and limitations. No performance findings exist until measurements are collected.
- **Findings:** group observed defects/usability comments by requirement or quality characteristic; document severity, release, evidence reference, disposition, and retest status. Do not claim ISO certification or full standard conformance.

## 7. Closeout

After sessions, reconcile all evidence references, remove or retain synthetic records according to the approved cleanup plan, confirm no real identifiers are in the paper, and publish only approved aggregate results. Keep unresolved defects and untested requirements visible in the final limitations.
