# Deployment and security gate

**Target:** InfinityFree free hosting. The owner reports R-04 is all good as of 2026-10-25. This document preserves the earlier live checks and notes where the precise deployed revision was not independently confirmed. The owner confirms the domain `talas-dev.page.gd` maps to `htdocs`; other account details remain subject to the verification record below.

## Current deployment status (updated 2026-10-25)

The owner-reported host probe recorded `talas-dev.page.gd` mapped to `htdocs`, PHP 8.4.25, the required PHP extensions, HTTPS, and route rewriting. On 2026-10-25, the owner reran the focused runtime check and reported PHP 8.4.27 with `intl`, `mbstring`, `mysqli`, and `pdo_mysql` all available. The owner also reports that the initial database schema import succeeded (15 tables and nine migration rows), and accepted R-04 as all good. Live outcomes are recorded below; the exact hosted revision and `/.env` HTTP status were not independently confirmed in this session.

Use [`scripts/Build-InfinityFreePackage.ps1`](../scripts/Build-InfinityFreePackage.ps1) to assemble an `htdocs`-root package locally. It copies the application and public assets, adjusts the front controller to load `app/Config/Paths.php` from the root, writes combined route-rewrite and private-path denial rules, creates fresh writable directories, seeds dependencies from local `vendor/`, and runs Composer with `--no-dev` in the package directory. It leaves the local `vendor/` and writable data untouched and never copies `.env`, tests, docs, local tools, or backups. The output is a uniquely named ignored directory under `build/`; inspect it and complete the checks below before any FTP upload. The script expects XAMPP PHP at `C:\xampp\php\php.exe` and `.tools/composer.phar`.

Run it from the repository root in PowerShell:

```powershell
powershell.exe -NoProfile -ExecutionPolicy Bypass -File .\scripts\Build-InfinityFreePackage.ps1
```

The process-scoped execution-policy setting above does not change PowerShell's machine policy. The reviewed local package is currently at `build/infinityfree-htdocs-package-20261025/` (ignored by Git, about 13.4 MB). Composer removed 30 development-only packages from the staging copy without changing the project's `vendor/`; its configured package source could not be reached, so the local locked production dependencies were used. The package inventory check found the required app, dependencies, assets, and fresh writable folders, with no local `.env`, logs, sessions, cache records, tests, or development tools. A local Apache check returned 403 for `/app/Config/Paths.php`, `/vendor/.../Boot.php`, `/writable/session/index.html`, `/.env`, `/composer.json`, and `/README.md`; the public navigation JavaScript returned 200. Apache confirmed the `.htaccess` syntax and accepted `FollowSymLinks`. The package uses the `index.php?/$1` rewrite form from the earlier successful host probe; app routing and PHP responses remain unverified in the local check. The script stops if its output directory already exists or Composer fails; it leaves partial output for inspection and does not upload anything. It removes Composer's root manifests after dependency installation. Do not add the host `.env` until the package has been reviewed and uploaded; configure secrets using the process below.

A second package was assembled and checked from the restart/package feature branch before PR #9 merged, at `build/infinityfree-htdocs-package-pr9-20261025/`. Its root layout, `.env` exclusion, front-controller syntax, selected source hashes, and deny rules were checked; Composer used the copied locked vendor tree because Packagist was unreachable. PR #9 and the validation record in PR #10 are now merged. This package has not been uploaded. The app/assets tree in it corresponds to the merged PR #9 content, but it is not a build artifact produced from the current `main` checkout. Rebuild from current `main` before treating it as a release candidate.

After PR #10 merged, a clean package was rebuilt from local `main` at `4c02a39` into `build/infinityfree-htdocs-20261025-162031/`; its upload archive is `build/infinityfree-htdocs-main-4c02a39.zip` (about 4.8 MB). Composer could not reach Packagist and completed using the copied locked dependency tree, removing 30 development packages. The package has only the expected root entries, no `.env` or Composer manifests, a PHP-syntax-valid `index.php`, and matching SHA-256 hashes for the home controller/view and signed-out navigation JavaScript. The ZIP was inspected to confirm that it contains `.htaccess` (including the hidden file), `index.php`, and no `.env`. The owner later reported R-04 is all good, but did not identify the exact revision uploaded; treat this artifact as a verified local build rather than confirmed proof of the current live files. Keep the host `.env` private and do not expose or overwrite it during future package uploads.

To reconfirm the actual PHP runtime, upload a temporary script that prints only `PHP_VERSION` and `extension_loaded()` results for `intl`, `mbstring`, `mysqli`, and `pdo_mysql`. Do not use a public `phpinfo()` page: it reveals more server configuration than needed and may be blocked. Fetch the result once over HTTPS, then delete the script and confirm its URL returns 404. Never place this diagnostic in a release package.

The first attempt to open `https://talas-dev.page.gd/runtime-check.php` returned InfinityFree's 403 page (owner report, 2026-10-25). After checking the `htdocs/.htaccess` rules, the owner successfully obtained the focused runtime result recorded below and later confirmed that deleting the temporary file made its URL return 404. The owner also reported that the site homepage returned 403 before Duno was deployed; that root response may be expected while no app index is present.

The owner uploaded the prepared ZIP through InfinityFree's File Manager and verified the deployed root `.htaccess` and `index.php` contain the reviewed package markers. On 2026-10-25, the Duno homepage and `assets/js/public-bottom-nav.js` loaded in the browser. The owner reports that hosted student registration, login, session persistence, and sign-out worked. A separate synthetic hosted account was promoted to the `admin` group in phpMyAdmin; it can open the admin dashboard, while the student account receives Forbidden on `/index.php?/admin`. The owner confirms the dashboard shows the approved aggregate view. These checks confirm hosted database reads and writes through the application.

The owner reports direct requests to the tested application/vendor/writable and Composer paths returned 403. The owner confirms the hosted `.htaccess` contains the dotfile denial rule `RewriteRule (^|/)\. - [F,L]`. In DevTools Network, the `/.env` document request failed with no HTTP status (consistent with the previously reported `ERR_EMPTY_RESPONSE`); the rule is present, but the host response itself is not verified as an HTTP 403/404. The uploaded ZIP was removed from remote `htdocs`. The owner also confirms a core synthetic-student walkthrough covering subjects/topics, planning, timer sessions, check-ins, and Aralytics. Remaining live work is to verify `.env` protection with a successful HTTP denial response or another direct check, itemize detailed feature acceptance, and review production error responses. The deployed package came from the local `main` worktree at `c83f664` plus uncommitted changes, so it is not identified by a clean commit.

## Before framework installation

- [x] Confirm the specific account PHP version is compatible with the pinned CodeIgniter 4 and Shield releases (PHP 8.4.27 reported on 2026-10-25).
- [x] Confirm `intl`, `mbstring`, and MySQLi/PDO MySQL are enabled on the account (all four reported available on 2026-10-25).
- [x] Confirm HTTPS works and determine how the account handles `.htaccess` rewrites and deny rules (HTTPS and a rewrite route passed in the 2026-10-14 temporary probe; listed probe paths returned 403/404 per owner report. Actual Duno package rules still need a host check after upload).
- [x] In the hosting Domains list, identify the exact web directory mapped to the target domain. The owner confirms `talas-dev.page.gd` maps to `htdocs` (2026-10-18). The prepared layout puts the front controller and public assets at that root while deny rules protect the application, vendor, and writable paths. Confirm any future domain mappings separately.
- [x] Use only a disposable database and synthetic records for the first probe (schema-only hosted import; no host account or study records in the probe).

Do not infer these results from provider marketing pages or local XAMPP behavior. Record observed account behavior, date, and evidence below before selecting the dependency versions and deployment layout.

## Non-sensitive staging probe

The one-time hosting probe has been completed and its reported results are recorded below. Its upload fixtures were temporary and are not part of the application or Git snapshot. If the probe must be repeated, create fresh disposable fixtures without credentials or personal data; do not commit or deploy them with Talas. Verify:

- [x] HTTPS route serves the expected non-sensitive response (owner reported HTTPS and static/direct PHP probe success, 2026-10-14).
- [x] Direct requests to `.env`, `app`, `writable`, `vendor`/`system`, configuration files, and backup files return 403 or 404 in the disposable probe (owner reported the listed fixture paths denied, 2026-10-14; actual Duno package paths remain to be checked).
- [x] Rewrites route requests correctly without exposing source files or directory listings (owner reported `/health-check` rewrite success and probe path denials, 2026-10-14).
- [ ] Error responses do not show stack traces, local paths, secrets, or request data (not yet checked against the actual Duno package).
- [x] The probe does not include database credentials or real personal data (schema-only and non-sensitive fixtures).

Treat any readable private path as a release blocker. A rewrite to a public front controller does not by itself prove private files are denied.

## Release checklist

- [x] Upload the reviewed `htdocs` package via InfinityFree File Manager ZIP import; the owner verified the root front-controller and denial-rule markers, the Duno homepage, and the public navigation asset. The package intentionally omitted `.env`; the owner confirms the remote ZIP archive was deleted after extraction.
- [x] Hosted application database configuration is operational: the owner reports registration, login, and persistent sessions succeeded, confirming application reads and writes.
- [x] Import the reviewed schema-only SQL file; the owner confirmed 15 tables and nine migration rows. Hosted student registration then created synthetic account data. Hosted admin was provisioned by changing a separate synthetic account's Shield group through phpMyAdmin; no password or identity table was edited.
- [x] Use synthetic demo accounts for hosted authentication and role checks; no real student records have been entered.
- [x] Core hosted synthetic-student smoke-check: the owner confirms subject/topic changes, planning, timer sessions, check-ins, and Aralytics were exercised.
- [x] Hosted plan-generation time-cap check: with one unfinished topic and 15 minutes available, the owner confirmed the generated block showed 15 planned of 15 available minutes.
- [x] Hosted plan-edit time-cap check: the owner tried increasing that 15-minute block to 16 minutes; the app rejected the edit.
- [x] Owner-directed follow-up: stop investigating the `/.env` browser result. The hosted `.htaccess` dotfile denial rule is present; DevTools showed `ERR_EMPTY_RESPONSE` without an HTTP status. This is recorded as the host's observed behavior, not as a verified 403/404.
- [x] Owner confirms the hosted cross-student check-in privacy and admin-visibility checks described below were completed previously; no repeat requested.
- [x] Owner accepted R-04 as all good on 2026-10-25. Individual outcomes for the remaining itemized checks (search/filter combinations, edit/delete and multi-entry behavior, private check-in summary details, Aralytics period/print details, admin aggregate totals, referenced-record deletion restrictions, and production error responses) were not restated in this conversation; retain them as owner-accepted but not individually documented.
- [x] Remove temporary probe files and confirm they are inaccessible after removal (the earlier host-probe marker returned 404; the `runtime-check.php` URL now returns 404 per owner report, 2026-10-25).
- [x] Record the deployed snapshot, date, observed checks, and remaining host limitations; the uploaded package came from `main` at `c83f664` plus uncommitted worktree changes.

## Account verification record

| Check | Observed value / evidence | Date | Status |
| --- | --- | --- | --- |
| PHP version | PHP 8.4.27 reported by the focused runtime check; the locked CI4/Shield versions meet the documented PHP requirement. | 2026-10-25 | Passed |
| CI4/Shield PHP compatibility | Composer installed CI4 4.7.5 and Shield 1.4.1; `composer.lock` records the resolved versions. CI4 4.7 supports the host PHP 8.4.27 reported by the owner, and Shield 1.4.1 requires PHP `^8.2`. Sources: [CI4 server requirements](https://www.codeigniter.com/user_guide/intro/requirements.html), [CI4 releases](https://codeigniter.com/download), [Shield 1.4.1 package metadata](https://packagist.org/packages/codeigniter4/shield). | 2026-10-25 | Resolved locally; host compatibility based on owner report |
| `intl`, `mbstring`, `mysqli`, `pdo_mysql` | All four reported available by the focused runtime check. | 2026-10-25 | Passed |
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
| Database import/migration path | Owner reports creating the hosted database and importing the schema-only initial file on 2026-10-18; phpMyAdmin showed 15 tables and nine migration rows. On 2026-10-25 the owner confirmed hosted registration, login, persistent session, and logout, demonstrating application database reads/writes. | 2026-10-25 | Passed by owner report |
| Current PHP runtime check | Owner reports `PHP_VERSION=8.4.27`; `intl`, `mbstring`, `mysqli`, and `pdo_mysql` are all `yes`. Owner confirms the temporary diagnostic URL returns 404 after cleanup. | 2026-10-25 | Passed and removed |
| Production package assembly and upload | `scripts/Build-InfinityFreePackage.ps1` assembled `build/infinityfree-htdocs-package-20261025/`; production dependencies came from the locked local vendor tree after Composer's package source was unreachable. The owner imported its ZIP through InfinityFree File Manager, verified `.htaccess`/`index.php` markers, and confirmed deleting the remote archive. The uploaded worktree was `main` at `c83f664` plus uncommitted files. A package and ZIP were then rebuilt from current `main` at `4c02a39` and validated locally. Owner reports R-04 is all good, but the precise revision now hosted was not confirmed in this session. | 2026-10-25 | R-04 accepted by owner; deployed revision not independently confirmed |
| Host homepage and public asset | Owner screenshot shows the Duno homepage; `assets/js/public-bottom-nav.js` displayed its JavaScript source in the browser. | 2026-10-25 | Passed by owner report |
| Deployed private path denials | Owner reports `/app/Config/Paths.php`, `/vendor/codeigniter4/framework/system/Boot.php`, `/writable/session/index.html`, and `/composer.json` returned 403. Owner confirms hosted `.htaccess` contains `RewriteRule (^|/)\. - [F,L]`. The `/.env` request failed in DevTools Network without an HTTP status (`ERR_EMPTY_RESPONSE`). | 2026-10-25 | Listed paths passed by owner report; `.env` rule present, host response unverified |
| Hosted student authentication | Owner reports a separate hosted synthetic student account registered, signed in, retained its session after refresh, and signed out. The local `priority-review@example.test` account was not present in the schema-only hosted database. | 2026-10-25 | Passed by owner report |
| Hosted admin authorization | Owner promoted a separate synthetic account from `student` to `admin` in `auth_groups_users` through phpMyAdmin. The admin can open the aggregate dashboard; the student receives Forbidden on `/index.php?/admin`. Owner confirmed dashboard content is good. | 2026-10-25 | Passed by owner report |
| Core hosted feature smoke-check | Owner confirms the synthetic student exercised subject/topic changes, planning, timer sessions, check-ins, and Aralytics. Owner confirms a generated plan with one unfinished topic and 15 minutes available showed 15 planned of 15 minutes, and an attempt to edit that block to 16 minutes was rejected. Owner also confirms previously completing cross-student check-in privacy and admin-visibility checks. Individual criteria such as filters, edit/delete, check-in summaries, print behavior, aggregate totals, and production error responses remain to be itemized. | 2026-10-25 | Core walkthrough, generated/edit cap, and privacy checks passed by owner report; other details pending |

### Initial hosted database import

For a newly created, empty hosted database, select that database in phpMyAdmin and import [`duno-hosted-initial-schema.sql`](duno-hosted-initial-schema.sql). The file creates the 15 current CodeIgniter, Shield, Settings, and Duno tables and records the nine already-applied migrations. It contains no account or study records and does not create or select a database. Do not import it into a database with existing tables, import it twice, upload a local database backup, or include this SQL file in the public FTP deployment. After import, verify that the `migrations` table contains nine rows before configuring the hosted app. The owner confirmed 15 tables and nine migration rows in phpMyAdmin and later confirmed database-backed registration/login on the host.

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
