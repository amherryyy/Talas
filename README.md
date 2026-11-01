# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot requirements are in [`docs/brand-identity.md`](docs/brand-identity.md). The restart board and attention list are in [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md).

`composer.lock` resolves CodeIgniter Framework 4.7.5 and CodeIgniter Shield 1.4.1. Product work through PR #15 is merged, including the InfinityFree query-route normalization. The owner uploaded r7 and reported marker `duno-full-release-20261101-r7`; a fresh cache-busted Brave request independently returned the same marker. A live check found the marker publicly cached for 30 days and static 403 error responses lacked no-store/security headers. A local follow-up routes the marker through PHP with no-store and renders server-level 403s through a PHP header wrapper; r8 preview package preparation is complete, but it is not tied to a clean source commit and is not approved for production upload. R7 was built from clean source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3` on `release/duno-production-r7`; ZIP SHA-256 is `B48283C634F98F9C3820C38AB5F9A035D380E97F28187456CEAE297DE87863C4`. Marker agreement does not establish complete uploaded-file parity. Signed-out Focus and branded static/private-path fallback pages were checked in Brave; actual offline behavior and live cross-owner isolation remain open. Details are in [`docs/deployment.md`](docs/deployment.md).

R-08 is accepted by the owner. R-09 is accepted: core Aralytics stays open while study-time milestones unlock optional insights and badges. The owner-approved daily visit reward and streak are implemented; private check-ins do not earn XP. On 2026-10-31 the owner confirmed hosted migrations through row 18/batch 13, including the topic difficulty and owner-aware plan/topic constraints. R7 marker is now confirmed in Brave; exact deployed-file parity and the remaining live security, cross-owner, response-header, and true offline checks are open. See [`docs/deployment.md`](docs/deployment.md), [`docs/ui-ux-redesign-story.md`](docs/ui-ux-redesign-story.md), and [`docs/study-progression-story.md`](docs/study-progression-story.md).

The owner requested glass surfaces on cards, a tighter header/menu, and a minimalist footer. The local styling update is recorded in [`docs/glass-surface-polish-story.md`](docs/glass-surface-polish-story.md); browser review is deferred until the accumulated updates are uploaded to InfinityFree.

The local Plan update adds a phone-oriented week agenda, topic-block scheduling, and a current-time marker while preserving the existing desktop planner. It requires the nullable `study_plan_items.start_time` column; follow [`docs/deployment.md`](docs/deployment.md) and its one-time SQL upgrade before uploading the app changes.

The student onboarding tour is also in the local update batch. It introduces the existing tools, can be skipped or replayed, and remembers completion per student in the current browser; its browser review is deferred until upload. See [`docs/student-onboarding-story.md`](docs/student-onboarding-story.md).

The subject catalog lists NVSU undergraduate offerings by Bayombong and Bambang campus and source-linked reference course lists for Forestry, Agroforestry, Ecotourism, BSIT Web and Mobile App Development, BSIT Network Design and Management, BSIS Business Analytics, BSIS Service Management, and both BSCS majors (Game Development and Robotics). The CAS curricula are effective S.Y. 2019â€“2020; they are historical references, not confirmation of current approved plans, and Duno does not assign their semester sequence. Three separately labeled CHED sample templates (BA Communication, BS Accountancy, BS Business Administration) and the DepEd SHS catalog remain separate. See [`docs/ched-curriculum-catalog-story.md`](docs/ched-curriculum-catalog-story.md). Browser review is deferred until the accumulated updates are uploaded to InfinityFree.

The subject catalog also lists DepEd's published Strengthened SHS curriculum guides for SY 2026-2027, with core, academic elective, TechPro elective, and new Grade 12 guide groups. It supports additive import of selected SHS subjects. It does not include legacy subjects for non-pilot Grade 12 learners; see [`docs/shs-subject-catalog-story.md`](docs/shs-subject-catalog-story.md).

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
