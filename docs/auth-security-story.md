# Authentication throttling, recovery, and bot controls

**Status:** Implemented locally on 2026-11-02; database migration, email delivery, host throttling, and end-to-end browser behavior remain unverified.

## Owner-approved scope

Implement the owner-authorized recommendations for endpoint-specific throttling, an account password reset flow, and the built-in CodeIgniter honeypot. Keep Shield authentication and password hashing. Do not enable magic-link sign-in, change the current password policy, add CAPTCHA, or create permanent account lockouts.

## Behavior

- Shield's existing limit remains at 10 auth-page requests per minute per IP.
- POST login is additionally limited to 8 requests per IP per 5 minutes and 10 per email identifier per 15 minutes.
- POST registration is limited to 5 requests per IP per hour and 3 per email identifier per hour. Email verification keeps its current maximum of five incorrect codes, one-minute resend cooldown, five sends per day, and Shield per-IP auth cap.
- Password reset code requests are limited to 3 per IP and 3 per email identifier per hour. Code submissions are capped at 10 per IP/email per hour and five failed attempts per issued code.
- Throttles expire and do not permanently lock accounts. They use CodeIgniter's configured file-cache throttler; InfinityFree persistence must be confirmed after upload.
- Forgot-password responses are the same whether or not an active account exists. Only active accounts receive an email. The email contains a cryptographically random six-digit reset code. Its hash uses PHP's password hashing API; the code expires after 15 minutes, is invalidated when replaced, and is consumed when the password is changed. Five incorrect entries invalidate it. The database enforces at most one active reset code per account.
- Password changes use Shield's password validation and hashing APIs. On success, the user returns to sign-in.
- The framework honeypot runs before and after requests. Its trap input is inserted into rendered forms and any submitted value rejects the request. Existing CSRF protection remains enabled.

## Schema and release

Migration `2026-11-02-000001_CreatePasswordResetCodes` creates `password_reset_codes` with one row per user and a cascading user foreign key. Back up the hosted database, verify the migration ledger, apply the migration once with [`2026-11-02-password-reset-codes-upgrade.sql`](2026-11-02-password-reset-codes-upgrade.sql), and confirm its row and table before uploading the recovery code. Keep the mail configuration in the private host `.env`. Do not expose SMTP values in Git or logs.

## Verification still required

- Local PHPUnit coverage now passes for missing registration consent, neutral handling of unknown-email requests, a valid reset code, one-time code consumption, five-attempt invalidation, student registration/activation, sign-in/out, and invalid credentials. The focused suite passed 8 tests / 64 assertions on 2026-11-02; the full local suite passed 118 tests / 1,275 assertions the same day. These are local SQLite feature tests, not hosted acceptance.
- Add feature cases for expired and replaced reset codes, inactive accounts, throttling thresholds/buckets, and honeypot rejection.
- Local browser review of the login, forgot-password, and reset-password screens, including CSRF and keyboard form behavior.
- Configure and verify email delivery using a synthetic account.
- On InfinityFree, verify cache-backed request limits across repeated requests and after the normal application lifecycle; confirm the cache directory remains private and writable.
- Apply the migration only after a database backup, then verify reset behavior on the hosted synthetic account.

## Not included

This story does not change the current 8-character minimum or password validators, add multi-factor authentication, guarantee invalidation of already-active sessions on other devices, add CAPTCHA, or change registration's email verification behavior.
