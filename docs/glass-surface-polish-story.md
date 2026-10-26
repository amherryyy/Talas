# Story: glass surfaces and compact Duno shell

**Task:** R-11 visual refinement
**State:** Implemented locally; browser review deferred until the accumulated update batch is uploaded to InfinityFree, per owner direction.
**Owner request:** Apply glassmorphism to cards, compact the menu and header with appropriate opacity, and establish a minimalist footer if useful (2026-10-26).

## Design decisions

- Keep the approved Duno canvas, purple, lavender, mint, gold, and ink palette.
- Use softly translucent white and tinted surfaces, a subtle inner highlight, restrained shadows, and backdrop blur on major cards, menus, and headers.
- Keep surfaces opaque enough for text and controls to remain readable. Browsers without backdrop-filter still receive translucent backgrounds and borders.
- Reduce header height, navigation gaps, menu width, and menu item padding while retaining visible keyboard focus and existing active-page cues.
- Use a short signed-in footer with the Duno wordmark, tagline, and required Solar Icons attribution. Keep the public footer's current concise content.
- Preserve responsive navigation, existing routes, content, and reduced-motion behavior. No new dependency or image asset is required.

## Verification

- PHP syntax passed for the changed student and admin layout views.
- `git diff --check` passed; Git displayed only existing line-ending notices on unrelated tracked files.
- Per the owner, do not use local browser visuals as the acceptance check for this batch. Review desktop and phone widths after the accumulated files are uploaded to InfinityFree.
- Uploading and deployment are outside this task; the owner will decide when to release the batch.
