# Security Evaluation Evidence

## Purpose and objectives

This assessment supports an academic evaluation of the Security characteristic of ISO/IEC 25010:2023 by describing what was inspected and exercised. It is not an ISO/IEC 25010 compliance claim, certification, or guarantee that the application is secure.

Objectives were to review authentication and recovery, authorization and student ownership, input/output handling, private check-ins, browser/session protections, PWA caching, deployment configuration, and existing security tests. Priority was given to cross-account data isolation and the 2026-11-02 uncommitted password recovery changes.

## Environment and scope

- **Application:** CodeIgniter 4.7.5 and Shield 1.4.1 per locked dependencies.
- **Runtime:** Windows, PHP 8.2.12. PHPUnit 10.5.66.
- **Source snapshot:** `main` at `60513916e73af45ad3d5c3dae1e67028827ec1f9` plus active tracked and untracked changes copied into the linked isolated worktree `C:\Users\Ahmer\AppData\Local\Temp\Talas-security-audit`. `.env` and local writable runtime data were excluded from runtime testing. The main development checkout was not modified by this work.
- **Database:** PHPUnit configured `ENVIRONMENT=testing` and an in-memory `:memory:` SQLite database. The feature tests assert that test database selection. The developer and hosted databases were not accessed.
- **Inspected scope:** Routes, filters, auth/session/cookie/security configuration, controllers/models, migrations, server-rendered views, root/app/public denial files, package assembly rules, public service worker, tests, and deployment documentation.
- **Out of scope:** Live r9 host, FTP package upload, SMTP, hosted database, external integrations, real student records, and broad scanning.

## Methods and tools

- Read-only review of the current source/configuration and repository status.
- Route, owner-scope, CSRF, caching, secret/configuration, and security-header searches using `rg` and targeted file inspection.
- PHPUnit feature and unit suite using synthetic in-memory records, including the added CSP nonce regression test.
- Local production-mode HTTP response check on `/terms` for emitted security headers; no outside network was contacted.
- Supplemental current-main integration test in a disposable copy at commit `4f17fe6`, with the security changes layered onto current application files.
- No production or public service requests, browser automation, destructive database operations, uncontrolled scanner, or real email delivery.

## Executed results

- PHPUnit: **119 passed, 1,280 assertions** with the installed SQLite extension enabled for the test process.
- CSP nonce regression: passed; generated style/script nonce attributes were present in the response body and matched the CSP policy, which also included `script-src-attr 'none'` and `frame-ancestors 'none'`.
- Local production `/terms`: CSP, HSTS (`max-age=31536000`), no-store, and standard SecureHeaders were observed. The home route probe returned 500 because this isolated environment has no configured database; homepage behavior was not inferred from that failed probe.
- Existing tests exercised registration role assignment, consent, neutral unknown-email reset response, reset-code expiry/attempt/use behavior, student/admin restrictions, owner isolation for major record classes, private check-in handling, validation, and CSRF.
- No confirmed exploitable vulnerability was established by the reviewed source and tests.
- The two informational observations have code-level fixes in this worktree: production CSP/HSTS response handling is configured and the source-root deny list includes singular `build/`. These are not host-verified, and the second did not receive an Apache path probe.
- The focused branch based on current `main` passed the full suite: 120 tests and 1,318 assertions. An earlier mixed-snapshot test copy was superseded; this integration run is not a full security reassessment of PR #21.

## Unverified risks and limitations

- The owner confirmed r9 marker text, but current full file parity and current host behavior were not independently verified. Historical live checks are documented against r8 or earlier.
- Existing deployment notes state that an earlier `/.env` request had no HTTP status; do not count that as a successful denial response.
- No InfinityFree header capture, manual browser flow, PWA installation/cache check, host denial probe, SMTP send, database-backed reset migration run, or real-time reset-enumeration timing measurement was performed. The local `/terms` check does not establish hosted proxy/TLS behavior.
- Dependency audit was not run in this offline assessment.
- Source and tests cannot establish server permissions, proxy behavior, database contents, TLS termination, SMTP configuration, or exact uploaded files.
- The source-root `.htaccess` observation is conditional. The documented package generator creates a more complete deployable root and excludes local `build/` contents.

## Corrective actions and evaluation statement

Before public deployment, verify exact package provenance and parity, host path denials, HTTPS/cookie/security headers, error behavior, browser CSP compatibility, and two-account read/write isolation on the exact release. Run `composer audit --locked` and exercise password recovery through a test mail sink. The local P2 code hardening is applied in this worktree; see the remediation plan for remaining checks.

The local implementation appears suitable for controlled evaluation with synthetic accounts when the reviewed package and host are used and these limitations are disclosed. This evidence does not justify a claim of full security, live-release parity, or ISO/IEC 25010:2023 compliance.
