# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot requirements are in [`docs/brand-identity.md`](docs/brand-identity.md). The restart board and attention list are in [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md).

The official CodeIgniter 4 app starter 4.7.5 and CodeIgniter Shield 1.4.1 are installed and locked in Composer. F-03 through F-10 were accepted and merged through PRs #1 to #5. F-11 through F-13 and the restart/package work are merged to `main` through [PR #9](https://github.com/amherryyy/Talas/pull/9); the package validation record is merged through [PR #10](https://github.com/amherryyy/Talas/pull/10). Current `main` is at `4c02a39` (2026-10-25). The owner reports R-04's InfinityFree release checks are all good. Earlier owner-reported deployment checks included the public homepage/assets, student registration/login, the admin dashboard, student denial of the admin route, core study flows, and private-path denials. A clean package and ZIP were prepared from `main` at `4c02a39`; the exact revision now present on the host has not been independently confirmed. The owner reports that there is no separate Phase 2 rubric and a live demo is required; see the [live-demo outline](docs/live-demo-runbook.md). Local and hosted synthetic auth review is documented. The signed-out navigation is implemented as Home, Tools, Guide, and Join, and the owner confirmed the guest experience is all good. CI automation and weekly Dependabot Composer/Actions updates merged in [PR #7](https://github.com/amherryyy/Talas/pull/7). The PHP 8.2/8.4 jobs passed with 67 tests and 612 assertions on each version, and the PHP 8.4 dependency audit found no advisories. The owner confirms `main` now requires pull requests and both PHP checks. Talas uses a separate local MariaDB instance because the original shared XAMPP data directory has a corrupt system table; it remains untouched. See [`docs/database-setup.md`](docs/database-setup.md).

R-08 is accepted by the owner, with the merged mascot/navigation follow-up awaiting browser review. R-09 is accepted: core Aralytics stays open while study-time milestones unlock optional insights and badges. The owner-approved daily visit reward and streak are implemented and automated checks pass; authenticated browser review is deferred until the accumulated updates are uploaded to InfinityFree. Private check-ins do not earn XP. No hosted migration or deployment was performed. See [`docs/ui-ux-redesign-story.md`](docs/ui-ux-redesign-story.md), [`docs/study-progression-story.md`](docs/study-progression-story.md), and [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md).

The owner requested glass surfaces on cards, a tighter header/menu, and a minimalist footer. The local styling update is recorded in [`docs/glass-surface-polish-story.md`](docs/glass-surface-polish-story.md); browser review is deferred until the accumulated updates are uploaded to InfinityFree.

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
