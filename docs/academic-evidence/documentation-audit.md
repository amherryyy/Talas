# Duno documentation and development evidence audit

**Audit snapshot:** 2026-11-01 (Asia/Manila). **Scope:** read-only repository inspection plus new evidence notes. This folder records the repository as inspected; it does not establish that the working tree stayed unchanged after inspection. The working tree was already heavily modified, and those changes were not overwritten.

## Executive finding

The repository has a substantial project record: an owner-approved requirements file, story-level decisions, development and AI-use records, a CodeIgniter design pack, migration history, acceptance procedures, CI configuration, deployment notes, and historical test results. The primary academic gap is that the separate approved SRS/approval record and formal ISO/IEC 25010 evaluation protocol/results are not established. A later owner report says r5 is live and passed a narrow Aralytics check; that report supplies the chronology missing from the earlier r4 observation, but there is still no independent confirmation of uploaded-file parity or a single clean deployed source revision. The local r6 ZIP is dirty-tree based and stale after the final role-denial header fix; no replacement package exists.

## Evidence inspected

- Project instructions and entry points: `AGENTS.md`, `README.md`, `docs/development-blueprint.md`, `docs/ai-agent-workflow.md`, and `docs/version-control.md`.
- Requirements and design: `docs/requirements.md`, `docs/DUNO_SRS_ALIGNMENT_DRAFT.md`, `docs/DUNO_SRS_TRACEABILITY.md`, `docs/system-design-paper-pack.md`, and feature story files under `docs/`.
- Verification/release records: `docs/acceptance-verification-plan.md`, `docs/deployment.md`, `docs/database-setup.md`, `docs/ai-use-log.md`, `tests/README.md`, `.github/workflows/ci.yml`.
- Implementation inventory: `app/Config/Routes.php`; controllers, models, services, migrations, views, `public/`, `tests/`.
- Version history: local Git log through `310c5e5` dated 2026-10-29. At inspection `main` was checked out, tracking `origin/main`, with extensive modified and untracked files. The log does not include the uncommitted current worktree as a commit.

No tests, database commands, browser sessions, host requests, external services, or internet browsing were used for this audit. Existing recorded outcomes below are historical evidence, not runs performed for this audit.

## Documentation completeness

| Area | Existing evidence | Audit assessment |
|---|---|---|
| Product scope/requirements | `requirements.md`, story decisions, `development-blueprint.md` | Strong repository-approved baseline. Formal approved SRS artifact and dated approval record are not present in the inspected repository. |
| Proposal/survey alignment | `DUNO_SRS_ALIGNMENT_DRAFT.md`, `DUNO_SRS_TRACEABILITY.md` | Useful conflict analysis. The draft explicitly remains unapproved; supplied response summaries were transcribed, not recalculated from raw responses. |
| Design | `system-design-paper-pack.md`; routes, controllers, services, models and migrations | Substantial implementation-grounded architecture/schema notes. ERD and wireframes are design representations, not deployed screenshots or independent production-schema inspection. |
| Development process/history | `ai-agent-workflow.md`, `ai-use-log.md`, story documents, Git history | Strong dated record for many implementation increments. Git history ends at committed 2026-10-29; later 2026-10-30/31 and 2026-11-01 work is represented by uncommitted files and log entries. |
| Testing | PHPUnit/feature/unit files, one Playwright spec, CI workflow, acceptance plan and story logs | Historical automated results are recorded; acceptance plan is not itself execution evidence. Current host acceptance and full ISO evaluation are incomplete. |
| Deployment | `deployment.md`, `database-setup.md`, package builder and SQL upgrade records | Detailed timeline; later r5 owner report follows the earlier r4 observation. Host migration ledger and package marker are owner-reported, not a substitute for file parity or complete acceptance. |

## Main gaps and contradictions

1. **Approved SRS baseline:** The file `DUNO_SRS_ALIGNMENT_DRAFT.md` says `requirements.md` is normative and the supplied SRS v1.0 is a candidate draft. No approved SRS and approval record was found in the repository inventory. Do not label the draft approved without human confirmation.
2. **ISO/IEC 25010:** Existing notes say evaluation is pending; no instrument, selected edition, participant/test conditions, thresholds, data, statistical plan, or results are recorded. The proposal may mention quality characteristics, but that is not an evaluation.
3. **Release identity:** The Oct 31 r4 browser observation precedes the Nov 1 owner-reported r5 marker and Aralytics acceptance. Current docs record r5 as owner-reported live. The unuploaded r6 ZIP is stale after the final role-denial header fix and no replacement exists. Exact deployed-file parity remains unverified; a clean source revision and post-upload package hash are still needed.
4. **Security-path evidence:** Prior host output for `/app/Config/Paths.php` was provider-generated 403; `/.env` displayed the offline recovery page. The latter is not an HTTP denial. The deployment note says not to repeat that probe. Do not claim route-specific HTTP denial for `/.env`.
5. **PWA and UI acceptance:** Manifest/service-worker evidence exists for r4, but full install/offline/cache behavior and current error-page rendering are pending. Viewport/browser matrix is not formally approved.
6. **Test-currentness:** All logged test results belong to particular earlier snapshots. This audit did not rerun them, and the active worktree may change during parallel development.
7. **Survey provenance:** The SRS review says it transcribed summary values from the supplied survey analysis without recalculating raw response rows; eligibility and rating-anchor discrepancies require human confirmation.
8. **Historical test-count variation:** Repository records cite both 67 tests/612 assertions for the two CI matrix runs and 67 tests/613 assertions for a separate full-suite run. These may be different runs, but their source revisions and exact commands should be retained when quoting a count.

See [requirements traceability](requirements-traceability.md), [testing evidence](testing-evidence.md), [deployment evidence](deployment-evidence.md), and [documentation priorities](documentation-priorities.md).

## Evidence classification

- **Approved requirements:** `docs/requirements.md` F-01–F-15 and S-01–S-10 plus approved linked story decisions.
- **Verified implementation (source):** Code and migration files exist in the inspected worktree. This demonstrates implementation artifacts, not successful runtime behavior or deployment.
- **Reported/historical verification:** Test results and manual observations written in existing records; cite their recorded date and scope, and label owner reports accordingly.
- **Pending / NOT VERIFIED:** No adequate evidence in the repository for the claim at issue. Do not infer completion from file presence, package construction, owner acceptance of a broader requirement, or a marker string.

## Snapshot caveat

At the original audit snapshot, the checkout contained numerous modified application, test, deployment, and documentation files plus untracked files, including source changes related to features discussed in the evidence. Per that audit brief, none were changed during the original read-only inspection. The later addendum and owner-requested status reconciliation below are subsequent edits. If source or status documentation changes during active development, recheck it before using this audit as a release or paper claim.

### Addendum: current release-record reconciliation (2026-11-01)

At the project owner's request, current repository and evidence notes were reconciled after the original audit snapshot. The owner-reported r5 marker/Aralytics acceptance is now ordered after the Oct 31 r4 browser checks; the r6 package hash, base commit, and dirty-worktree provenance are recorded. This removes the documentation's temporal r4/r5 conflict without upgrading owner-reported evidence to independent verification. Exact r5 file parity remains unverified; the r6 ZIP is stale after the final role-denial header fix and has not been uploaded. A clean source revision is still needed. See `docs/deployment.md`, `docs/version-control.md`, and `docs/acceptance-verification-plan.md` for current details. The assistance is logged in `docs/ai-use-log.md`.
