# Duno UI, feature, and privacy audit

**Review date:** 2026-11-01
**Baseline:** Owner-approved `docs/requirements.md` and linked story decisions.
**Status:** Source audit and bounded local updates; this is not a production or legal acceptance.

## Findings and disposition

| Area | Current source finding | Disposition |
| --- | --- | --- |
| Account academic program | Local source now has a separate account-level field and onboarding lock; `subjects.program_id` remains an editable curriculum association for individual subjects. | Owner approved one current program per student account on 2026-11-01. Local implementation adds one-time server-enforced selection and a read-only Profile value. Existing subject associations remain unchanged; later changes go through the project administrator. See [`academic-program-onboarding-story.md`](academic-program-onboarding-story.md). |
| Subject and topic management | Student-owned CRUD already exists under Subjects and Topics, with server-side ownership checks. Subject deletion is blocked where records/history still depend on it. The dashboard already links to Subjects. | After reviewing the existing routes, show up to four current-program subjects on the dashboard with topic counts, two topic-title previews, and direct subject/topic links. Keep CRUD forms on their focused pages; duplicating forms on the dashboard would add clutter. |
| Duno Plan time entry | The UI asked for one number of minutes; server and storage already use validated 15–480 whole minutes. | Implemented a two-field hours/minutes control for Plan generation and the mobile add sheet. The server normalizes to the same `available_minutes`; no migration or calculation change. |
| Onboarding and visual consistency | The supplied phone screenshots show slide copy and art out of alignment, including a blank final art frame. The carousel also repeated its welcome label and used floating callout pills and a framed illustration panel. | Locally changed to per-slide art positioning and removed those redundant/decorative UI pieces. Visual browser confirmation remains open because Chromium launch was denied (`spawn EPERM`) and the browser-control host was unavailable. |
| Password visibility controls | A shared script injects keyboard-operable buttons with accessible show/hide names and `aria-pressed`; CSS reserves input space and positions the button inside the field. | No source defect was established. Phone/desktop rendering and keyboard behavior still need live browser review; do not change working auth behavior based on an unverified visual concern. |
| Admin analytics | F-12 originally exposed five totals. On 2026-11-01, the owner approved saved-plan count, average saved-session length, recorded visit-day counts, program-selection completion, weekly visit trends, and monthly registration trends. | Implemented locally with inline SVG charts and accessible tabular data. No new instrumentation or runtime dependency was added. Trend bins with fewer than five contributing students and onboarding percentages with fewer than five students in either group are withheld. No identities, program breakdowns, check-ins, exports, or drill-down are exposed. Hosted migration/runtime and browser visual checks remain pending. See [`admin-dashboard-story.md`](admin-dashboard-story.md). |
| Terms, privacy, and browser storage | Source uses the `ci_session` session cookie, session-based CSRF, a one-year `duno_language` preference cookie, and local browser keys for onboarding completion and check-in prompt dismissal. No optional analytics/tracking integration was found in the inspected app source. | Public Terms, Privacy, and Cookies pages describe source-verified behavior and remain preliminary. Profile now offers password-confirmed, explicit self-service deletion of application account data; provider backup expiry and general retention remain unverified. No registration acknowledgement or cookie consent gate was added. Controller identity, lawful bases, host/mail providers, and institutional process still require owner/institutional/legal confirmation before the pages are treated as final. |
| Feature grouping | Focus, Plan, Timer, Aralytics, Guide, Subjects/Topics, check-ins, and the admin view have distinct approved requirements. | Keep each purpose. Improve discovery through concise routes and contextual entry points; do not merge or remove features without a product decision. |
| Overall mobile UI | Source includes responsive student and admin navigation, mobile Plan agenda, forms, and compact admin totals. A current all-route visual audit could not be performed in a browser. | Avoid application-wide restyling from source alone. Review one route and viewport at a time when browser execution is available; prioritize demonstrated issues. |

## Privacy inventory from source

- **Account/authentication:** username and email are used by Shield; passwords are handled by Shield and must remain stored as hashes. Registration verification generates and emails a one-time numeric code. Pending registrations are cleaned after 48 hours. The configured mail transport and provider retention were not confirmed.
- **Academic records:** subjects, topics and their progress/dates/importance/difficulty, saved plan dates/available minutes/start times, study-session durations/timestamps and subject/topic snapshots, and progression activity are stored for app features.
- **Optional check-ins:** workload and energy ratings are optional, contain no free-text note, remain until the student deletes them, and are excluded from admin aggregates and printed study reports under the approved requirements.
- **Admin access:** the dashboard returns aggregate counts only; the current approved scope does not expose student identities or record-level academic or check-in data.
- **Cookies and browser storage:** the language preference is a first-party `duno_language` cookie lasting 365 days. Authentication uses `ci_session`; CSRF protection is configured for the server session. Onboarding completion and daily prompt dismissal use account-scoped `localStorage` keys, with `sessionStorage` fallback for tour completion when local storage is unavailable. These browser keys contain state, not study records.
- **PWA:** the service worker caches a selected public shell and offline recovery page. It does not cache authenticated pages or study records and does not queue writes.
- **Unknowns to resolve before publication:** controller/institution identity and privacy contact; mail and hosting processors, data locations and contracts; provider access/error logs; backups and retention/deletion behavior for accounts; student access/correction/export/deletion request process; and any school-specific rules for minors or institutional research.

The inventory is a source review, not a network capture or host contract review. A privacy notice must disclose verified facts and a reviewed lawful basis for each processing purpose. The Philippine Data Privacy Act and NPC material emphasize notice content and data-subject rights; see [Republic Act No. 10173](https://officialgazette.gov.ph/2012/15/republic-act-no-10173), [NPC: the right to be informed](https://privacy.gov.ph/the-right-to-be-informed/), and [NPC: data subject rights](https://privacy.gov.ph/data-subject-rights/). This audit does not determine legal compliance.

## Admin analytics: data already available

The owner approved these aggregate additions on 2026-11-01. They are derived from existing records; no page-view or feature-use instrumentation was added:

| Candidate | Existing source | Limit |
| --- | --- | --- |
| Saved daily plans | `study_plans` rows and `plan_date` | A plan is replaced in place, so this measures saved plan records/days, not every generation action. |
| Average saved session length | `study_sessions.duration_minutes` and session count | Describes saved sessions only; it does not measure time spent in the app. |
| Recorded visit-day counts in a period | `student_streak_days.user_id` and `activity_date` | Counts student-day records, with at least five distinct contributing accounts per displayed trend bin. It is not a complete page-view or feature-usage log. |
| New account trend | Student-group accounts and `users.created_at` | Requires only a fixed, coarse month grouping; do not include identity rows or program filters. |
| Onboarding completion | `users.academic_program_selected_at` | Available only after the pending academic-program migration is applied and verified; use only an overall count/percentage. |

The current schema cannot establish plan adherence or a planned-versus-completed rate: `study_plan_items` stores topic and subject snapshots but not a completion event. Topic progress is current state and cannot reconstruct what was completed on a past plan date. Feature-event and application-error trends need new instrumentation and a separate retention/privacy decision. Avoid program-level or narrow date filters; small groups could reveal participation even when names are hidden. Do not include private check-ins in admin metrics.

## Approval gates

1. Apply and verify the reviewed academic-program schema upgrade before uploading app code that reads the new columns; then check with synthetic accounts.
2. The owner approved the metric expansion and five-student disclosure threshold on 2026-11-01. Review the local implementation and verification record in [`admin-dashboard-story.md`](admin-dashboard-story.md); host deployment remains a separate step.
3. Have the institution/project owner supply the controller identity, privacy contact, approved retention/deletion process, processors, and required student/guardian notices. Review and approve the preliminary public pages before treating them as final or adding registration acknowledgements or consent UI.
4. Complete the pending rendered checks for onboarding and authentication controls when a browser runner is available.

## Local work and verification

- The onboarding art alignment/clutter adjustment is recorded in [`student-onboarding-story.md`](student-onboarding-story.md).
- The dashboard now shows a short current-program subject/topic overview with counts and direct links; CRUD remains in the owner-checked route flows. The Plan UI-only hours/minutes change is recorded in [`plan-generation-story.md`](plan-generation-story.md); `docs/deployment.md` records that it is not part of uploaded r8 and requires no migration.
- The focused Plan feature suite passed: 10 tests and 111 assertions. The initial invocation omitted `php_sqlite3.dll`; rerunning with the extension enabled passed. PHPUnit reports that no code-coverage driver is installed.
- Academic-program selection passed locally: 2 feature tests and 31 assertions. The full suite then passed 106 tests and 1,086 assertions with `php_intl.dll` and `php_sqlite3.dll` enabled. PHPUnit reports that no code-coverage driver is installed. Hosted schema/runtime behavior remains pending.
- The broader browser visual check remains unverified because Chromium could not start with `spawn EPERM`.
- No production files were uploaded; nothing was staged or committed.
