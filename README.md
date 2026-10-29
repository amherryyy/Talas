# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot requirements are in [`docs/brand-identity.md`](docs/brand-identity.md). The restart board and attention list are in [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md).

The official CodeIgniter 4 app starter 4.7.5 and CodeIgniter Shield 1.4.1 are installed and locked in Composer. The accumulated product work is tracked in PRs #1–#15; PR #15 (InfinityFree query-route normalization) is merged, but its r2 package is not confirmed uploaded. The latest confirmed live marker remains `duno-full-release-20261029-r1`. Current local updates add a mobile Plan agenda, onboarding carousel, and UI improvements; they remain unmerged and need a focused review before release. Plan agenda migration `2026-10-29-000005` must be applied to the host before uploading its application files. See [`docs/deployment.md`](docs/deployment.md) and [`docs/version-control.md`](docs/version-control.md) for the release gates. The owner reports that there is no separate Phase 2 rubric and a live demo is required; see the [live-demo outline](docs/live-demo-runbook.md). The owner confirms `main` requires pull requests and both PHP checks. Talas uses a separate local MariaDB instance because the original shared XAMPP data directory has a corrupt system table; it remains untouched. See [`docs/database-setup.md`](docs/database-setup.md).

R-08 is accepted by the owner. R-09 is accepted: core Aralytics stays open while study-time milestones unlock optional insights and badges. The owner-approved daily visit reward and streak are implemented; private check-ins do not earn XP. The owner verified hosted migration 16 (batch 11), including the nullable Plan agenda `start_time` column, and the history-preserving session constraints. Current code changes still require focused Git review and a new package; the live app marker remains r1. See [`docs/deployment.md`](docs/deployment.md), [`docs/ui-ux-redesign-story.md`](docs/ui-ux-redesign-story.md), and [`docs/study-progression-story.md`](docs/study-progression-story.md).

The owner requested glass surfaces on cards, a tighter header/menu, and a minimalist footer. The local styling update is recorded in [`docs/glass-surface-polish-story.md`](docs/glass-surface-polish-story.md); browser review is deferred until the accumulated updates are uploaded to InfinityFree.

The local Plan update adds a phone-oriented week agenda, topic-block scheduling, and a current-time marker while preserving the existing desktop planner. It requires the nullable `study_plan_items.start_time` column; follow [`docs/deployment.md`](docs/deployment.md) and its one-time SQL upgrade before uploading the app changes.

The student onboarding tour is also in the local update batch. It introduces the existing tools, can be skipped or replayed, and remembers completion per student in the current browser; its browser review is deferred until upload. See [`docs/student-onboarding-story.md`](docs/student-onboarding-story.md).

The subject catalog lists NVSU undergraduate offerings by Bayombong and Bambang campus and source-linked reference course lists for Forestry, Agroforestry, Ecotourism, BSIT Web and Mobile App Development, BSIT Network Design and Management, BSIS Business Analytics, BSIS Service Management, and both BSCS majors (Game Development and Robotics). The CAS curricula are effective S.Y. 2019–2020; they are historical references, not confirmation of current approved plans, and Duno does not assign their semester sequence. Three separately labeled CHED sample templates (BA Communication, BS Accountancy, BS Business Administration) and the DepEd SHS catalog remain separate. See [`docs/ched-curriculum-catalog-story.md`](docs/ched-curriculum-catalog-story.md). Browser review is deferred until the accumulated updates are uploaded to InfinityFree.

The subject catalog also lists DepEd's published Strengthened SHS curriculum guides for SY 2026-2027, with core, academic elective, TechPro elective, and new Grade 12 guide groups. It supports additive import of selected SHS subjects. It does not include legacy subjects for non-pilot Grade 12 learners; see [`docs/shs-subject-catalog-story.md`](docs/shs-subject-catalog-story.md).

## Local prerequisites found

- VS Code and Git are available.
- XAMPP PHP is at `C:\xampp\php\php.exe` (8.2.12). MySQL tools are under `C:\xampp\mysql\bin`.
- The PHP `intl` DLL exists but is disabled in XAMPP's default configuration. On this Windows setup, use `-d extension=php_intl.dll`; `-d extension=intl` did not load the `Locale` class. The ZIP DLL also needs `-d extension=php_zip.dll` for Composer archive installs.
- Composer is kept project-local at `.tools/composer.phar` (ignored by Git); run it with `C:\xampp\php\php.exe .tools\composer.phar`.
- Playwright is a development-only dependency for browser layout checks. Setup and commands are in [`docs/browser-testing.md`](docs/browser-testing.md); it is not part of the deployed Duno app.

## Next steps

Use [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md) for the ordered local review, documentation, CI, and release tasks. For host setup, follow [`docs/deployment.md`](docs/deployment.md) and [`docs/database-setup.md`](docs/database-setup.md); never reuse the local `.env` or put host credentials in Git.

## Open in VS Code

In VS Code, choose **File → Open Folder** and select `C:\xampp\htdocs\Talas`. The local, untracked workspace settings point PHP validation to XAMPP; each developer should use their own PHP path.
