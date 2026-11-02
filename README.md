# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot requirements are in [`docs/brand-identity.md`](docs/brand-identity.md). The restart board and attention list are in [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md).

`composer.lock` resolves CodeIgniter Framework 4.7.5 and CodeIgniter Shield 1.4.1. PRs #22 and #23 are merged. On 2026-11-02, the owner reported uploading r10 and supplied the marker `duno-full-release-20261102-r10`; phpMyAdmin output confirms migrations `2026-11-02-000001` and `2026-11-02-000002` and their schemas. Email/reset behavior, post-upload smoke checks, and full uploaded-file parity remain unverified. See [`docs/deployment.md`](docs/deployment.md) and [`docs/version-control.md`](docs/version-control.md).

The original R-08 redesign was accepted; its later visual refinements remain under review. R-09 is accepted: core Aralytics stays open while study-time milestones unlock optional insights and badges. The owner-approved daily visit reward and streak are implemented; private check-ins do not earn XP. On 2026-11-02, owner-provided phpMyAdmin output confirmed hosted migration `2026-11-01-000002` in batch 15 and zero remaining 500-XP visit rows. The owner also reports selected r9 cross-account write-isolation and account-deletion checks passed. Full deployed-file parity and other requirement-level live acceptance remain open. See [`docs/deployment.md`](docs/deployment.md), [`docs/ui-ux-redesign-story.md`](docs/ui-ux-redesign-story.md), and [`docs/study-progression-story.md`](docs/study-progression-story.md).

The owner requested glass surfaces on cards, a tighter header/menu, and a minimalist footer. The local styling update is recorded in [`docs/glass-surface-polish-story.md`](docs/glass-surface-polish-story.md); browser review can continue against the owner-reported r10 upload.

Each student chooses one current academic program per account. The setup keeps the NVSU Bayombong college/program list and adds manual school/program entry for students outside that list. Programs without a Duno subject list use typed subject names. Choices are read-only in Profile. The hosted academic-program migration `2026-11-01-000001` was applied and verified; the school-name migration `2026-11-02-000002` still needs a reviewed hosted upgrade before this feature is released. Existing per-subject curriculum values are preserved. See [`docs/academic-program-onboarding-story.md`](docs/academic-program-onboarding-story.md) and [`docs/deployment.md`](docs/deployment.md).

The local Plan update adds a phone-oriented week agenda, topic-block scheduling, and a current-time marker while preserving the existing desktop planner. It requires the nullable `study_plan_items.start_time` column; follow [`docs/deployment.md`](docs/deployment.md) and its one-time SQL upgrade before uploading the app changes.

The student onboarding tour is also in the local update batch. It introduces the existing tools, can be skipped or replayed, and remembers completion per student in the current browser; its browser review is deferred until upload. See [`docs/student-onboarding-story.md`](docs/student-onboarding-story.md).

Students choose one current academic program per account. The NVSU Bayombong list remains available, and students from other schools can enter their school and program manually. Programs without a Duno subject list use typed subject names. BSCS, BSIT, and BSIS are grouped under the College of Information Technology Education (CITE), their current college; their CAS course lists are historical references effective S.Y. 2019â€“2020, not confirmation of current approved plans. See [`docs/academic-program-onboarding-story.md`](docs/academic-program-onboarding-story.md). Browser review is deferred until the accumulated updates are uploaded to InfinityFree.

CHED sample templates and DepEd SHS subjects remain separate references and are not accepted as imports into an NVSU student's subject list.

The owner approved a bounded installable PWA for the next release: the worker caches public shell assets and an offline recovery page only. Authenticated pages, student data, and changes remain online-only; no database migration is required. See [`docs/pwa-story.md`](docs/pwa-story.md) and [`docs/deployment.md`](docs/deployment.md).

## Local prerequisites found

- VS Code and Git are available.
- XAMPP PHP is at `C:\xampp\php\php.exe` (8.2.12). MySQL tools are under `C:\xampp\mysql\bin`.
- The PHP `intl` DLL exists but is disabled in XAMPP's default configuration. On this Windows setup, use `-d extension=php_intl.dll`; `-d extension=intl` did not load the `Locale` class. The ZIP DLL also needs `-d extension=php_zip.dll` for Composer archive installs.
- Composer is kept project-local at `.tools/composer.phar` (ignored by Git); run it with `C:\xampp\php\php.exe .tools\composer.phar`.
- Playwright is a development-only dependency for browser layout checks. Setup and commands are in [`docs/browser-testing.md`](docs/browser-testing.md); it is not part of the deployed Duno app.

## Next steps

Use [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md) for the ordered local review, documentation, CI, and release tasks. For host setup, follow [`docs/deployment.md`](docs/deployment.md) and [`docs/database-setup.md`](docs/database-setup.md); never reuse the local `.env` or put host credentials in Git.

## Open in VS Code

In VS Code, choose **File â†’ Open Folder** and select `C:\xampp\htdocs\Talas`. The local, untracked workspace settings point PHP validation to XAMPP; each developer should use their own PHP path.
