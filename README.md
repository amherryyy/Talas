# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot requirements are in [`docs/brand-identity.md`](docs/brand-identity.md). The restart board and attention list are in [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md).

The official CodeIgniter 4 app starter 4.7.5 and CodeIgniter Shield 1.4.1 are installed and locked in Composer. F-03 through F-10 were accepted and merged through PRs #1 to #5. F-11 through F-13 are accepted for the local milestone and integrated on `main` at `c83f664` (2026-10-25 review). On 2026-10-25, the owner deployed a reviewed `htdocs` package through InfinityFree File Manager and verified the public homepage/assets, hosted student registration/login, the admin dashboard, and student denial of the admin route. The upload came from `main` at `c83f664` plus uncommitted worktree changes; remaining host checks are listed in [`docs/deployment.md`](docs/deployment.md). Local and hosted synthetic auth review is documented. The signed-out navigation is implemented as Home, Tools, Guide, and Join, and the owner confirmed the guest experience is all good. CI automation and weekly Dependabot Composer/Actions updates merged in [PR #7](https://github.com/amherryyy/Talas/pull/7). The PHP 8.2/8.4 jobs passed with 67 tests and 612 assertions on each version, and the PHP 8.4 dependency audit found no advisories. The owner confirms `main` now requires pull requests and both PHP checks. Talas uses a separate local MariaDB instance because the original shared XAMPP data directory has a corrupt system table; it remains untouched. See [`docs/database-setup.md`](docs/database-setup.md).

## Local prerequisites found

- VS Code and Git are available.
- XAMPP PHP is at `C:\xampp\php\php.exe` (8.2.12). MySQL tools are under `C:\xampp\mysql\bin`.
- The PHP `intl` DLL exists but is disabled in XAMPP's default configuration. Use `-d extension=intl` for local CLI commands. The ZIP DLL also needs `-d extension=zip` for Composer archive installs.
- Composer is kept project-local at `.tools/composer.phar` (ignored by Git); run it with `C:\xampp\php\php.exe .tools\composer.phar`.
- Playwright is a development-only dependency for browser layout checks. Setup and commands are in [`docs/browser-testing.md`](docs/browser-testing.md); it is not part of the deployed Duno app.

## Next steps

Use [`docs/project-overview-and-restart.md`](docs/project-overview-and-restart.md) for the ordered local review, documentation, CI, and release tasks. For host setup, follow [`docs/deployment.md`](docs/deployment.md) and [`docs/database-setup.md`](docs/database-setup.md); never reuse the local `.env` or put host credentials in Git.

## Open in VS Code

In VS Code, choose **File → Open Folder** and select `C:\xampp\htdocs\Talas`. The local, untracked workspace settings point PHP validation to XAMPP; each developer should use their own PHP path.
