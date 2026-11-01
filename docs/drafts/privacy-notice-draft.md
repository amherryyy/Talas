# Duno Privacy Notice — draft for review

> **Draft only — do not publish or rely on this notice yet.** Confirm every item marked for review with the project owner, institution/privacy officer, hosting and email providers, and legal reviewer. This text does not determine a lawful basis or certify compliance.

**Personal Information Controller:** `[Legal name of the responsible project/institution — confirm]`
**Privacy contact / DPO, if applicable:** `[Name, role, monitored email/address — confirm]`
**Effective date:** `[Set after review]`

## 1. What Duno does

Duno is a browser-based study organization service. It processes account details and student-entered study records to provide account access, subject and topic management, study planning, saved study sessions, Aralytics, general study suggestions, and optional private check-ins.

## 2. Information used

| Category | Examples found in the current application | Purpose in the app |
| --- | --- | --- |
| Account and authentication | Username, email address, password handled through CodeIgniter Shield, registration verification code and limits | Create and protect an account, verify registration, sign in/out, and prevent abuse |
| Academic organization | Subject names and optional curriculum associations; topic titles, due dates, importance, difficulty, and progress | Let a student organize and prioritize their own study tasks |
| Plans and study activity | Plan date, available minutes, optional start times, topic/subject snapshots, session duration and completion time | Build a time-bounded plan and show saved activity summaries |
| Progression | Saved-study-minute and visit-day activity used for the student's XP, streak, and unlocks | Provide the approved deterministic progression view |
| Optional check-ins | Student-selected workload and energy ratings, with no free-text note | Show the student's own descriptive private summaries; these are not a health assessment |
| Language preference | A first-party language-choice cookie | Remember English, Taglish, or Filipino preference |
| Browser-only interface state | Account-scoped tour completion and daily check-in-prompt dismissal keys in local storage; the tour can fall back to session storage | Avoid repeating optional interface prompts; these keys do not contain study records |
| Operational data | `[Confirm hosting access logs, security logs, error logs, backups, and retention with the providers.]` | `[Confirm actual purposes and recipients before publication.]` |

The current code review did not find an optional analytics or advertising tracker. The production host, email provider, and any provider-generated logs were not verified in this source audit.

## 3. Why and on what basis information is used

The app uses account and study records to provide the features a student requests and to protect the service. The responsible controller must identify and document the applicable lawful basis for each processing purpose before publication; this draft does not claim that all processing is based on consent. Optional check-ins are an optional feature, but their optional status alone does not settle the lawful basis or institutional review required for processing them.

## 4. Who can access or receive information

- A student can access their own account-scoped academic records and private check-ins.
- The current administrator view exposes aggregate totals only and excludes student identities, individual study records, and check-in values/patterns.
- The hosting provider and email-delivery provider may process information needed to operate the service. `[Confirm provider names, roles, locations, contracts, and data transfers before publication.]`
- `[Confirm whether the school/institution, project owner, or any other party receives information.]` Do not claim that instructors receive student data unless this is verified and approved.

## 5. Retention and deletion

| Information | Current app behavior found | Publication item to resolve |
| --- | --- | --- |
| Abandoned pending registrations | App cleanup removes pending registrations after 48 hours | Confirm the production cleanup path and related email-provider retention |
| Subjects, topics, plans, sessions, progression | Persist in the app database; supported student delete actions exist for some records, subject/topic deletion can be blocked by dependent saved history | Define account closure, backup expiry, and the retention of blocked/dependent records |
| Optional check-ins | Kept until the student deletes an entry | Confirm backup and closed-account handling |
| Authentication/session data | Session-based login; configured session expiration is two hours | Confirm actual host behavior and provider logs |
| Language preference | `duno_language` cookie is configured for 365 days | Confirm deployed attributes and whether the duration should remain |
| Local interface state | Persists in the user's browser until cleared or the browser's storage is removed | Explain how users can clear browser storage; local storage is not a server-side study record |
| Hosting and mail logs/backups | Not established by this source review | Obtain provider retention periods and publish accurate periods/processes |

The app currently has no self-service account deletion or export feature. The controller must establish an accessible request/contact process and decide how account closure and backups are handled. Do not promise a deletion deadline until it is confirmed.

## 6. Cookies and browser storage

The source configures a first-party `ci_session` authentication session cookie, session-based CSRF protection, and a first-party `duno_language` preference cookie. Production config marks cookies Secure on HTTPS, HttpOnly, and SameSite=Lax. Duno also uses local storage for tour completion and a check-in-prompt dismissal flag, and session storage as a fallback for the tour. It does not use those browser keys to store subjects, topics, study plans, sessions, or check-in values.

The bounded service worker caches only selected public assets and an offline recovery page; it does not cache authenticated pages or saved study data and does not queue writes. No optional analytics/advertising storage was found in the reviewed app source. The owner/privacy reviewer should decide whether a cookie/storage notice is required for this actual deployment; no consent banner has been added.

## 7. Security and access controls

The application uses session authentication, role-gated routes, CSRF protection, and application-level owner checks for student records. These controls reduce risk but cannot guarantee that unauthorized access, provider incidents, or service outages will never occur. The controller should add its approved incident and breach-contact procedure here after review.

## 8. Data subject rights and requests

Applicable Philippine privacy rules may provide rights relating to information, access, correction, objection, erasure/blocking, portability, damages, and complaints, subject to the law and its limits. Requests should be directed to `[approved privacy contact and verification/request process]`. The controller must confirm the request procedure and response responsibilities before this notice is published. Official references for review: [Republic Act No. 10173](https://officialgazette.gov.ph/2012/15/republic-act-no-10173), [NPC: the right to be informed](https://privacy.gov.ph/the-right-to-be-informed/), and [NPC: data subject rights](https://privacy.gov.ph/data-subject-rights/).

## 9. Changes and contact

`[Describe how material changes to this notice will be announced.]`

Privacy questions and requests: `[Insert the verified controller/privacy contact.]`

## Review checklist — resolve before publication

- Name the controller, institution, privacy contact/DPO if applicable, and responsible support contact.
- Confirm age/guardian and school-project rules.
- Determine and document lawful basis and purposes for each data category, especially optional check-ins and progression.
- Confirm InfinityFree hosting details, mail provider, data locations/transfers, sub-processors, access/error logs, and backup retention.
- Set account closure, data export, correction, and deletion-request processes and realistic response/retention periods.
- Confirm access controls and admin aggregate policy against live production source.
- Obtain institutional/privacy/legal review and student-facing usability review.
- Publish the final notice with a review date. If terms are required, record that agreement separately from optional processing consent.
