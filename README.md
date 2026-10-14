# Talas

Local VS Code workspace for the one-month Talas AppDev project. The agreed stack is CodeIgniter 4, Shield authentication, MySQL/MariaDB, server-rendered views, CSS, and small JavaScript modules. InfinityFree is the planned deployment host.

## Current state

The official CodeIgniter 4 app starter 4.7.5 and CodeIgniter Shield 1.4.1 are installed and locked in Composer. Public auth routes and session-protected student/admin landing routes are configured. Database schema, first-admin provisioning, and product features are still pending. The development blueprint is in `docs/development-blueprint.md`, coding instructions are in `AGENTS.md`, and the Git/GitHub procedure is in `docs/version-control.md`.

## Local prerequisites found

- VS Code and Git are available.
- XAMPP PHP is at `C:\xampp\php\php.exe` (8.2.12). MySQL tools are under `C:\xampp\mysql\bin`.
- The PHP `intl` DLL exists but is disabled in XAMPP's default configuration. Use `-d extension=intl` for local CLI commands. The ZIP DLL also needs `-d extension=zip` for Composer archive installs.
- Composer is kept project-local at `.tools/composer.phar` (ignored by Git); run it with `C:\xampp\php\php.exe .tools\composer.phar`.

## Next setup steps

1. For Composer operations, run `C:\xampp\php\php.exe -d extension=intl -d extension=zip .tools\composer.phar install`.
2. Create a local MySQL database and untracked `.env`; add migrations and complete database-backed auth checks and first-admin provisioning.
3. Verify the specific InfinityFree account's PHP extensions, HTTPS, `.htaccess` behavior, and private-path denials before the first upload.

The starter and Shield were installed from their official Composer packages. Do not substitute hand-written framework files.

## Open in VS Code

In VS Code, choose **File → Open Folder** and select `C:\xampp\htdocs\Talas`. The local, untracked workspace settings point PHP validation to XAMPP; each developer should use their own PHP path.
