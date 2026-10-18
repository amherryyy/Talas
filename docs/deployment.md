# Deployment and security gate

**Target:** InfinityFree free hosting. This document is a preflight and release checklist, not evidence that deployment is complete. The owner confirms the domain `talas-dev.page.gd` maps to `htdocs`; other account details remain subject to the verification record below.

## Before framework installation

- [ ] Confirm the specific account PHP version is compatible with the pinned CodeIgniter 4 and Shield releases.
- [ ] Confirm `intl`, `mbstring`, and MySQLi/PDO MySQL are enabled on the account.
- [ ] Confirm HTTPS works and determine how the account handles `.htaccess` rewrites and deny rules.
- [x] In the hosting Domains list, identify the exact web directory mapped to the target domain. The owner confirms `talas-dev.page.gd` maps to `htdocs` (2026-10-18). The prepared layout puts the front controller and public assets at that root while deny rules protect the application, vendor, and writable paths. Confirm any future domain mappings separately.
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

- [ ] Upload a reviewed snapshot over FTP/FTPS; include `public/assets/images/duno-mascot.png`, `public/assets/icons/solar/`, and the matching CSS; exclude `.env`, credentials, real records, local logs, development-only packages, and backups.
- [ ] Configure secrets through the documented protected configuration method; confirm the file cannot be fetched directly.
- [ ] Apply reviewed migrations, including subjects, topics, daily plans, study sessions, and private check-ins, using the approved one-time process or reviewed SQL import. Remove any one-time setup endpoint immediately afterward.
- [ ] Use synthetic demo accounts/data only.
- [ ] Smoke-check sign-in/out, student/admin boundaries, aggregate-only admin dashboard totals (student accounts, subjects, topics, saved sessions, and saved study minutes), absence of identities/study details/check-in values or patterns, subject and topic create/list/view/edit/delete, search and combined topic filters, daily-plan generation and adjustments within the available-time cap, timer review and explicit session save, saved-session edit/delete, private check-in create/list/edit/delete with multiple entries per day, the owner-only last-7-days check-in summary and its empty state, selected-period Aralytics pattern counts and paired rating bars with matching numeric table, chart exclusion from print/admin/search/exports, check-in owner isolation, subject/topic deletion restrictions while referenced by sessions, Aralytics periods (today, 7 days, 30 days, all time), study summary and subject/topic totals, dated sessions, print layout, curated study suggestion cards and source links, HTTPS, and all protected-path denials.
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
| Talas root `.htaccess` against real project paths | A temporary Apache 2.4.58 instance bound only to `127.0.0.1:8085` returned 403 for `/.env`, `app/Config/Routes.php`, `writable`, `vendor`, `tests`, `docs`, and `composer.json`; the public Aralytics JavaScript asset returned 200. The temporary instance was stopped. Host-specific path behavior remains unverified. | 2026-10-17 | Passed locally; host pending |
| Temporary probe cleanup | User reports removing `talas-host-probe`; confirms its public marker URL returns 404 Not Found. | 2026-10-14 | Passed |
| Database import/migration path | Owner reports creating the hosted database and successfully importing the schema-only initial file on 2026-10-18. The owner confirms phpMyAdmin shows 15 tables and nine rows in `migrations`; app connection remains unconfigured. | 2026-10-18 | Imported; schema verified by owner |

### Initial hosted database import

For a newly created, empty hosted database, select that database in phpMyAdmin and import [`duno-hosted-initial-schema.sql`](duno-hosted-initial-schema.sql). The file creates the 15 current CodeIgniter, Shield, Settings, and Duno tables and records the nine already-applied migrations. It contains no account or study records and does not create or select a database. Do not import it into a database with existing tables, import it twice, upload a local database backup, or include this SQL file in the public FTP deployment. After import, verify that the `migrations` table contains nine rows before configuring the hosted app. The owner confirmed 15 tables and nine migration rows in phpMyAdmin. App connectivity remains unverified.

### Hosted application database configuration

Use the exact MySQL hostname, database name, username, password, and port shown in the hosting Control Panel. Do not assume `localhost`, and do not share the password in chat. For the prepared `htdocs` layout, create a host-specific `.env` in `htdocs`, alongside `index.php`, `app`, and `vendor` (the deployable package intentionally omits `spark`). Never upload or reuse the local development `.env`. Set these values using the host's actual details:

```dotenv
CI_ENVIRONMENT = production
app.baseURL = 'https://YOUR-DOMAIN/'
database.default.hostname = 'HOST-MYSQL-HOSTNAME'
database.default.database = 'HOST-DATABASE-NAME'
database.default.username = 'HOST-DATABASE-USERNAME'
database.default.password = 'HOST-DATABASE-PASSWORD'
database.default.DBDriver = MySQLi
database.default.port = HOST-MYSQL-PORT
database.default.DBDebug = false
```

Upload the prepared application package first, without `.env`, and confirm `https://YOUR-DOMAIN/.env` returns 403 or 404. Then upload the host-specific `.env` into `htdocs` and repeat that request; it must still return 403 or 404 before signing in or adding records. Do not put the environment file in a publicly served assets directory. If the deployed folder layout changes, adjust the configuration path and access-denial check before uploading secrets.
