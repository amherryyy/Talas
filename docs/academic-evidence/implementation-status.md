# Implementation status by increment

**Basis:** code and migration files present in the inspected worktree, plus the repository's story and acceptance records. A source-present status is not proof of complete behavior, acceptance, deployment, or current release parity. `NOT VERIFIED` means evidence is absent or insufficient for that stronger claim.

| Increment | Requirements | Evidence/status | Verification status and limits |
|---|---|---|---|
| Secure foundation and accounts | F-01, F-02, S-01, S-04 | Shield configuration, explicit routes, registration recovery, auth views and rate-limit migration exist. | Historical tests and owner-reported hosted auth/role checks; current revision and all edge cases NOT VERIFIED. |
| Subject and topic management | F-03, F-04, S-02, S-03 | Controllers/models/views; migrations for subjects/topics and topic difficulty; owner-scoped CRUD paths. | Historical feature tests; latest source/host parity and full two-student isolation NOT VERIFIED. |
| Search and Focus | F-05, F-06 | Search/filter routes; deterministic `TopicPriorityService` and explanation view. | Priority unit tests recorded; owner reported a small live Focus check. Full edge/tie/combined-filter acceptance NOT VERIFIED. |
| Plan | F-07, S-02 | `DailyPlanService`, plan persistence, item editing and agenda start times; composite owner migration. | Unit/feature test history and limited owner-reported live plan case. Full schedule, conflict, timezone and foreign-owner tests NOT VERIFIED. |
| Timer and study history | F-08 | Timer JS, session controller/model, duration validation and migrations. | Historical PHPUnit/Playwright and owner-reported Timer check; all live lifecycle cases NOT VERIFIED. |
| Aralytics and print | F-09 | Summary service/view and print behavior; private check-in distributions are separate. | Historical service/feature tests and 2026-11-01 owner report of summary/chart/print. Exact data values and full privacy scope NOT VERIFIED. |
| Guide/suggestions and outcomes | F-10 | Curated content plus outcome association code; current 2026-11-01 worktree adds interactive goal selection. | Prior suggestion tests exist; newest interactions and current release behavior NOT VERIFIED. |
| Check-ins | F-11, S-07 | Owner-scoped optional ratings, edit/delete, private summary services/views; migration. | Feature and local browser tests recorded; host cross-user/admin/print evidence incomplete. |
| Admin aggregates | F-12, S-01/S-07 | Role-protected aggregate dashboard and summary service. | Historical tests and owner reports; fresh count comparison/route test NOT VERIFIED. |
| Responsive UI/onboarding/localization/catalog | F-13 and accepted supplemental decisions | Responsive CSS, navigation, onboarding, English/Taglish/Filipino resources, static catalogs and importer services. | Historical visual/browser notes vary by increment. Latest acceptance and catalog currency NOT VERIFIED. |
| Progression | F-14 | XP, streak and deterministic unlock service; daily streak migration. | Unit/feature tests recorded; current host behavior NOT VERIFIED. |
| Bounded PWA | F-15 | Manifest, icons, service worker, offline page, network notice and package inclusion in current tree. | r4 surface smoke recorded. Owner reports r5 live; exact r5 file parity, install prompt, offline interactions and cache allowlist NOT VERIFIED. The r6 ZIP remains local but is stale after the final role-denial header fix; no replacement package exists. |
| Security/error/deployment hardening | S-03–S-10 | Escaping, CSRF/session configuration, headers, deny rules, production error views, package builder and static fallback files. | Some historical source checks and host observations. Response header matrix, exact path denial behavior, branded host fallback and safe-error behavior NOT VERIFIED. |

## Limitations and unfinished work

- Current application modifications are uncommitted on `main`; no commit identifies the full current source snapshot.
- The Oct 31 live r4 browser observations precede the Nov 1 owner report of r5 live acceptance/marker. No current release chronology conflict remains; exact deployed-file parity is still unverified. The r6 ZIP was built from the dirty worktree, is stale after the final role-denial header fix, and is not uploaded.
- “Implemented” stories may still need release-specific browser, security, or host acceptance. The open cases are enumerated in `docs/acceptance-verification-plan.md`.
- The data collection/ISO evaluation claims in academic materials must remain pending until protocol and actual evidence are supplied.
