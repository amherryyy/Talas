# Duno Security Audit Summary

**Assessment date:** 2026-11-02

**Scope:** Local repository snapshot at `main` HEAD `60513916e73af45ad3d5c3dae1e67028827ec1f9`, overlaid with the active uncommitted application and test changes present on 2026-11-02. The audit includes the new password reset flow. `.env`, local runtime data, and Git metadata were excluded.

**Execution:** Source/configuration review and remediation in the linked isolated Git worktree `C:\Users\Ahmer\AppData\Local\Temp\Talas-security-audit`. The development checkout was not modified by this work.

**Integration follow-up:** The fixes were reapplied on a focused branch from current `main` at `4f17fe6`. The full suite passed there with the current project files. This confirms code/test integration but does not expand the original security review to every change in PR #21.

## Assessment

The inspected application has a solid baseline for a small server-rendered CI4 app: fixed routes; session and role gates; server-side owner scoping for student records; global CSRF; input validation and escaped views; production cookie protections; no-store dynamic responses; rate limits; and a PWA worker allowlist that does not cache navigations or student data. Existing in-memory feature tests cover important cross-account read/write denials, private check-ins, admin separation, CSRF, and password reset safeguards.

No confirmed exploitable application vulnerability was found in the local source review and tests. The two informational hardening observations have been remediated in this worktree: production CSP/HSTS response handling is configured and the root deny rule covers `build/`. This is not proof that Duno is secure. No live r9 request, browser session, hosted database, SMTP delivery, or external dependency advisory service was tested. Current host evidence in `docs/deployment.md` is primarily from r8; r9 marker text alone does not prove full-file parity or behavior.

| Severity | Confirmed findings |
|---|---:|
| Critical | 0 |
| High | 0 |
| Medium | 0 |
| Low | 0 |
| Informational observations | 2 (both remediated in the local worktree; live verification remains open) |

The two observations were defense-in-depth/configuration consistency items, not demonstrated exploits. Their local fixes are recorded in [vulnerability findings](vulnerability-findings.md); verify them on the exact hosted release before considering them closed for deployment.

## Top five security risks to close

1. **Current deployment behavior is not established.** The owner confirmed the r9 marker, while direct denial, security headers, cross-owner writes, and full uploaded-file parity were checked only against earlier releases or local tests.
2. **Password recovery may reveal account existence through response timing.** The code returns a neutral redirect, but only sends email for an activated account and does so synchronously. Timing was not measured with a local mail stub or SMTP, so this is an unverified risk.
3. **Hosted path denial must be rechecked against the current release.** The source-root `build/` deny rule is fixed locally, but the deployment record says the `/.env` HTTP result had no status and was not verified as a 403/404. Host behavior and exact package parity remain open.
4. **The newly configured CSP/HSTS have not been verified on InfinityFree.** A local production-mode HTTP response emitted both; host-level TLS/proxy behavior and CSP compatibility in a browser remain unverified.
5. **Dependency advisory status is not current for this assessment.** `composer audit --locked` was attempted on the focused worktree but could not connect to Packagist because the configured proxy at `127.0.0.1:9` refused the connection; review the current lockfile in a network-enabled workflow before public release.

## Highest urgency

There are no confirmed critical or high vulnerabilities requiring emergency remediation. Before public deployment, repeat the deployment security checks against the exact r9 files and verify private-path denials, HTTPS headers, and two-account read/write isolation. If the recovery timing difference is measurable, make the public response path uniform or queue the mail operation through an approved design.

## Verification performed

- Inspected routes, filters, Shield configuration, student/admin controllers, owner-scoped models, reset-code model and migration, CI security/session/cookie settings, `.htaccess` rules, error handling, PWA caching, and relevant tests.
- Ran `php vendor/bin/phpunit --no-coverage` in the isolated worktree with PHP 8.2.12 and the installed `php_sqlite3.dll` enabled for the process. Result: **119 tests, 1,280 assertions, passed**, including a new CSP nonce/policy regression test. PHPUnit selected the configured `:memory:` SQLite database; tests verified this in setup.
- A local production-mode PHP response on `/terms` emitted CSP, HSTS, no-store, and existing SecureHeaders. A separate homepage probe returned 500 because the isolated snapshot has no database; homepage rendering and nonce behavior were therefore verified by the focused CSP unit test, not that route probe.
- On the focused branch based on current `main`, the full suite passed: **120 tests, 1,318 assertions**. An earlier disposable mixed-snapshot copy produced three stale UI-test mismatches; that copy was superseded by the clean current-main-based worktree run.
- Did not run a browser, live-host request, database migration against the developer or hosted database, SMTP delivery, scanner, or external network scan. Composer advisory lookup was attempted but the configured proxy refused the connection. Full browser compatibility and InfinityFree behavior remain unverified.

## Evaluation readiness

The inspected local code appears suitable for **controlled student and IT evaluation with synthetic accounts**, provided the evaluation is on the intended reviewed package and HTTPS host and the evaluator is told that r9 parity and some host checks remain open. This is a source-and-test assessment, not ISO/IEC 25010:2023 certification or a guarantee of security.

Additional security verification is recommended before public deployment: verify exact package provenance/parity; repeat direct path denial and response-header checks; exercise cross-account read, update, delete, and forged-reference cases on a disposable hosted test dataset; verify password reset delivery, expiry, resend/attempt limits, and recovery timing; and run the current locked dependency audit.
