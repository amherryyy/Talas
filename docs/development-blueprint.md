# Duno development blueprint

**Status:** Local Duno repository at `C:\xampp\htdocs\Talas`; the repository folder retains its historical Talas name. The official CodeIgniter 4.7.5 starter and Shield 1.4.1 are installed. F-03 through F-13 are integrated on `main` at `c83f664` and locally accepted; the owner reviewed student, admin, and guest views on 2026-10-25. The owner deployed the prepared package to InfinityFree on 2026-10-25 and confirmed homepage/assets, hosted student authentication, admin dashboard, and student/admin route separation. The owner confirms the remote ZIP was removed and the core student walkthrough covered subjects/topics, planning, timer sessions, check-ins, and Aralytics. The hosted `.htaccess` dotfile denial rule is present; the owner elected not to pursue the `/.env` request's `ERR_EMPTY_RESPONSE` result further, and it is not recorded as a verified HTTP denial. The uploaded package was built from `main` at `c83f664` plus uncommitted changes. The owner accepted R-04 as all good; the exact deployed revision and the HTTP status for `/.env` remain unverified. The approved identity, palette, product names, and mascot export requirements are in `docs/brand-identity.md`.

**Current audit snapshot (2026-10-29):** local `origin/main` is `77034e4`; the working branch `release/accumulated-2026-10-26` is seven commits beyond `main`, one commit ahead of its remote, and has unstaged/untracked accumulated changes. The owner has confirmed the hosted database is at migration 14/batch 9 and the history-preserving foreign-key rules are active. The application revision currently on the host is still unconfirmed; see `docs/version-control.md` and `docs/deployment.md` for the current audit/release state.

## Decisions and source of requirements

Duno is the current user-facing product identity. The Talas 2.0 proposal name is retained only as historical source context. Follow `docs/brand-identity.md` for the approved wordmark, tagline, module names, palette, and mascot export requirements.

The latest editable **Talas 2.0 Phase 1 proposal** is the product baseline. It describes a one-month responsive website for students to organize subjects and topics, add due dates and progress, see rule-based priorities, adjust a daily study plan, time study sessions, review simple summaries, print reports, and optionally record a private workload or energy check-in. It also includes student/admin accounts, brief study suggestions, and a basic admin view. The older Talas proposal supplies the name **Aralytics** for simple observations, plus search and filtering. The latest proposal deliberately removes CHED claims and treats study suggestions as a supporting feature. The referenced AppDev chat mentions Phase 2, but its answer content is unavailable through the chat reference; this plan does not claim to reproduce an approved Phase 2 specification.

**Product boundary:** One browser-based MVP in one month. Mobile and desktop apps, reminders, calendar links, class accounts, group study, AI-generated advice, and automatic grade predictions are later possibilities. The check-in is optional and is not health assessment.

## Hosting decision

Target: **InfinityFree free hosting**, as confirmed by the project owner. Its current site lists PHP 8.4, MySQL 8.0/MariaDB 11.4, SSL, `.htaccess`, and FTP upload. Its support documentation says free accounts have no SSH/SFTP or server Composer, no cron jobs, and no remote MySQL. The web directory is assigned by domain: it may be the main `htdocs` or a domain-specific `<domain>/htdocs`; check the Domains list rather than assuming the path. Application dependencies and assets must be prepared locally, then uploaded. A local MySQL/MariaDB database is needed for development; remote access to the hosted database is unavailable. [InfinityFree features](https://www.infinityfree.com/), [SSH/Composer](https://forum.infinityfree.com/t/ssh/51141), [cron policy](https://forum.infinityfree.com/t/cron-job-feature-is-now-disabled/80495/), [remote MySQL](https://forum.infinityfree.com/t/connecting-to-mysql-from-an-external-application/49339), [domain web directory](https://forum.infinityfree.com/t/how-to-locate-root-directory/88595).

**Framework decision:** CodeIgniter 4, selected by the project owner. Composer currently locks CI4 4.7.5 and Shield 1.4.1, compatible with host PHP 8.4.27 as reported by the owner on 2026-10-25. It has MVC, migrations, validation, sessions, and test support with less deployment machinery than a full SPA stack. Local XAMPP PHP is 8.2.12; `intl` is enabled for Composer/CLI through a runtime flag and ZIP is enabled for archive extraction. [Requirements](https://www.codeigniter.com/user_guide/intro/requirements.html), [installation](https://codeigniter.com/user_guide/installation/).

**Frontend:** CodeIgniter views, semantic HTML, one small CSS layer, and plain JavaScript modules for the timer and plan adjustments. Use CSS responsive layouts and print styles. No React/Vue, Node server, queue worker, or required runtime build pipeline. Server validates every saved timer duration and plan change; the browser timer is only a user interface.

**Authentication:** Use official CodeIgniter Shield with session-based login and student/admin groups, subject to a local installation and host compatibility check. Owner-approved: student registration is open; public sign-up assigns the student group server-side and provides no role selection; the initial admin is a private demo account; use a separate synthetic student account for any public demo; and separate `/student/*` and `/admin/*` route groups with session plus group authorization. A guarded local admin bootstrap is implemented and reviewed; the owner provisioned a separate synthetic hosted admin through phpMyAdmin and confirmed dashboard access. Do not hand-build password storage or login sessions. Keep the one-month flow independent of outbound email: skip email verification and magic-link reset unless the account's mail path is separately verified. [Shield documentation](https://shield.codeigniter.com/latest/).

**Deployment gate before release:** Check the account's PHP version and `intl`/`mbstring`/MySQLi extensions; identify the target domain's assigned web directory; create a disposable database; confirm `.htaccess` rewrite and deny rules; verify HTTPS; and upload a tiny non-sensitive routing/denial probe. On InfinityFree the code must sit within that domain's assigned `htdocs` directory; protect `app`, `vendor`/`system`, `writable`, configuration, and `.env` with deny rules and test that direct HTTP requests return 403/404. Never rely on rewriting to `public` alone as proof of privacy. CodeIgniter documents the public-folder deployment requirement and its last-resort rewrite pattern for fixed roots. [CodeIgniter deployment](https://codeigniter.com/user_guide/installation/deployment.html), [InfinityFree file access guidance](https://forum.infinityfree.com/t/how-to-write-outside-the-htdocs-directory/34658).

## Four one-week increments

| Week | Goal | Demo and acceptance gate |
| --- | --- | --- |
| 1 | Repo, host probe, schema, account registration/sign-in/out, student/admin roles, subject CRUD | A student can sign in and manage only their own subjects. Admin routes reject students. Hosted denial probes pass. |
| 2 | Topics, due dates, importance/progress, search/filter, rule-based priority list | A student can create topics and see an explained priority order from their own inputs. Empty and overdue cases work. |
| 3 | Editable daily plan from available time; timer and persisted study sessions | Plan fits the entered time and can be changed. A saved session belongs to its owner, subject, and optional topic. Refresh/interruption does not silently create time. |
| 4 | Aralytics summary, print report, brief curated study suggestions, optional private check-in, basic admin view; hardening and deployment | Demo journey works on mobile and desktop, print output is readable, and security/test gates pass on staging before final FTP upload. |

**Scope rule:** Each week ends with a working vertical slice. If schedule slips, preserve accounts, subjects/topics, sessions, and a usable summary first. Simplify plan recommendations and study suggestions to deterministic, explainable rules; keep the check-in and expanded admin totals last. Do not silently drop a Phase 1 feature—record any reduction for instructor/project-owner review.

## Initial repository layout

```text
talas/
  AGENTS.md                    # project instructions for AI coding agents
  README.md                    # setup, run, test, deploy
  composer.json / composer.lock
  app/
    Config/                    # routes, filters, security settings
    Controllers/               # thin request handlers
    Models/                    # persistence and owner-scoped queries
    Services/                  # priority, plan, Aralytics rules
    Views/                     # server-rendered pages and print view
    Database/Migrations/       # schema history
    Database/Seeds/            # demo data, never real student data
  public/                      # front controller, CSS, JS, images
  tests/                       # focused feature and rule tests
  writable/                    # logs/cache/sessions, denied from web
  docs/requirements.md         # Phase 1 traceability and decisions
  docs/deployment.md           # InfinityFree upload and denial checks
  docs/brand-identity.md       # Duno naming, palette, and mascot export requirements
  .github/workflows/ci.yml     # add when GitHub repository exists
```

**Initial entities:** Shield users/groups, subjects (owner), topics (subject, due date, importance, progress), availability blocks, plan items, study sessions, curated study suggestions, and optional check-ins. Start with one MySQL database. Index owner/date foreign keys. Keep check-ins separate from admin aggregate views and omit them from exports unless a student explicitly chooses them.

**Rules:** Priority and daily plan are pure, documented functions so they can be tested without a server. A priority explanation cites user-entered deadline, importance, or progress. Planning never claims to know the best study method or actual learning outcome. Aralytics reports descriptive totals only.

## Lean SDLC and AI coding workflow

The formal project flow, security gates, roles, backlog transitions, implementation protocol, and review record are in [`docs/ai-agent-workflow.md`](ai-agent-workflow.md). Work proceeds through scope approval, environment/hosting readiness, secure foundation, weekly vertical slices, and release. Every story passes Ready → Doing → Verification → Review → Accepted before deployment. Record material LLM assistance in [`docs/ai-use-log.md`](ai-use-log.md); the owner handles any course-specific disclosure requirements. The project owner orders stories weekly. Use one implementer agent plus human review; optional independent review is reserved for sensitive changes. Hold one brief weekly planning/review session.

## Test, CI, and security gates

**Local tests:** Unit tests for priority order, daily-plan capacity, session duration bounds, and Aralytics totals; feature tests for registration/login, student/admin authorization, owner isolation, validation, CSRF-protected writes, subject/topic/session CRUD, and print data scoping. Playwright is a development-only browser test for responsive page checks; manual mobile/desktop and keyboard checks still cover interaction quality and print layout.

**CI:** `.github/workflows/ci.yml` runs on pull requests and pushes to `main`, using PHP 8.2 and 8.4 with locked Composer install, Composer validation, PHP syntax lint, PHPUnit, and `composer audit --locked`. It creates CodeIgniter writable directories for the test runner, has read-only repository permissions, and uses no deployment or hosted credentials. Actions are pinned to full commit SHAs. `.github/dependabot.yml` requests weekly Composer and GitHub Actions dependency updates. PR #7 merged these changes at `34fa405`; pull-request and post-merge runs passed with 67 tests and 612 assertions on each PHP version, and the PHP 8.4 dependency audit found no advisories. The owner confirms `main` requires PRs and the PHP 8.2/8.4 checks. No PHP style tool is configured, so style lint is not claimed. Add database-backed CI only when an isolated synthetic test database and migration checks are ready.

**Security release checks:** Password hashing via framework APIs; secure session cookies over HTTPS; login throttling; CSRF protection; output escaping; parameterized queries; per-record ownership checks; strict input validation; role authorization; production error mode; secrets excluded from Git; minimal retention of optional check-ins; no study data in logs. Test direct web access to `.env`, `app`, `writable`, `vendor`/`system`, and backup files after every deployment. Use dummy accounts and data in the public demo.

**Deployment:** Build/install locally, exclude development packages and secrets from the upload bundle, upload via FTP/FTPS, configure the hosted database in an untracked protected file, run migrations using a reviewed one-time mechanism or import reviewed SQL via phpMyAdmin, then remove any one-time setup endpoint. Verify login, owner isolation, saved sessions, reporting, HTTPS, and denial paths on the live URL. No cron-dependent jobs or email-dependent password reset in the one-month MVP.

## Open items

- The owner reports that no separate Phase 2 rubric was issued; use the approved Phase 1 requirements and recorded product decisions.
- The course requires a live demo, per the owner. Use the hosted app for the walkthrough and keep the local setup available as a backup; no demo duration or scoring criteria were provided. The outline is in `docs/live-demo-runbook.md`.
- R-04 is accepted by the owner. Earlier host evidence is recorded in `docs/deployment.md`; the owner chose not to pursue the `/.env` browser response, which remains unverified as an HTTP status.
- Keep the original XAMPP MariaDB data directory isolated from Talas because its `mysql.db` table is corrupt. The separate local Talas instance and schema are configured, and local database-backed authorization checks are complete. The owner reports hosted registration/login, the student/admin access boundary, and R-04 acceptance. The exact hosted revision and `/.env` HTTP status remain unverified; see [`docs/database-setup.md`](database-setup.md) and [`docs/deployment.md`](deployment.md).
