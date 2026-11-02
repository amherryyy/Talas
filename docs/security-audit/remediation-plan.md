# Remediation Plan

No P0 or P1 source-code correction is supported by the evidence gathered in this audit. The two local P2 code corrections are applied in the isolated worktree; remaining items close deployment and verification gaps before broader release.

| Priority | Action | Acceptance criteria | Evidence to retain |
|---|---|---|---|
| P1 before public deployment | Verify exact r9 package provenance and full uploaded-file parity; repeat direct path denial, headers, auth boundaries, and synthetic two-account read/write tests against the exact live build | `.env`, app, vendor, writable, backups and Git metadata receive an observed 403/404; HTTPS cookies and dynamic no-store behavior are present; student/admin route boundaries and cross-owner reads/writes/deletes are denied | Timestamped request results, sanitized headers/status, package inventory/hash, synthetic account test record |
| P1 before public deployment | Run `composer audit --locked` for the current lockfile in the authorized network-enabled build environment | Command completes successfully with no unresolved advisories, or each advisory has a recorded decision and upgrade plan | Sanitized command output and lockfile revision |
| P2 | **Applied locally:** production CSP and host-only HSTS response handling | CSP policy and nonce agreement pass the focused PHPUnit test; local `/terms` response emits CSP/HSTS. Browser compatibility and InfinityFree HTTPS/proxy behavior still need verification | `ContentSecurityPolicyTest`; sanitized local HTTP header capture; repeat on exact hosted release |
| P2 | **Applied locally:** add singular `build/` to repository-root `.htaccess` deny list | Source rule includes `build`; Apache/host response still needs a 403/404 probe | Source diff and local Apache/host request status |
| P2, conditional | Measure reset-request timing with a fake mail transport; make the path uniform if a meaningful account-existence signal is observed | Valid and invalid synthetic emails receive indistinguishable status/body/redirect and no useful timing distinction under repeated local measurements; mail failure remains generic to visitors | Focused automated test using a local mail fake; no real message sent |
| P2 | **Applied locally:** add reset `code` to exception trace sensitive-field redaction | `code` is in CI4 `sensitiveDataInTrace`; deliberate HTML/CLI trace test remains useful follow-up | Source review; exception-path regression test not added |

The last action is defense in depth: no clear reset code was observed in the current logs, and the report does not claim that one is currently leaked.

## Regression tests and follow-up verification

- Add route-class assertions for CSP/HSTS on auth, student, admin, 403, and 404 responses; public `/terms` was locally observed. Repeat on host and check browser compatibility.
- Assert the source-root `.htaccess` protects `build/` when the repository-root layout is supported.
- Use a fake email service to assert identical public responses for known and unknown accounts, code expiry, resend behavior, failed-attempt lockout, one-time use, and generic delivery failures without network calls.
- Assert no reset code or password is emitted in exception/debug traces.
- Retain the current owner isolation tests and add any future write path to both allowed-owner and foreign-owner cases.
