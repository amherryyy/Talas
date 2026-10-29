# Duno mascot asset integration guide

## Purpose

This guide documents the approved Duno mascot image set and how to use it in the current application. The assets are designed for the existing CodeIgniter views, local CSS, and small JavaScript modules. They add expression to existing welcome, onboarding, encouragement, and notification moments without adding a new runtime dependency or changing Duno's study behavior.

## Asset files

All application assets live under `public/assets/images/` and have transparent backgrounds. The WebP derivatives are 512 × 536 pixels and approximately 40–48 KB each.

| File | Intended use |
| --- | --- |
| `duno-mascot-welcome.webp` | Public and signed-in welcome areas, routine success, and the opening onboarding-tour card |
| `duno-mascot-curious.webp` | Helpful informational notifications |
| `duno-mascot-alert.webp` | Warnings and reminders that need the student's attention |
| `duno-mascot-support.webp` | Error feedback and the optional workload/energy check-in prompt |
| `duno-mascot-celebrating.webp` | Earned rewards, level-up moments, and the brief response when students activate Duno on login or registration pages |

`duno-mascot.png` is retained as the original transparent artwork and character reference. The WebP files are optimized derivatives for app delivery. The mascot is a cheerful yellow lightbulb wearing a Filipino salakot with purple study accessories.

## On-screen display sizes

The 512 × 536 pixel WebP dimensions are the source-image resolution; CSS scales them down for each screen. The sizes below are the current CSS image or box sizes. Pixel conversions are approximate at the browser's default 16 px per `rem`; a parent layout, narrow viewport, or user text scaling can make the rendered image smaller. The transparent padding also means the visible character is slightly smaller than its CSS box.

| Location | Wide layout | Phone layout |
| --- | --- | --- |
| Public welcome hero | Up to 15.5 rem wide (about 248 px); constrained by the hero column | 6.5 rem wide (about 104 px) |
| Student dashboard hero | Up to 13 rem wide (about 208 px) | 8 rem wide (about 128 px) |
| First-visit tour card | 3.6 rem wide (about 58 px) | 3.1 rem wide (about 50 px) |
| Login and registration | Up to 15 rem wide (about 240 px) | Up to 10.5 rem (about 168 px); at very narrow widths, the button constrains it to about 10 rem (160 px) |
| Standard notification toast | 10 rem wide (160 px), extending above and beside the card | 7.25 rem wide (116 px), perched above the card at widths up to 40 rem |
| Compact notification, such as logout | 8 rem wide (128 px), extending above the short card | 6.75 rem wide (108 px), perched above the short card |
| Daily check-in dialog | 4.5 rem square (72 px) | 4.5 rem square (72 px) |
| Level-up dialog | 7 rem square (112 px) | 7 rem square (112 px) |
| Timer rhythm card | 5.25 rem wide (about 84 px) | 4.6 rem wide (about 74 px) |

These are layout dimensions, not required design targets for a replacement asset. Keep the source aspect ratio and let each existing component's CSS control its size.

The owner requested larger notification mascots that may extend outside the bar. Notification artwork now has a transparent, overflowing position without the former small background tile. On phone widths the message uses the full card width below the mascot, and the dismiss control stays in the upper right. The stack reserves space above and between cards so the enlarged artwork is visible; overflowing artwork has no pointer interaction.

## Current integration points

- `app/Views/home.php` uses the welcome pose in the public start-page hero.
- `app/Views/partials/app_tour.php` shows the welcome pose beside the existing first-visit tour text.
- `app/Views/auth_layout.php` starts with the welcome pose. `public/assets/js/auth-mascot.js` swaps to the celebration pose while the existing bounce and encouragement response runs, then restores the welcome pose.
- `app/Views/partials/app_notifications.php` selects the server-rendered pose by notification mood. Earned rewards use the celebration pose.
- `public/assets/js/app-notifications.js` uses the same mood mapping when converting existing inline notices into toast cards.
- `app/Views/student/dashboard.php` uses the welcome pose in the student hero and the supportive pose in the optional check-in dialog.
- `app/Views/student/partials/level_up_dialog.php` uses the celebration pose.
- `app/Views/student/sessions/index.php` uses the welcome pose in the Timer rhythm card.
- `public/assets/css/duno-ui.css` supplies the existing float, bounce, entrance, and notification motion. Reduced-motion preferences remain respected.

## Notification mood mapping

Keep the server-rendered and JavaScript-created toast mappings aligned:

| Notification mood | Mascot pose |
| --- | --- |
| Success | `duno-mascot-welcome.webp` |
| Information | `duno-mascot-curious.webp` |
| Warning | `duno-mascot-alert.webp` |
| Error | `duno-mascot-support.webp` |
| Earned reward | `duno-mascot-celebrating.webp` |

The mood communicates tone; the notification text remains the source of truth for what happened and what the student should do. Do not let an expression replace an important status, error detail, or next step. Avoid adding a second, unrelated motivational sentence to a routine notification.

## Adding or changing an image reference

Use the project's `duno_asset()` helper for every app image URL. It applies the project's asset versioning behavior so a changed static asset can be fetched after an update. Keep artwork under `public/assets/images/` so it remains public and deployable through the existing FTP package workflow.

Example in a PHP view:

```php
<img
    src="<?= esc(duno_asset('assets/images/duno-mascot-curious.webp')) ?>"
    alt=""
    width="512"
    height="536"
    aria-hidden="true"
>
```

For JavaScript-created notification cards, read the mood-specific URLs from the notification stack's `data-mascot-*` attributes. Keep a welcome/default URL as a fallback. Do not hard-code a second asset path in JavaScript when the server-rendered partial already provides it.

The stack also provides `data-mascot-reward-src`. A success notice marked `data-notification-reward="true"` or `streak-reward-notice` uses that celebration artwork when the JavaScript renderer creates its card. The auth interaction preloads its celebration image at low priority so the short reaction can display promptly.

If an image is added only for decoration, keep it hidden from assistive technology with an empty `alt` and/or `aria-hidden="true"`. Keep an accessible button label and a live text message for the login/register interaction; the picture itself is not the message.

## Motion and performance rules

- Use coordinated CSS keyframes and inline decorative SVG for the dashboard welcome sequence. The movement can be expressive and multi-stage while staying in the existing frontend stack; do not add animated GIFs, a canvas, a frontend framework, or an animation package for these poses.
- Preserve the `prefers-reduced-motion` behavior. Decorative art remains visible in its final pose when animation is disabled.
- Keep notification auto-dismiss timing, warning/error persistence, live-region roles, and dismissal controls unchanged when changing artwork.
- Use the small transparent WebP variants in the app. Preserve the original PNG as source artwork; do not replace it during routine UI updates.
- Do not add new tracking, saved student data, AI-generated encouragement, or study advice as part of mascot presentation.

## Review checklist for future asset updates

1. Confirm each pose still matches Duno's salakot, bulb head, face, brown limbs, and purple accessories.
2. Confirm transparent edges, 512 × 536 dimensions, and a small file size appropriate for the target display size.
3. Confirm all five moods resolve to the intended files in both server-rendered and JavaScript-created notifications.
4. Review welcome, onboarding, authentication, check-in, level-up, and notification appearances at phone and desktop widths.
5. Check keyboard use, screen-reader text, notification dismissal, and reduced-motion behavior.
6. Keep the study feature behavior, privacy boundaries, and existing accessible notification semantics unchanged.

## Integration verification on 2026-10-29

- Confirmed all five WebP files are 512 by 536 pixels, retain alpha transparency, and are 40,964 to 47,818 bytes each.
- The existing public, auth, student-dashboard, and subject feature checks passed: 28 application tests. A separate ignored preview fixture passed with one test and five assertions after its explicit flash-rendering option was corrected. Updated the two existing assertions that still expected the source PNG.
- PHP syntax passed for the seven mascot views; both changed JavaScript modules and `git diff --check` passed.
- Reviewed actual rendered PHP view snapshots in the in-app browser, without requiring the local MariaDB service or a standalone Chromium launch. All five server-rendered and client-created card poses loaded. Phone (400 px), tablet (768 px), and desktop previews fit their viewports. The auth reaction worked by click and Enter, restored the welcome pose, and announced its encouragement; dismissing an error card left other notices intact.
- Reviewed the existing reduced-motion CSS; no animation dependency or notification timing change was introduced. Full authenticated browser navigation and hosted asset delivery remain to be checked after upload.
- Composer advisory audit was attempted but could not reach Packagist through the configured proxy. No dependency was added or changed.

The incremental upload archive is `build/duno-mascot-update-20261029-r4.zip` (16 entries, 247,942 bytes). It updates the seven views, shared CSS, two scripts, and five WebP images in the existing installation; it also sets the public package marker to `duno-mascot-20261029-r4`. Extract into the existing domain `htdocs` with replacement enabled. The archive contains no `.env`, database records, original PNG replacement, or writable data. No migration is required. Its SHA-256 is `1244da01d5fd63ed7357cc4b6c0422af5a6044eb35f36f0a84aec18070223fc5`. It has not been uploaded by the agent.

The subsequent size update is available in `build/duno-mascot-update-20261029-r5.zip` (16 entries, 248,071 bytes), which includes all r4 mascot files with the larger notification styling. The marker is `duno-mascot-20261029-r5`; its SHA-256 is `c5140e569bc622988e68311a1b1a2d770a0b2ce7e220256ba743f415e5ede06a`. Browser previews at 320, 400, 768, and 1280 pixels confirmed viewport fit; narrow-phone and desktop standard/compact cards kept artwork clear of the message and dismiss control. The original card semantics, copy, and JavaScript timing were preserved. This CSS-only adjustment needed no additional PHP tests or dependency audit; `git diff --check` passed. Hosted delivery remains pending upload.
