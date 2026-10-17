# Talas 2.0 Phase 1 requirements

**Status:** Project-owner-approved MVP scope baseline as of 2026-10-13, reconstructed from `docs/development-blueprint.md`. The source proposal and exact Phase 2 rubric are not present in this repository. Detailed product rules listed below remain undecided and must be decided before the affected behavior is implemented.

## Product goal and boundary

Build a responsive, browser-based MVP in one month that helps a student organize subjects and topics, choose what to work on, plan available study time, record completed sessions, and review descriptive summaries. Optional workload or energy check-ins remain private and are not health assessments.

The MVP does not include AI-generated recommendations, grade prediction, reminders, calendar integration, class or group accounts, or native apps. Priority, plan, summary, and study suggestion outputs must be deterministic and explainable. Do not make claims about learning outcomes or grades.

## Functional requirements and acceptance criteria

| ID | Priority | Requirement / acceptance criteria | Security and privacy acceptance |
| --- | --- | --- | --- |
| F-01 | Must | A student can register through open student registration, sign in, and sign out using the approved session-based authentication flow. Public registration always creates a student account; users cannot choose or submit a role. | Use CodeIgniter Shield APIs; enforce CSRF on state-changing forms, safe session settings, and login throttling. Never store plaintext passwords. |
| F-02 | Must | The app distinguishes student and admin access; student-only routes reject admins where inappropriate, and admin routes reject students. | Deny by default on protected routes and authorize on the server for every request. Hiding a link is not authorization. |
| F-03 | Must | A student can create, view, edit, and delete their own subjects. | Scope every query and mutation to the authenticated owner; a guessed or foreign subject ID must not reveal or alter another student's data. |
| F-04 | Must | A student can create and manage topics under their subjects, with due date, importance, and progress fields. | Verify that the parent subject belongs to the current student on every read and write. Validate all fields server-side and escape output. |
| F-05 | Must | A student can search and filter their topics/subjects using the available fields. | Search results and filter counts contain only the current student's records; use parameterized queries. |
| F-06 | Must | The student sees a rule-based priority order with a short explanation based on entered deadline, importance, or progress. Empty and overdue cases are handled. | Output only the student's records. Rules and tie behavior are documented and tested; no unsupported prediction or opaque AI ranking. Exact formula remains a product decision. |
| F-07 | Must | A student can enter 15–480 available minutes and receive one 25-minute block per unfinished topic in F-06 order, shortening the last block to fit. They can edit durations, remove and reorder blocks, and save one plan per local date; replacing a plan requires confirmation. | Validate duration and referenced records on the server. Store and display only within the student's owner scope. See [`docs/plan-generation-story.md`](plan-generation-story.md). |
| F-08 | Must | A student can time a study session for a subject and optional topic, review it, and explicitly save it. An interrupted or refreshed timer never silently creates a session. Students can edit or delete saved sessions. | Browser timing is not trusted. Validate 1–480 whole minutes and ownership/relationship of subject and optional topic on the server; protect writes with CSRF. See [`docs/study-session-story.md`](study-session-story.md). |
| F-09 | Must | A student can view simple Aralytics summaries of their study activity and print a readable report. | Summaries and print data are owner-scoped. Do not expose private check-in data in summaries or reports by default. Escape rendered values. |
| F-10 | Should | The app presents brief curated study suggestions as supporting content. | Suggestions are static/curated and do not claim guaranteed outcomes, diagnose, or infer personal traits. No generative AI. |
| F-11 | Should | A student may optionally record a private workload or energy check-in. | Keep check-ins separate from admin aggregates and exports. They are visible only to the student unless an explicitly approved requirement changes this. Collect the minimum needed and document retention/deletion behavior before implementation. |
| F-12 | Should | An admin can access a basic administrative view. | Admin sees only the approved administrative data. No check-in values or student study details unless separately approved. Admin access is server-authorized and audited by tests. Exact fields remain a product decision. |
| F-13 | Must | The main experience works on mobile and desktop, supports keyboard use, and provides a readable print layout. | Do not put private data in URLs, client logs, or publicly accessible assets. |

## Cross-cutting security requirements

| ID | Requirement | Acceptance evidence |
| --- | --- | --- |
| S-01 | Authentication and authorization use framework facilities and server-side checks. | Feature tests cover signed-out access, role boundaries, and direct requests to protected routes. |
| S-02 | Every record read/write checks authenticated ownership, including nested topic/session references. | Cross-user tests use two dummy students and verify no read, update, delete, count, export, or relation attach crosses owners. |
| S-03 | All inputs are validated server-side; all user-provided output is escaped; database access is parameterized. | Tests cover invalid values and rendered payloads; review confirms query safety. |
| S-04 | State-changing requests require CSRF protection; sessions use secure settings appropriate to HTTPS deployment. | Framework configuration review plus feature checks; staging HTTPS smoke check. |
| S-05 | Secrets and real student data never enter source control, demo data, logs, or CI. | Repository secret/data review; use synthetic seed data only. |
| S-06 | Private application paths and configuration are inaccessible over HTTP. | Staging direct-URL probes to `.env`, `app`, `writable`, `vendor`/`system`, configuration, and backup files return 403/404. |
| S-07 | Optional check-ins are minimized and private by default. | Tests verify student owner scope and absence from admin aggregate, search, and print/export paths. |
| S-08 | Errors in production do not disclose stack traces, secrets, or study data. | Production-mode configuration review and safe error-path smoke check. |

## Release acceptance

- Relevant unit and feature tests pass, including priority/plan/session/summary rules and auth, role, owner-isolation, validation, CSRF, and report scoping.
- PHP syntax/style checks and dependency audit pass for the locked dependency set.
- Changed interactions receive a manual browser check on mobile and desktop, including keyboard and print checks where applicable.
- Auth, data-access, and deployment changes include cross-user and direct-URL checks.
- Deployment notes identify any host behavior that remains unverified. Use only synthetic demo accounts and data.

## Decisions required before implementation

These items affect product behavior. The implementer must ask the project owner and record the decision before coding the affected requirement.

1. **Admin view:** Which exact aggregate fields are required, and should any drill-down exist? Default is aggregate-only and excludes check-ins.
2. **Check-in lifecycle:** Confirm whether students can edit/delete check-ins and the retention period. Until decided, do not implement check-in persistence.
3. **Account provisioning:** Owner decisions (2026-10-14): student registration is open; create a private admin demo account; use a separate synthetic student account for any public demo; public admin registration is prohibited. The secure bootstrap mechanism for the first admin remains unresolved; see `docs/auth-foundation-story.md`.
4. **Source of curated suggestions:** Confirm the approved content/source and wording before adding advice content.
5. **Course rubric/deployment:** Add the approved Phase 2 rubric if it changes this scope; decide whether acceptance requires a live hosted demo or a local demo plus hosted snapshot.

## Confirmed product decisions

- **F-06 priority formula (2026-10-15):** Only unfinished topics are scored; importance contributes low/medium/high = 1/2/3; due date contributes overdue/today = 3, within 7 days = 2, later = 1, undated = 0; not started adds 1 and in progress 0. Sort by score, nearest due date, then title and show the reasons. See [`docs/priority-story.md`](priority-story.md).
- **F-07 daily plan (2026-10-16):** The owner approved 15–480 available minutes, one 25-minute block per unfinished topic in F-06 order with the final block shortened to fit, editable/removable/reorderable blocks capped by available time, and one saved plan per local date with confirmation before replacement. If no unfinished topics exist, the saved plan has no blocks. See [`docs/plan-generation-story.md`](plan-generation-story.md).
- **F-08 study sessions (2026-10-16):** The owner approved whole-minute durations from 1–480, explicit saving after timer review, and editing/deleting saved sessions. An interrupted timer must never silently save. See [`docs/study-session-story.md`](study-session-story.md).
- **F-08 subject-history deletion rule (2026-10-16):** The owner decided to block subject deletion while any saved study session references that subject, preserving the saved history. See [`docs/study-session-story.md`](study-session-story.md).
- **F-09 Aralytics summaries (2026-10-16):** The owner approved total minutes, session count, average length, and subject totals for today, 7 days, 30 days, and all time. The printable report includes the selected period, totals, subject/topic breakdown, and dated session list. Private check-ins remain excluded by default. Periods use application-local calendar dates. See [`docs/aralytics-story.md`](aralytics-story.md).
- **F-10 curated study suggestions (2026-10-16):** The owner approved four fixed, general study suggestions adapted from the What Works Clearinghouse practice guide, shown as cards on the student dashboard with source links. Suggestions are not personalized and make no guaranteed outcome, diagnosis, or personal-trait claim; no generative AI is used. See [`docs/study-suggestions-story.md`](study-suggestions-story.md).
- **F-04 topic management (2026-10-15):** Required title up to 120 characters; duplicate titles allowed per subject; optional date-only due date with past dates allowed; importance is low/medium/high; progress is not started/in progress/done; new topics default to medium importance and not started progress, and both values can be changed later. Topic deletion is blocked while saved study sessions reference it, with a restrictive database foreign key as the future race-safe backstop. See [`docs/topic-management-story.md`](topic-management-story.md).
- **F-05 search and filter (2026-10-15):** Search subject names and topic titles/parent subject names; filter topics by subject, importance, progress, and due-date state. Search forms use CSRF-protected POST requests to keep search terms out of URLs. Overdue means before the current app date and not done; upcoming means today or later and not done; undated means no due date. See [`docs/search-filter-story.md`](search-filter-story.md).

## Traceability status

F-06 formula, implementation, verification, and visual acceptance are complete as of 2026-10-16. See the confirmed product decision above and the priority story.

F-03 subject management is accepted and merged to `main` through PR #1. F-04 topic management, F-05 search/filter, and F-06 explainable priority are accepted by the owner as of 2026-10-16. F-07 plan generation is implemented and merged through PR #2. F-08 study sessions are accepted and merged through PR #3. F-09 Aralytics is accepted and merged through PR #4. F-10 suggestions are accepted by the owner on `feature/F-10-study-suggestions` as of 2026-10-17 and are being published for PR review; other requirements remain planned until their corresponding stories are implemented and accepted. Link each story, pull request, and verification evidence to its requirement ID. Update this document and `docs/deployment.md` when accepted behavior or deployment controls change.
