# Mascot notification cards

**Status:** Implemented in the current workspace; hosted appearance not yet reviewed.

## Owner request

Convert app notifications from plain success/error banners into pop-up cards that include the Duno mascot with an expression that matches the message, including errors.

## Behavior

- Reuse a shared notification stack across student, authentication, admin, public start, and role pages.
- Present flash success, info, warning, and error messages as dismissible mascot cards. Success and info messages dismiss after a short delay; warnings and errors remain until dismissed.
- Move inline form validation lists and Shield authentication alerts into the same cards when JavaScript is available. Without JavaScript, their existing inline content remains visible.
- Use a positive, curious, surprised, or concerned emotion marker, entrance/exit motion, and a reduced-motion alternative. Errors use assertive announcement semantics; other notices use polite status semantics.
- Keep instructional timer status and non-interactive catalog notices in their original locations.

## Verification status

PHP syntax passed for 172 app/test files and `node --check public/assets/js/app-notifications.js` passed. The full PHPUnit suite passed (86 tests, 874 assertions). An authenticated browser review is still pending; the local web runtime lacks Intl in its normal configuration, so the hosted UI remains the planned visual-review target. No database migration or new dependency is required.
