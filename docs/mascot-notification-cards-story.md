# Mascot notification cards

**Status:** Implemented with server-rendered flash cards in the current workspace; hosted appearance not yet reviewed.

## Owner request

Convert app notifications from plain success/error banners into pop-up cards that include the Duno mascot with an expression that matches the message, including errors.

## Behavior

- Reuse a shared notification stack across student, authentication, admin, public start, and role pages.
- Present flash success, info, warning, and error messages as dismissible mascot cards. Flash cards render on the server so their popup appearance does not depend on JavaScript loading. Success and info messages dismiss after a short delay when JavaScript is available; warnings and errors remain until dismissed.
- Move inline form validation lists and Shield authentication alerts into the same cards when JavaScript is available. Without JavaScript, their existing inline content remains visible.
- Use a positive, curious, surprised, or concerned emotion marker, entrance/exit motion, and a reduced-motion alternative. Errors use assertive announcement semantics; other notices use polite status semantics.
- Keep instructional timer status and non-interactive catalog notices in their original locations.

## Verification status

The owner reported that hosted success messages still appeared as inline banners. Flash cards are now server-rendered, and the shared script enhances dismissal and timing instead of being required to create the card. Feature tests verify the server-rendered card; the full PHPUnit suite passes with 88 tests and 902 assertions. Both JavaScript syntax checks and PHP lint pass. An authenticated browser review is still needed after the owner uploads the updated package. No database migration or new dependency is required for notifications.
