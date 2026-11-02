# Security Test Cases

Tests were run from the linked isolated worktree `C:\Users\Ahmer\AppData\Local\Temp\Talas-security-audit`. The suite used the repository's PHPUnit configuration and its configured in-memory SQLite database. No hosted or developer database was contacted. The first run exposed a missing local PHP `sqlite3` extension; the installed `php_sqlite3.dll` was enabled for the successful test process only.

## Executed tests

| Case | Coverage / existing test evidence | Result |
|---|---|---|
| Full PHPUnit suite after remediation | Authentication, registration, roles, student CRUD, validation, private check-ins, plans, sessions, reports, progression, unit services, CSP nonce policy | **Passed: 119 tests, 1,280 assertions** |
| CSP nonce regression | `ContentSecurityPolicyTest::testFinalizeReplacesInlineNoncePlaceholdersAndBuildsPolicy` | **Passed: nonce attributes match CSP sources; inline script attributes and framing are restricted** |
| Local production `/terms` response | PHP development server with production environment; no external host/database | **Passed: CSP, HSTS, no-store, and SecureHeaders observed** |
| Public registration cannot assign admin role | `AuthenticationFlowTest::testRegistrationAssignsStudentRoleAndIgnoresSubmittedAdminRole` | Passed as part of suite |
| Registration requires consent | `AuthenticationFlowTest::testRegistrationRequiresTermsAndPrivacyConsentOnTheServer` | Passed as part of suite |
| Unknown reset email receives neutral response | `AuthenticationFlowTest::testUnknownEmailPasswordRecoveryUsesNeutralResponse` | Passed as part of suite |
| Valid reset changes password and code cannot be reused | `AuthenticationFlowTest::testValidPasswordResetCodeChangesPasswordAndCannotBeReused` | Passed as part of suite |
| Five incorrect reset codes invalidate code | `AuthenticationFlowTest::testFiveIncorrectPasswordResetCodesInvalidateTheCode` | Passed as part of suite |
| Student/admin and signed-out route boundaries | Admin, subject/topic/session/check-in feature tests | Passed as part of suite |
| Cross-account access and modification | Foreign subjects/topics, plan items, sessions and check-ins through owner model/controller flows | Passed as part of suite |
| CSRF on state changes | Subject/topic/session/check-in feature tests | Passed as part of suite |
| Input validation | Rating, topic, session, plan, account and reset validation tests | Passed as part of suite |
| Private check-in reporting | Check-in values omitted from admin and student Aralytics views where required | Passed as part of suite |

The CSP nonce regression test was added in the isolated worktree. These are newly executed results for this remediation snapshot, not a restatement of historical CI results.

## Current-main integration follow-up

A focused remediation branch based on current `main` at `4f17fe6` ran the full current project suite: **120 tests, 1,318 assertions, passed**. An earlier disposable mixed-snapshot copy had three academic-program/subject test mismatches; it combined stale audit-snapshot files with current-source changes and was superseded by the full run on the clean current-main-based worktree. Temporary copies were removed afterward.

## Not executed

| Case | Reason / next step |
|---|---|
| Unauthenticated and unauthorized live route requests | No HTTP app/browser was started; use a local synthetic app or exact hosted release. |
| Cross-account POST/update/delete and forged foreign references on host | Local automated coverage passed; deployment doc marks parts of live cross-owner writes unverified. Repeat using synthetic accounts and disposable records on the reviewed release. |
| CSRF browser flow and session invalidation in a real browser | Feature tests passed; manual browser check was outside this read-only audit run. |
| Browser compatibility and complete route-class header coverage | Local `/terms` header capture and CSP nonce unit test passed. Homepage local probe returned 500 because the snapshot has no database; no browser or InfinityFree header capture was performed. |
| Private path denial (`.env`, app, vendor, writable, backups, Git metadata) | Source and prior deployment notes inspected. No fresh Apache/host probe run. `.env` status remains explicitly unverified in existing evidence. |
| Password recovery email/resend/expiry via SMTP | No external mail was sent. Use an injected local mail fake or a dedicated test SMTP sink. |
| Password-reset timing enumeration | Code branch inspected; timing was not measured. |
| PWA install/offline/cache contents/logout/back behavior | Worker source reviewed; no browser session or r9 cache test. |
| Dependency vulnerability audit | `composer audit --locked` was attempted; the configured proxy at `127.0.0.1:9` refused the Packagist connection. Retry in the approved network-enabled workflow. |
| Production PHP error disclosure | Production error configuration inspected; no disposable fault was triggered. |

## Test execution command

```powershell
C:\xampp\php\php.exe -d extension=php_sqlite3.dll vendor\bin\phpunit --no-coverage
```

The process ran only in the isolated snapshot. Its PHPUnit configuration selects `:memory:` SQLite in `ENVIRONMENT=testing`; feature tests also assert that database driver and database name during setup.
