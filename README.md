# duno.

**Your Little Study Buddy.**

Local VS Code workspace for the one-month Duno study-planning app. Its product areas are Duno Focus, Duno Plan, Duno Guide, and Duno Timer; **Aralytics** keeps its existing name. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The approved Duno identity and mascot export requirements are in [`docs/brand-identity.md`](docs/brand-identity.md).

The official CodeIgniter 4 app starter 4.7.5 and CodeIgniter Shield 1.4.1 are installed and locked in Composer. F-03 through F-10 are merged to `main` through PRs #1–#5; F-10 was merged on 2026-10-17 (merge commit `ce44d12`). F-11 private workload and energy check-ins, including the owner-approved Aralytics pattern summary and chart, are accepted for the local development milestone; focused tests and browser checks pass locally, while hosted release verification remains pending. F-11, F-12, and F-13 are integrated on local branch `feature/F-13-responsive-accessibility`. The approved behavior is documented in `docs/private-check-in-story.md`. Topic, plan, session, and private check-in migrations are applied to the isolated local review database. The original shared XAMPP MariaDB data directory has a corrupt system table and was left untouched; Talas uses a separate local instance. Local first-admin setup is implemented and was used for review; the owner reports hosted database creation and confirms 15 imported tables and nine migration rows; hosted app configuration and admin provisioning remain pending. See `docs/database-setup.md` for local settings and recovery boundaries. The development blueprint is in `docs/development-blueprint.md`, coding instructions are in `AGENTS.md`, and the Git/GitHub procedure is in `docs/version-control.md`.

## Local prerequisites found

- VS Code and Git are available.
- XAMPP PHP is at `C:\xampp\php\php.exe` (8.2.12). MySQL tools are under `C:\xampp\mysql\bin`.
- The PHP `intl` DLL exists but is disabled in XAMPP's default configuration. Use `-d extension=intl` for local CLI commands. The ZIP DLL also needs `-d extension=zip` for Composer archive installs.
- Composer is kept project-local at `.tools/composer.phar` (ignored by Git); run it with `C:\xampp\php\php.exe .tools\composer.phar`.
- Playwright is a development-only dependency for browser layout checks. Setup and commands are in [`docs/browser-testing.md`](docs/browser-testing.md); it is not part of the deployed Duno app.

## Next setup steps

1. For Composer operations, run `C:\xampp\php\php.exe -d extension=intl -d extension=zip .tools\composer.phar install`.
2. Configure a host-specific `.env` in the application root using the values shown by the hosting panel; never reuse the local `.env`. See the protected configuration steps in `docs/deployment.md`. See `docs/deployment.md` and `docs/database-setup.md`.
3. Before upload, verify the target domain's assigned web directory and repeat the root `.htaccess` privacy checks on the host. Local Apache confirms the repository deny rules, but host behavior remains unverified.

The starter and Shield were installed from their official Composer packages. Do not substitute hand-written framework files.

## Open in VS Code

In VS Code, choose **File → Open Folder** and select `C:\xampp\htdocs\Talas`. The local, untracked workspace settings point PHP validation to XAMPP; each developer should use their own PHP path.
