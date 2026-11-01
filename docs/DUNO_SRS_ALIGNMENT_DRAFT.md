# Duno Software Requirements Specification

**Document status:** Aligned working version; repository requirements are the normative baseline. Formal stakeholder/adviser approval is not recorded.

**Prepared:** 30 October 2026
**Version:** 1.1 (aligned working copy)

**Project:** Duno study planning, tracking, and academic activity summaries

**Authority:** Owner-approved `requirements.md` and accepted decisions in this repository; the supplied proposal and survey analysis are supporting sources.

## 1. Purpose and document authority

This SRS organizes the owner-approved Duno scope and aligns the supplied proposal and survey evidence to it. The repository `requirements.md` (F-01 to F-15 and S-01 to S-10) is the normative baseline. The supplied `DUNO_SRS_v1.0.docx` contains reconstructed candidate requirements; where these differ from `requirements.md`, the repository baseline controls unless the owner approves a later change.

The supplied SRS v1.0 is explicitly marked as a draft. Its additional functional and nonfunctional statements are candidates, while survey findings are evidence rather than approval. This aligned version preserves F-01 to F-15 and S-01 to S-10 as normative identifiers and lists proposal/SRS additions separately with a disposition.

## 2. Objectives and source limitations

The owner supplied `Duno App SysFun Proposal.docx` on 30 October 2026. Its objectives are reproduced below. The file does not state a formal approval date or sign-off; that provenance remains to be recorded.

**General objective**

> The general objective of the project is to develop a mobile-first academic companion that enables college students to organize academic responsibilities, generate personalized study plans, access appropriate study guidance, and monitor their study activities through an integrated and accessible platform.

**Specific objectives**

1. Develop an academic organization and personalized planning system that enables students to manage subjects, topics, academic tasks, and deadlines; identify study priorities through Duno Focus; and generate adjustable study schedules through Duno Plan based on available study time, task difficulty, importance, and progress.
2. Integrate study guidance and activity tracking features that recommend appropriate study methods through Duno StudyGuide using curated educational references and relevant learning outcomes, while allowing students to record study sessions through a built-in timer and optionally reflect on their academic workload and study habits.
3. Implement an academic analytics and management platform that presents study patterns, time distribution, and task completion through Aralytics; supports searchable records, printable reports, secure user accounts, and basic administrative functions; and evaluate the developed system using the ISO/IEC 25010 software product quality model in terms of functional suitability, interaction capability, performance efficiency, reliability, security, and other relevant quality characteristics.

The approved product goal in [`requirements.md`](requirements.md) covers most of these outcomes. The comparison below makes the known scope differences explicit; it does not silently add fields or behavior to the implementation.

| Proposal objective area | Current project evidence | Alignment |
|---|---|---|
| Mobile-first, integrated, accessible academic companion | Responsive browser UI, shared navigation, onboarding, and accessibility-oriented controls exist; a current full-device/accessibility review is outstanding. | Partially implemented; fresh acceptance evidence is missing. |
| Subjects, topics/tasks, deadlines, Focus priorities, adjustable Plan | Subject/topic CRUD, due dates, approved priority rules, difficulty-aware ranking, and adjustable plans exist. Difficulty is Low/Medium/High, defaults to Medium, and contributes 1/2/3 points to Focus; Plan uses the resulting rank. | Implemented in the current worktree; the owner verified hosted migrations 000001 (batch 12) and 000002 (batch 13) on 2026-10-31. Deployed revision and route-specific acceptance remain unverified. |
| Study guidance using curated references and relevant learning outcomes | Four general suggestions are curated from an educational practice guide. Students may select their own NVSU-linked BSCS, BSIS, or BSIT subject to see related CHED CMO No. 25, s. 2015 program outcomes. | Partially aligned: the source verifies program-level outcomes, not course-specific outcomes. The Duno association is editorial and clearly labeled; programs without verified outcomes remain unmapped. |
| Timer and optional reflection on workload/study habits | Explicitly saved study sessions and optional private workload/energy ratings are implemented. The self-reports are not a measure of health and do not currently cover free-text study-habit reflection. | Partially aligned; the paper should describe only current ratings and session data. |
| Aralytics, searchable records, printable reports, secure accounts, admin functions | Owner-scoped analytics, search, print, authentication, and aggregate-only admin dashboard exist in source. The owner reports that the live site is available and all pages were checked on 2026-10-31. | Implemented in source; detailed hosted acceptance and deployed revision are unverified. |
| ISO/IEC 25010 quality evaluation | The supplied proposal names characteristics and the owner confirmed that evaluation remains planned and has not yet been conducted. | Evaluation status: Pending. No results are claimed. Record the selected edition, method, participants/test conditions, measures, acceptance criteria, and results before reporting an evaluation. |

The proposal references ISO/IEC 25010 and names functional suitability, interaction capability, performance efficiency, reliability, security, and other relevant characteristics. The owner confirmed that evaluation is planned but has not yet been conducted. No results are claimed. Before evaluation, record the selected edition, measures, instrument/method, participants or test conditions, acceptance criteria, and analysis plan. The proposal does not state these details.

The supplied `Duno App SRS survey results.docx` contains the questionnaire text and a descriptive analysis chapter. Its figures are transcribed in Section 9 and have not been recalculated from raw response rows. The original earlier FR/NFR baseline referenced in SRS v1.0 and formal proposal approval record were not supplied.

## 2.1 Proposal and survey conflicts with the approved baseline

| Proposal statement or survey preference | Repository-approved baseline/current implementation | SRS disposition |
|---|---|---|
| Mobile-first installable PWA, service worker, and offline access to selected content | The owner approved a bounded installable PWA on 2026-10-30: public static shell assets and an offline recovery page only; private pages and study data remain network-only. | Installability and static offline fallback are approved as F-15. Offline study data, queued writes, and synchronization remain excluded. |
| Store estimated study duration on each task and use it for scheduling | Topic fields are due date, importance, difficulty, and progress. Plan generates 25-minute topic blocks, shortens the last block to fit, and allows adjustment. | Not in the approved baseline. |
| Recommend techniques based on a specific topic and its intended learning outcomes | Four static sourced suggestions are available. Verified program outcomes are shown for supported NVSU-linked BSCS, BSIS, and BSIT subjects. The suggestion-to-outcome link is editorial and program-level. | Partially implemented. Topic-specific recommendations and course-level outcomes remain unverified/out of scope. |
| Save optional notes with a study session | F-08 stores duration, subject, optional topic, and completion time; it has no notes field. | Not in the approved baseline. |
| Check-ins record workload, motivation, focus, energy, or written reflection | F-11 covers private workload and energy ratings, edit/delete, and descriptive summaries. | Narrowed to workload and energy ratings. No motivation/focus assessment, free text, mental-health claim, or pop-up intervention. |
| Administrators manage accounts and curate recommendations | F-12 is an aggregate-only dashboard. It does not expose identities, individual records, check-ins, account-management actions, or content editing. | Keep aggregate-only admin scope. |
| Reminders and offline access are desired by survey respondents | The owner later approved only installability and a public static offline shell; reminders and offline study data remain excluded. | The survey remains evidence, not approval. F-15 approval is a separate owner decision; reminders and offline records are not approved. |
| Proposal anticipates Tailwind and frontend build tooling | The approved application uses custom CSS and small plain JavaScript modules without a frontend framework or runtime build dependency. | Describe the actual project stack in this SRS; do not present proposed Tailwind tooling as implemented. |
| ISO/IEC 25010 software evaluation | The owner confirms evaluation is planned but has not been conducted. | Pending. No evaluation results or quality claims are asserted. |

## 3. Product scope

Duno is a browser-based client/server application for individual students, with a restricted administrator role. Students manage subjects and topics, review deterministic priority guidance, build and adjust study plans, time and save study sessions, view Aralytics, use static study suggestions, and optionally record private workload/energy ratings. The app also provides onboarding, progression, and a bounded set of curriculum references.

The application uses CodeIgniter 4, CodeIgniter Shield, PHP server-rendered views, CSS, plain JavaScript, and MySQL/MariaDB. Registration verification email is sent through configured SMTP. Curriculum catalogues are curated application data; they are not live synchronized, complete national datasets, or a relational curriculum management service.

The approved MVP excludes generative AI, grade prediction, deadline reminders, calendar integration, class/group accounts, and native mobile apps. The owner has approved F-15: installability and a static public offline fallback. Private pages, study records, offline writes, and synchronization remain excluded. Static study suggestions are not topic-specific or personalized recommendations.

## 4. User classes

| User class | Permitted use |
|---|---|
| Guest | View the public start page, select a browser-local language, register, and sign in. A guest cannot access student data. |
| Student | Manage only that student's subjects, topics, plans, sessions, optional check-ins, analytics, and progression. |
| Administrator | View approved aggregate totals only. The dashboard excludes student identity, individual records, check-in values/patterns, exports, and drill-down. |

## 5. Functional requirements

The complete owner-approved wording and security acceptance criteria are in [`requirements.md`](requirements.md). The table below summarizes each approved requirement and gives its current implementation status. The status describes repository evidence, not current hosted acceptance.

| ID | Approved requirement summary | Current implementation status | Main evidence |
|---|---|---|---|
| F-01 | Public start, student registration with numeric email verification, session sign-in/out, unverified-user denial, and rate-limited recovery | Implemented in source; current hosted email and route behavior unverified | [Authentication controller/config and tests](../tests/feature/AuthenticationFlowTest.php) |
| F-02 | Separate student and administrator access with server-side authorization | Implemented in source; current hosted route boundary unverified | [Routes and role filter](../app/Config/Routes.php), [admin tests](../tests/feature/AdminDashboardTest.php) |
| F-03 | Student-owned subject create, view, edit, and delete | Implemented; catalogs remain bounded reference data | [Subject tests](../tests/feature/StudentSubjectsTest.php) |
| F-04 | Student topic management with due date, importance, difficulty, and progress | Implemented in current worktree | [Topic tests](../tests/feature/StudentTopicsTest.php) |
| F-05 | Owner-scoped search and filters for subjects/topics | Implemented | [Subject tests](../tests/feature/StudentSubjectsTest.php), [topic tests](../tests/feature/StudentTopicsTest.php) |
| F-06 | Deterministic, explainable topic priority using approved rules | Implemented | [Priority service tests](../tests/unit/TopicPriorityServiceTest.php), [approved rules](priority-story.md) |
| F-07 | Daily plans within available time; edit, reorder, remove, progress, and date-based scheduled agenda blocks | Implemented in source; latest host migration/agenda acceptance requires fresh release verification | [Plan tests](../tests/feature/StudentPlansTest.php), [plan model](../app/Models/StudyPlanModel.php) |
| F-08 | Timer with review and explicit save; saved sessions can be edited or deleted | Implemented in source; live browser flow unverified | [Session tests](../tests/feature/StudentStudySessionsTest.php) |
| F-09 | Owner-scoped descriptive Aralytics, accessible numeric equivalents, and printable study report | Implemented in source; fresh visual/print review remains | [Aralytics tests](../tests/feature/StudentAralyticsTest.php), [service tests](../tests/unit/AralyticsServiceTest.php) |
| F-10 | Curated, source-linked study suggestions with verified program outcomes when available | Implemented for the verified NVSU-linked BSCS/BSIS/BSIT scope | [Suggestion tests](../tests/unit/StudySuggestionsTest.php), [dashboard tests](../tests/feature/StudentDashboardTest.php), [mapping source](../app/Content/StudyGuideOutcomes.php) |
| F-11 | Optional private workload/energy ratings; edit/delete; student-only summaries; no automatic expiry | Implemented in source; legal/privacy policy review remains separate | [Check-in tests](../tests/feature/StudentCheckInsTest.php), [privacy story](private-check-in-story.md) |
| F-12 | Administrator aggregate totals only | Implemented in source; current hosted access unverified | [Admin tests](../tests/feature/AdminDashboardTest.php) |
| F-13 | Responsive, keyboard-usable experience; readable print; skippable/replayable onboarding; contextual navigation | Partially implemented; current visual, keyboard, and browser-matrix acceptance remains | [Responsive spec](../tests/e2e/responsive.spec.js), [onboarding story](student-onboarding-story.md) |
| F-14 | 1,000 XP per saved study minute; 5,000 XP on first authenticated student visit per app-local date; streak and deterministic unlocks | Implemented locally; new scale and host migration unverified | [Progression tests](../tests/unit/StudyProgressionServiceTest.php), [streak tests](../tests/feature/StudentStreakTest.php) |
| F-15 | Installable PWA with public static offline shell and recovery page | Partially implemented; source syntax and manifest checks passed. Package output, browser installation/offline behavior, and host responses remain unverified. Private pages/data and writes stay online-only; no migration is required. | [Manifest](../public/manifest.webmanifest), [service worker](../public/sw.js), [offline page](../public/offline.html), [PWA story](pwa-story.md) |

## 6. Security and cross-cutting requirements

The approved security requirements S-01 through S-10 are listed in [`requirements.md`](requirements.md#cross-cutting-security-requirements). The application uses framework authentication and CSRF protections, explicit routes and role checks, server-side validation, escaped output, owner-scoped queries, production-safe configuration, security headers, and no-store responses for dynamic pages.

| ID | Requirement | Implementation/evidence status |
|---|---|---|
| S-01 | Framework authentication and server authorization | Shield, explicit routes/filters, and feature tests exist; current hosted check is unverified. |
| S-02 | Owner checks for records and nested references | Enforced in application queries/models and covered by cross-user tests. This is application-enforced row-level access control; the target MySQL/MariaDB does not use native RLS policies. |
| S-03 | Server validation, output escaping, parameterized database operations | Framework validation/query builder and escaped PHP views are used; representative tests exist; a complete fresh audit remains. |
| S-04 | CSRF and secure session settings | Global CSRF and session/cookie configuration exist; current HTTPS host behavior is unverified. |
| S-05 | No secrets or real student data in source control/CI | Policy and ignore/package rules exist; no new secret scan was run for this draft. |
| S-06 | Private application paths denied over HTTP | Package/root deny rules exist; current direct-URL host verification is blocked by the provider 403. |
| S-07 | Check-ins minimized and private | Owner-scoped records; tests cover privacy exclusions from admin/report paths. |
| S-08 | Production errors do not reveal internals | Production configuration and error views exist; hosted safe-error behavior is unverified. |
| S-09 | Security headers and safe DB error behavior | Filters/configuration exist; deployed response headers and DB error behavior are not freshly measured. |
| S-10 | No-store policy for dynamic application responses | Filter exists; deployed response headers are not freshly measured. |

Database constraints strengthen application checks where defined. Study-session references have composite constraints for owner/subject and topic/subject consistency. The plan-item/topic relation is not bound to plan ownership with a composite foreign key, so the application check remains significant. See the [schema and ERD notes](system-design-paper-pack.md#4-database-design).

## 7. Nonfunctional requirements and acceptance gaps

The draft NFR-01 through NFR-10 statements are candidate quality requirements, not approved replacements for S-01 through S-10. The approved baseline gives security and privacy checks and requires responsive, accessible behavior, but it does not set numeric performance, availability, or browser-compatibility thresholds.

| Quality area | Current design/evidence | Decision or verification still needed |
|---|---|---|
| Privacy and confidentiality | Optional check-ins are owner-scoped, self-reported, editable/deletable, and excluded from admin and print. | Confirm applicable institutional/jurisdictional policy, privacy notice, account deletion, and backup retention/access. |
| Usability/accessibility | Responsive views, keyboard-oriented controls, onboarding, print stylesheet, and chart equivalents exist in source. | Approve a viewport/browser matrix and accessibility review method; repeat keyboard and visual review on the current revision. |
| Performance | Plain server-rendered PHP and small local scripts; no approved numeric target or fresh benchmark. | Approve workload, data size, response-time percentiles, and test environment before treating performance as a release gate. |
| Browser/device compatibility | Responsive-web intent and prior local review are recorded. | Approve supported browser/version and phone/tablet/desktop matrix; test current core journeys. |
| Availability/connectivity | Network-only private pages and writes; public shell assets and offline recovery screen are cached under F-15. | Define measurable availability and error behavior. No availability target or offline data guarantee is claimed. |
| Maintainability/release | Migrations, feature/unit tests, CI workflow, and release documentation exist. | Record fresh test/CI results, migration parity, deployed revision, backup/restore evidence, and smoke checks for each release. |

No numeric quality target is invented in this draft. Survey concerns inform which measures may be useful but do not establish acceptance thresholds.

## 8. External interfaces and data

### 8.1 User interface

Students use public/authentication pages and the Duno Focus, Plan, Timer, Guide, and Aralytics areas. Administrators use a restricted aggregate dashboard. English is the default interface language; Taglish and Filipino selections are stored in the current browser. The system-design packet contains low-fidelity page wireframes; these must be labeled as design wireframes, not screenshots of production.

### 8.2 Software interfaces

- **Database:** MySQL/MariaDB via CodeIgniter's MySQLi/query-builder configuration. The exact live DB version is unverified.
- **Authentication:** CodeIgniter Shield plus application student/admin authorization.
- **Email:** Configured SMTP for numeric registration verification. The current live service/host path is not freshly verified.
- **Curriculum sources:** Curated NVSU, CHED, and DepEd reference content in application code, with source links. There is no live curriculum API synchronization.

### 8.3 Data entities

The relational design includes framework user/auth records; student-owned subjects and topics; date-based study plans and plan items; saved study sessions; optional workload/energy check-ins; student streak/progression records; and registration-verification limits. Catalogues are code-defined; they are not separate institution, program, or curriculum database tables. The detailed ERD and key/relationship notes are in the [system-design packet](system-design-paper-pack.md#4-database-design) and derive from repository migrations, not a fresh production database export.

## 9. Survey evidence and limits

The supplied `Duno App SRS survey results.docx` includes the questionnaire and a descriptive analysis of its responses. It reports 37 submissions; one respondent did not consent and left the remaining questions blank, so 36 responses were included. The supplied analysis contains summary tables but this SRS did not recalculate values from individual response rows.

| Survey result reported in the supplied analysis | Result | Requirements discussion supported |
|---|---:|---|
| Common current organization methods | Notes app 18/36 (50.0%); memory 12/36 (33.3%); productivity/to-do app 11/36 (30.6%); physical planner 11/36 (30.6%) | Consolidated task organization |
| Most common reported challenges | Overlapping deadlines 21/36 (58.3%); prioritization 19/36 (52.8%); focus 17/36 (47.2%); study habits 13/36 (36.1%) | F-04, F-06, and study workflow |
| Most common task-selection approaches | Nearest deadline 13/36 (36.1%); most important 9/36 (25.0%); most difficult 7/36 (19.4%) | Supports transparent, adjustable ranking; does not establish a scoring formula |
| Sometimes, often, or always difficulty managing multiple academic tasks/deadlines | 31/36 (86.1%) | Subject/task organization and planning |
| Prioritization feature rated 4 or 5; mean | 30/36 (83.3%); 4.25/5 | F-06 discussion; does not validate the specific formula |
| Personalized planner rated 4 or 5; mean | 27/36 (75.0%); 4.11/5 | F-07 discussion |
| Study-method recommendations rated 4 or 5; mean | 29/36 (80.6%); 4.36/5 | F-10 discussion; does not prove accuracy or efficacy |
| Timer/tracker rated 4 or 5; mean | 26/36 (72.2%); 3.89/5 | F-08 discussion; lowest relative mean among the six items |
| Progress dashboard rated 4 or 5; mean | 29/36 (80.6%); 4.22/5 | F-09 discussion |
| Reminder preference; offline-access preference | 22/36 (61.1%); 19/36 (52.8%) | Survey evidence only. Reminders and offline study data are not approved; limited F-15 installability/static fallback was approved separately by the owner. |
| Search/filter and printable reports selected | 16/36 (44.4%) each | Supports discussion of F-05 and F-09 |
| Privacy/security concern; slow performance; interface complexity | 24/36 (66.7%); 11/36 (30.6%); 10/36 (27.8%) | Security, performance, usability risks; thresholds require separate approval |
| Recommendation accuracy and internet connectivity concerns | 9/36 (25.0%); 7/36 (19.4%) | Avoid overclaiming recommendation personalization; disclose browser/network dependency |
| Optional written suggestion item | 14/36 answered; reported themes included reminders, task sorting, and offline access | Small optional subset; theme counts are not prevalence or requirement approval |
| Any access option if convenient/responsive; smartphone-installed option | 11/36 (30.6%); 14/36 (38.9%) | Supports discussion of responsive/installable access; F-15 approval comes from the owner, not these survey results. |

The results are descriptive of this respondent group. The analysis identifies no sampling frame and includes one Grade 9 response despite the college-student target. Multi-select percentages are non-exclusive; the optional written question received 14 answers and its theme counts are not prevalence estimates. The document includes questionnaire text with verbal rating labels, while its analysis says the response export lacked those labels. Confirm that the displayed instrument is the version administered before interpreting the means.

Survey responses are evidence of preferences, not approval of requirements, algorithms, deployment architecture, or quality thresholds. The owner separately approved F-15's limited installable/static-shell behavior; survey results do not approve reminders or offline access to study records.
## 10. Traceability and verification

The [SRS traceability report](DUNO_SRS_TRACEABILITY.md) crosswalks every draft FR/NFR to its approval/evidence class, implementation status, source files, tests, and gaps. The [acceptance verification plan](acceptance-verification-plan.md) maps approved F-01–F-15 and S-01–S-10 to manual cases and records them as not run until evidence is attached. The [system-design packet](system-design-paper-pack.md) supplies architecture, schema design, wireframes, and nonfunctional design notes. The approved requirement file and story decisions remain the normative acceptance baseline.

For each release, record the source revision, environment, migration state, check result, evidence location, and unresolved defect. Distinguish **Passed**, **Failed**, **Not run**, and **Not applicable**. Code presence, a historical test result, successful file upload, or a single HTTP response is not by itself full production acceptance.

## 11. Open actions before SRS approval

1. Record the proposal approval date and approving stakeholders if available; the supplied proposal file is now cited as an objective source.
2. Verify topic difficulty and Guide outcome mapping in a released build after release approval and the hosted database migration.
3. Reconcile the attached SRS v1.0 candidate statements against `requirements.md`; the disposition table below records the known conflicts. The earlier FR/NFR baseline referenced by SRS v1.0 remains unavailable.
4. Confirm the Grade 9 response eligibility and whether the labeled 1-5 scale shown in the supplied questionnaire is the version administered; raw responses were not recalculated for this SRS.
5. Resolved for this baseline: F-15 installability and a public static offline fallback are approved by the owner; offline study data, queued writes, synchronization, reminders, and wellness pop-ups remain outside the approved scope.
6. Evaluation remains pending as confirmed by the owner. Before conducting it, document the standard edition, characteristics, instrument/method, participants or test conditions, measures, acceptance criteria, and analysis plan; do not report results before execution.
7. Keep unapproved numeric performance, availability, browser/device, and accessibility-conformance thresholds marked TBD until stakeholders approve measurable targets.
8. Refresh verification for the current source and schema, then capture stable demo screenshots and deployment identity. The owner reports the site is available; production acceptance still needs route-specific evidence and hosted migration parity.
9. Obtain formal stakeholder review and approval date before labeling this SRS an approved baseline.

## Appendix A. Source documents and evidence provenance

| Source | Use in this SRS | Limit |
|---|---|---|
| `requirements.md` and linked approved stories | Normative product and security requirements F-01 to F-15 and S-01 to S-10. | Current hosted behavior and migration state are not inferred from source alone. |
| `Duno App SysFun Proposal.docx` (supplied 30 October 2026) | Proposal objectives, proposed functions, technology, and evaluation intent. | The document does not include a formal approval date or sign-off. Repository requirements remain controlling. |
| `Duno App SRS survey results.docx` (supplied 30 October 2026) | Questionnaire text and descriptive survey analysis summarized in Section 9. | Raw response rows were not recalculated; eligibility and rating-scale version need confirmation. Findings are not requirements approval. |
| `DUNO_SRS_v1.0.docx` | Candidate FR/NFR statements and existing SRS narrative. | The document labels itself draft. Its earlier FR/NFR baseline is not available as a separate approved source. |
| CHED CMO No. 25, s. 2015 | Verified national computing program outcomes used by the current NVSU-linked StudyGuide outcome map. | Program-level outcomes only; no course-specific outcomes are claimed. |
