# Methodology support notes for the academic manuscript

These are evidence notes for later integration. They do not rewrite the manuscript and must not be presented as participant findings or completed evaluation where the underlying evidence is absent.

## Methodology A: Project Development Process

**Evidence-supported draft:** The repository describes an iterative/incremental, Agile-guided process using small vertical feature increments, explicit story acceptance criteria, implementation, automated/manual verification, review, owner acceptance and release gates. `docs/ai-agent-workflow.md` documents the state flow and role responsibilities. `docs/development-blueprint.md`, feature stories and `docs/ai-use-log.md` record scope and decisions. Git commits from October 2026 provide dates for committed increments through 2026-10-29.

**Qualify:** Do not claim sprint ceremonies, team size, meeting frequency or instructor approvals unless separately evidenced. After 2026-10-29, many implementation changes are on a dirty working tree and are not represented by a release commit. The development process evidence supports a process description, not a claim that every story passed every gate.

## Methodology B: Data Collection Methods

**Evidence-supported draft:** The SRS alignment and traceability records identify a supplied proposal and questionnaire/summary document and describe survey results as descriptive, transcribed evidence. The alignment record reports 37 submissions, one non-consenting respondent with other items blank, and 36 included responses. It also flags a Grade 9 eligibility answer and a discrepancy between questionnaire rating anchors and the response export.

**Qualify:** This audit did not inspect or recalculate raw response rows. The survey analysis values are not independently verified here. Participant recruitment, sampling frame, dates, consent procedure, instrument validation, respondent demographics beyond recorded summaries, and raw-data integrity are NOT VERIFIED from this audit. Do not invent participant counts or treat stated preferences as approved requirements. Have the project owner verify original files, eligibility, instrument version and ethics/consent details before paper use.

## Methodology C: System Design and Implementation

**Evidence-supported draft:** Describe a server-rendered CodeIgniter 4 MVC web application with Shield authentication, PHP, MySQL/MariaDB, CSS and small plain JavaScript modules. Explain explicit routes, role/session filters, controllers, owner-scoped models and deterministic services. Use the source-grounded schema/architecture in `docs/system-design-paper-pack.md`, with migrations as schema-history evidence. Features and implementation deltas are in `implementation-status.md` and `requirements-traceability.md`.

**Qualify:** Mark relationship models as reconstructed from code/migrations. Do not imply normalized institutional curriculum tables, MariaDB row-level security, an AI recommendation engine, a native application, offline authenticated records, or a live/deployed behavior not evidenced. Use revision-identified screenshots for the manuscript; wireframes are not screenshots.

## Methodology D: System Testing and Evaluation

**Evidence-supported draft:** Report the existence of PHPUnit unit/feature tests, a Playwright responsive spec and GitHub Actions checks. Historical runs and selected manual journeys are recorded with dates in `docs/ai-use-log.md`, stories, `acceptance-verification-plan.md` and `deployment.md`. Testing evidence is summarized in `testing-evidence.md`.

**Qualify:** Separate automated verification from user acceptance, host smoke tests and formal quality evaluation. Results are tied to their historical snapshots; they were not rerun in this audit. The ISO/IEC 25010:2023 evaluation remains pending, with no protocol or results established. Do not describe test-plan rows as passed tests.

## Methodology E: Data Analysis and Statistical Treatment

**Evidence-supported draft:** Product outputs are deterministic descriptive calculations: priority score/rank from student-entered topic data; time-bounded plan allocation; counts/totals/averages over saved sessions; private counts/distributions of optional self-ratings; and XP/streak/unlock rules. Their services and requirement stories define formulas. These are system calculations, not inferential statistics.

**Qualify:** No new system measurements or survey statistics were calculated in this audit. Do not claim performance percentiles, reliability rates, security pass rates, statistical significance, learning gains, grades, causal effects or survey findings without source data and a reproducible analysis. Before a quality evaluation, specify the descriptive statistics and denominators for each measure, quality characteristic, test conditions, missing-data handling and acceptance criteria.

## Human confirmations needed for paper accuracy

- Locate and confirm the approved SRS version and formal approval record, or explicitly identify `requirements.md` as the operative project baseline without calling the draft SRS approved.
- Verify proposal and survey source files, respondent eligibility, questionnaire version/anchors and consent/sampling details.
- Decide the evaluation protocol and selected ISO/IEC 25010:2023 characteristics/measures before collecting or reporting results.
- Reconcile the deployed application revision and current release state.
- Confirm which dated owner reports may be included as project records and retain sanitized evidence artifacts.
