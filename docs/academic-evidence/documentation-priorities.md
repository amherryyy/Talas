# Documentation and evidence priorities

Priorities address evidence needed for the three project objectives. The ranking reflects academic/release impact, not implementation difficulty.

## P0 — essential for submission or compliance evidence

| Gap | Why P0 | Next action / owner input |
|---|---|---|
| Approved requirements/SRS identity and approval | The paper needs an authoritative baseline. Repository requirements are owner-approved, but the supplied SRS is explicitly draft and no formal approval record was located. | Project owner/adviser confirms approved SRS version/date or authorizes the paper to cite repository `requirements.md` as the operative requirements baseline. Do not change scope by inference. |
| ISO/IEC 25010:2023 evaluation protocol and results | Objective 3 is not demonstrated by source code or functional tests. | Human-approved protocol specifying characteristics, methods/instruments, environments, sample/test data, thresholds, analysis and reporting. Execute and preserve actual results; until then state pending. |
| Release and deployment identity | Owner reports r5 live; r7 is a local package from clean source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3`. The r5 marker does not establish file parity; r6 is stale. | Upload only after approval, then record the uploaded package hash, live marker and migration rows; capture sanitized evidence and run remaining acceptance on that release. |
| Core requirements acceptance evidence | Existing plan has many unrun cases and only narrow reported host journeys. | Execute relevant F/S acceptance cases using synthetic accounts; log source revision, DB state, exact result and evidence. Prioritize auth/roles, ownership/privacy, core Focus/Plan/Timer/Aralytics and release controls. |
| Survey/proposal provenance | Data collection methods and survey summaries are not fully verified. | Owner verifies original proposal/questionnaire/response exports, eligibility, consent, rating anchors and summary calculations before paper inclusion. |

## P1 — technical completeness and reproducibility

| Gap | Follow-up |
|---|---|
| Test traceability/currentness | Tie each captured automated result to a commit/package and list suite/command, runtime, count and limitations. Avoid presenting old totals as current. |
| Host security evidence | Capture path-specific denial outcomes, response headers, safe errors, cache headers, session behavior and PWA cache allowlist on the declared release. Respect the existing instruction not to repeat the `/.env` probe. |
| Database reproducibility | Preserve sanitized migration status/schema metadata for the tested host and local synthetic environment; map migration version to app package. |
| Responsive/accessibility/print record | Obtain owner-approved browser/viewport matrix; record keyboard, focus, screen state, print and error views. |
| Dependency audit | `composer audit --locked` passed on 2026-11-01 after clearing injected proxy variables for the process. Tie the result to the lockfile revision used for the final package. |
| Implementation timeline | Keep later work linked to accepted story IDs, reviewed commits and exact test/deployment records. Current post-2026-10-29 snapshot is uncommitted. |

## P2 — optional after submission

- Add a maintained evidence index linking each requirement to artifact locations and release revisions.
- Add a report-generation template for sanitized browser, migration and test evidence.
- Improve long-term database-backed CI when an isolated synthetic database job can safely run migrations.
- Refresh overview and paper status notes after each owner-reported or verified release; distinguish owner reports from independent host evidence.

## Minimum paper-ready gate

Before claiming objective completion, have one human-approved requirements baseline; a revision-identified implementation/design account; actual acceptance and security evidence tied to that revision; and an executed, documented quality evaluation protocol for the selected ISO/IEC 25010:2023 characteristics. Any missing item should be described as pending or NOT VERIFIED.
