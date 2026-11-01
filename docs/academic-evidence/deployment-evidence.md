# Deployment and maintenance evidence

## Target and architecture

Project deployment notes target InfinityFree free hosting through FTP/FTPS and an assigned `htdocs` document root. The workflow uses a locally assembled package containing the PHP app, public entry point/assets and production dependencies; it excludes secrets, development tests/docs and local tooling. The project does not assume SSH, server Composer, cron, remote MySQL, or an editable web root. See `docs/deployment.md` and `scripts/Build-InfinityFreePackage.ps1`.

## Recorded environment and release state

- Host details in project notes are owner-reported: PHP 8.4.27, required extensions, HTTPS, rewrite routing, host mapping to `htdocs`.
- On 2026-10-31, browser checks observed the live r4 marker and public PWA resources. On 2026-11-01, the owner reported r5 live acceptance and the r5 marker. This is a later owner report, not a contradiction in the timeline. Uploaded-file parity is still **NOT VERIFIED**; a marker proves only the marker text.
- Stale r6 archive `build/duno-full-release-20261101-r6.zip` has SHA-256 `F95CBD32FC661E515384A33EC5DDC912AFF4F118F10F248EAA81AB0C36AE92BC`; it predates the final role-denial header fix and must not be uploaded.
- Local r7 package `build/duno-full-release-20261101-r7.zip` has SHA-256 `B48283C634F98F9C3820C38AB5F9A035D380E97F28187456CEAE297DE87863C4`. It was built from clean source commit `5c7463cc77f4b3a3e1e129da86623c4d02d02ef3` and has not been uploaded. Live behavior remains unverified.
- No independent host request or file access was performed in this audit; host PHP and r5 status remain owner-reported.

## Database and migration state

The code uses CodeIgniter migrations and MySQL/MariaDB. A safe schema-only baseline SQL file is documented for a new empty database and is not for re-import into the existing host. The owner-reported hosted ledger is through row 18/batch 13, including migrations for program links, progression, study-history constraints, agenda start times, topic difficulty and owner-aware topic/plan-item constraints. `docs/database-setup.md` distinguishes local isolated review database state from hosted state. Do not execute migrations or SQL based on this evidence note.

The database-backed behavior is not established merely by migration rows. The current application revision must match columns/constraints before its code is described as live. The repository also documents the damaged original shared XAMPP MariaDB data directory; it was not accessed or altered in this audit.

## Operational controls and verification gaps

The package builder and deny rules address private app/config/vendor/writable paths and dotfiles; production configuration, HTTPS cookies, safe error pages and cache headers are represented in source. Historical probes include provider-generated 403 output for one application path. The `/.env` request displayed the offline fallback, which does not establish a denial status; the deployment note says not to repeat that probe. Path-by-path denial, current response headers, current error rendering, service-worker cache behavior and deployed cross-owner checks remain NOT VERIFIED.

## Release/maintenance tasks

1. After owner-approved release, record the exact live marker, uploaded archive hash, source commit, and schema migration ledger; preserve sanitized evidence.
2. Match the current app release to migration rows 1–18 before further release activity.
3. Complete the pending acceptance procedures against a declared revision/environment and synthetic records.
4. `composer audit --locked` passed on 2026-11-01 after clearing injected proxy variables for that process. Preserve the lockfile revision and result when preparing the final release.
5. Verify path denials without exposing secrets, production errors, security headers, no-store behavior, HTTPS/session behavior and public-only PWA caches. Follow the explicit instruction not to repeat the inconclusive `/.env` probe.
6. Preserve package hashes, upload date, marker, migration state, tester and post-upload outcomes for maintenance history.

No deployment, infrastructure, database, package, or production change was made for this audit.
