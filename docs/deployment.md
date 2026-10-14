# Deployment and security gate

**Target:** InfinityFree free hosting. This document is a preflight and release checklist, not evidence that a specific account has passed it. No host account has been inspected from this repository.

## Before framework installation

- [ ] Confirm the specific account PHP version is compatible with the pinned CodeIgniter 4 and Shield releases.
- [ ] Confirm `intl`, `mbstring`, and MySQLi/PDO MySQL are enabled on the account.
- [ ] Confirm HTTPS works and determine how the account handles `.htaccess` rewrites and deny rules.
- [ ] In the hosting Domains list, identify the exact web directory mapped to the target domain (often `htdocs` or `<domain>/htdocs`); plan the deployable arrangement so application code, dependencies, writable data, and configuration are not directly downloadable. The document root cannot be assumed to be the same for every domain.
- [ ] Use only a disposable database and synthetic records for the first probe.

Do not infer these results from provider marketing pages or local XAMPP behavior. Record observed account behavior, date, and evidence below before selecting the dependency versions and deployment layout.

## Non-sensitive staging probe

The one-time hosting probe has been completed and its reported results are recorded below. Its upload fixtures were temporary and are not part of the application or Git snapshot. If the probe must be repeated, create fresh disposable fixtures without credentials or personal data; do not commit or deploy them with Talas. Verify:

- [ ] HTTPS route serves the expected non-sensitive response.
- [ ] Direct requests to `.env`, `app`, `writable`, `vendor`/`system`, configuration files, and backup files return 403 or 404.
- [ ] Rewrites route requests correctly without exposing source files or directory listings.
- [ ] Error responses do not show stack traces, local paths, secrets, or request data.
- [ ] The probe does not include database credentials or real personal data.

Treat any readable private path as a release blocker. A rewrite to a public front controller does not by itself prove private files are denied.

## Release checklist

- [ ] Upload a reviewed snapshot over FTP/FTPS; exclude `.env`, credentials, real records, local logs, development-only packages, and backups.
- [ ] Configure secrets through the documented protected configuration method; confirm the file cannot be fetched directly.
- [ ] Apply reviewed migrations using the approved one-time process or reviewed SQL import. Remove any one-time setup endpoint immediately afterward.
- [ ] Use synthetic demo accounts/data only.
- [ ] Smoke-check sign-in/out, student/admin boundaries, cross-user isolation, subject/topic/session flows, summaries, print output, HTTPS, and all protected-path denials.
- [ ] Remove temporary probe files and confirm they are inaccessible after removal.
- [ ] Record the deployed revision, date, checks, and any host behavior that remains unverified.

## Account verification record

| Check | Observed value / evidence | Date | Status |
| --- | --- | --- | --- |
| PHP version | PHP 8.4.25 reported by the probe; the locked CI4/Shield versions meet the documented PHP requirement. | 2026-10-14 | Observed |
| CI4/Shield PHP compatibility | Composer installed CI4 4.7.5 and Shield 1.4.1; `composer.lock` records the resolved versions. CI4 4.7 supports the host PHP 8.4.25 reported by the owner, and Shield 1.4.1 requires PHP `^8.2`. Sources: [CI4 server requirements](https://www.codeigniter.com/user_guide/intro/requirements.html), [CI4 releases](https://codeigniter.com/download), [Shield 1.4.1 package metadata](https://packagist.org/packages/codeigniter4/shield). | 2026-10-14 | Resolved locally; host compatibility based on owner report |
| `intl`, `mbstring`, `mysqli`, `pdo_mysql` | All four reported available by the probe. | 2026-10-14 | Passed |
| HTTPS | Probe reports HTTPS: yes. | 2026-10-14 | Passed |
| `/health-check` rewrite | User confirms it displays the same probe output as direct `index.php`. | 2026-10-14 | Passed |
| Direct `/index.php` in probe folder | User confirms `index.php` is present at the mapped path and loads at `https://talas-dev.page.gd/talas-host-probe/index.php`; it reports PHP 8.4.25 and HTTPS: yes. Automated fetch from this environment was closed by the host before a response could be read. | 2026-10-14 | Passed |
| Static `/static-check.txt` in probe folder | User confirms the harmless marker text loads from the mapped directory. | 2026-10-14 | Passed |
| `/.env.probe` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| `/app/probe.txt` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| `/writable/probe.txt` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| `/vendor/probe.txt` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| `/system/probe.txt` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| `/tests/probe.txt` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| `/docs/probe.txt` denial (fixture uploaded) | User reports 403/404; fixture contents not exposed. | 2026-10-14 | Passed |
| Talas root `.htaccess` against real project paths | Root deny rules now cover application, dependency, writable, test, docs, config, and local-tool paths. They have not yet been checked through local Apache or the host. | 2026-10-14 | Not verified |
| Temporary probe cleanup | User reports removing `talas-host-probe`; confirms its public marker URL returns 404 Not Found. | 2026-10-14 | Passed |
| Database import/migration path | User confirms the MySQL Databases panel is accessible; no database has been created. phpMyAdmin access and the Import tab are deferred by owner decision. | 2026-10-14 | Pending |
