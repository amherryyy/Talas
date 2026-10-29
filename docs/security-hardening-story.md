# Security baseline hardening

## Scope

Review the existing authentication, authorization, CSRF, student ownership, cookie, error, and deployment controls, then close small configuration gaps that can be addressed without changing product behavior.

## Changes

- Enabled CodeIgniter's built-in `SecureHeaders` filter for application responses. It supplies frame, MIME sniffing, permitted cross-domain policy, and referrer protections.
- Made database debug output default off in production, even if a production environment file omits an explicit `DBDebug` override.
- Added common credential, CSRF, authorization, and private check-in field names to the framework's sensitive trace redaction list.
- Marked all dynamic application responses `no-store` so auth forms, role-specific links, and student/private-check-in pages are not retained in browser or intermediary caches. Versioned public assets keep their separate static cache policy.
- Expanded both the repository-root and generated-hosting `.htaccess` rules to deny common database, archive, log, backup, and temporary file extensions.
- Added S-09 traceability and post-upload checks for response headers, safe database errors, HTTPS/HSTS, and direct private-path denials.

## Existing controls reviewed

The app uses Shield's session authenticator and auth route throttling; registration defaults to the student group. CSRF is globally enabled with session storage. Student and admin routes use explicit server-side filters, with a separate student/admin role boundary. Subject, topic, session, plan, check-in, and curriculum import methods use owner-scoped queries or owner validation. Session cookies inherit production Secure, HttpOnly, and SameSite=Lax settings. The deployment package's root `.htaccess` denies private directories, dotfiles, and common backup/database file extensions.

## Row-level access boundary

The production target is MySQL/MariaDB on InfinityFree, which does not provide PostgreSQL-style row-level security policies. Duno therefore enforces row-level access in its application data layer: subject rows are scoped by `user_id`; topic rows are scoped through their owning subject; plans and streak/check-in/session rows are scoped by `user_id`; and plan items are scoped through a plan whose `user_id` matches the authenticated student. Writes and nested references use the same owner checks. Student controllers derive the owner ID from the authenticated session rather than accepting it from request data. Admin dashboard queries are separate and return aggregate counts only; private check-ins are excluded.

This is application-layer row-level security, not a database-enforced policy. Keep student table access behind these owner-scoped model methods. A future move to a database with native RLS would require per-request database identity setup and policies for every owned table and relation; it is not enabled by this MySQL deployment and no migration is required for the current controls.

## Verification status

The full PHPUnit suite passed (86 tests, 874 assertions), PHP syntax passed for 172 app/test files, and `git diff --check` passed. An HTTP/browser header inspection has not been run. Composer's locked dependency audit could not reach Packagist in this environment, so current advisory status is unverified. InfinityFree response headers and private-path denials still require a post-upload check.
