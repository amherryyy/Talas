# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot export requirements are in [`docs/brand-identity.md`](docs/brand-identity.md).

The official CodeIgniter 4 app starter 4.7.5 and CodeIgniter Shield 1.4.1 are installed and locked in Composer. F-03 through F-10 are merged to `main` through PRs #1–#5; F-10 was merged on 2026-10-17 (merge commit `ce44d12`). F-11 private workload and energy check-ins, including the owner-approved Aralytics pattern summary and chart, are accepted for the local development milestone on `feature/F-11-private-check-ins`; release verification remains pending. The approved behavior is documented in `docs/private-check-in-story.md`. Topic, plan, session, and private check-in migrations are applied to the isolated local review database. The original shared XAMPP MariaDB data directory has a corrupt system table and was left untouched; Talas uses a separate local instance. First-admin provisioning and hosted database creation are pending. See `docs/database-setup.md` for local settings and recovery boundaries. The development blueprint is in `docs/development-blueprint.md`, coding instructions are in `AGENTS.md`, and the Git/GitHub procedure is in `docs/version-control.md`.

## Local prerequisites found

- VS Code and Git are available.
- XAMPP PHP is at `C:\xampp\php\php.exe` (8.2.12). MySQL tools are under `C:\xampp\mysql\bin`.
- The PHP `intl` DLL exists but is disabled in XAMPP's default configuration. Use `-d extension=intl` for local CLI commands. The ZIP DLL also needs `-d extension=zip` for Composer archive installs.
- Composer is kept project-local at `.tools/composer.phar` (ignored by Git); run it with `C:\xampp\php\php.exe .tools\composer.phar`.
- Playwright is a development-only dependency for browser layout checks. Setup and commands are in [`docs/browser-testing.md`](docs/browser-testing.md); it is not part of the deployed Duno app.

## Next setup steps

1. For Composer operations, run `C:\xampp\php\php.exe -d extension=intl -d extension=zip .tools\composer.phar install`.
2. Complete database-backed authentication checks and decide a safe first-admin provisioning flow. The isolated local database and `.env` are already configured; see `docs/database-setup.md`.
3. Verify the specific InfinityFree account's PHP extensions, HTTPS, `.htaccess` behavior, and private-path denials before the first upload.

The starter and Shield were installed from their official Composer packages. Do not substitute hand-written framework files.

## Open in VS Code

In VS Code, choose **File → Open Folder** and select `C:\xampp\htdocs\Talas`. The local, untracked workspace settings point PHP validation to XAMPP; each developer should use their own PHP path.
