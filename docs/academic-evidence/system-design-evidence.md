# System design evidence notes

This is a concise map to the existing implementation-grounded design source, `docs/system-design-paper-pack.md`. The diagrams/wireframes in that pack are explanatory artifacts and should not be presented as screenshots or independent validation.

## Architecture and stack

Duno is a server-rendered PHP web application using CodeIgniter 4 (Composer requirements `^4.7`) and CodeIgniter Shield (`^1.4`), with MySQL/MariaDB as the intended persistent store. The inspected Composer manifest requires PHP `^8.2`; the project records an InfinityFree PHP 8.4 host. Views render HTML on the server, custom CSS supplies the responsive interface, and small plain JavaScript modules handle progressive interactions. No frontend framework or runtime build step is configured. Playwright/Node is for development browser tests only.

The request flow is: explicit route in `app/Config/Routes.php` → route/session/role/permission filters → controller → owner-scoped model and/or small calculation service → server-rendered view. Business rules are concentrated in services such as `TopicPriorityService`, `DailyPlanService`, `AralyticsService`, `StudyProgressionService` and `CheckInSummaryService`. Controllers coordinate validation, authorization and response flow. This description is supported by source organization and existing design notes; it is not a measured performance architecture assessment.

## MVC and product areas

| Product area | Main implementation evidence |
|---|---|
| Public/authentication | `Home`, Shield routes, `RegistrationRecovery`, auth config/views |
| Duno Focus | `Student/Topics`, `TopicModel`, `TopicPriorityService`, topic views and `focus-browser.js` |
| Duno Plan | `Student/Plans`, `StudyPlanModel`, `StudyPlanItemModel`, `DailyPlanService`, plan views and `plan-agenda.js` |
| Duno Timer | `Student/StudySessions`, `StudySessionModel`, `study-sessions.js` |
| Aralytics | `Student/Aralytics`, `AralyticsService`, study/check-in summary services, report view and print JS |
| Duno Guide | dashboard/Guide content, curated content and outcome mapping code in current worktree |
| Optional check-ins | `Student/CheckIns`, `CheckInModel`, `CheckInSummaryService` |
| Admin | `Admin/Dashboard`, `AdminDashboardSummaryService`; aggregate dashboard only |

## Authentication and authorization

`Routes.php` disables auto-routing and declares public/auth routes explicitly. Student routes are grouped under session, exclusive student role, and streak filters; admin routes require session, exclusive admin role, and `admin.access`. State-changing operations are POST in the explicit routes. Shield is the authentication foundation. The docs describe email activation code handling, rate limits, CSRF and session controls. These code/config artifacts do not prove all deployed behavior. Route filters are application-level authorization; hiding links is not relied on as the security boundary.

## Data design

The detailed ER diagram and table dictionary are in `docs/system-design-paper-pack.md` §4; actual schema evolution is in `app/Database/Migrations/`. Principal entities include Shield users/auth identities/groups; student-owned subjects; topics; daily plans and plan items; saved study sessions; optional check-ins; daily streak rows; registration verification throttling records; and framework settings. Some program/catalog data is static application code, not normalized curriculum tables.

Ownership is derived from the authenticated session and enforced in model/controller query paths. Composite foreign keys protect selected nested references, including subject/topic and plan/topic ownership; further foreign keys protect session relations. This is application-enforced row-level access control plus relational constraints. MariaDB native row-level security policies are not configured. Check-ins are separated from admin aggregate views and printed study reports per the approved requirements.

Migration state is environment-specific. Owner-provided phpMyAdmin output confirms hosted migration `2026-11-01-000002_ScaleProgressionXpRewards` at batch 15, following the academic-program migration at row 19/batch 14. The host's exact running application revision and complete file parity remain unverified; see `deployment-evidence.md`.

## Main workflows to describe in a paper

1. Student registers, verifies the address, signs in; role defaults to student.
2. Student creates/selects own subjects and topics, then uses Focus to see deterministic ranking and explanations.
3. Student supplies available time or uses the date agenda to schedule owned study-topic blocks; server validates ownership and limits.
4. Timer records elapsed focus locally in the browser until the student reviews and explicitly saves a bounded session.
5. Aralytics summarizes saved sessions for selected periods. Optional check-in summaries are separately presented to their owner and excluded from admin/print.
6. Admin sees aggregate counts through a separately authorized route.
7. PWA service worker is intended to cache only selected public assets and the offline recovery page; authenticated HTML and writes stay network-only. Full behavior remains unverified.

## Design evidence limits

- `docs/system-design-paper-pack.md` is current-worktree documentation, not proof that all described code was uploaded.
- Entity relationships are reconstructed from migrations and code. The host schema was not queried during this audit.
- Program-outcome associations are editorial mappings to selected official outcomes as documented, not endorsements or official mappings by the source institution.
- Wireframes are low fidelity; use current sanitized screenshots for the manuscript and label them by revision/environment.
