# Duno UI consistency and mobile-density audit

**Review date:** 2026-11-01
**Review scope:** Public landing, About/legal pages, authentication shell, student workspace routes, admin dashboard, shared navigation and footer, error page, lists, forms, Aralytics, and responsive layout rules.
**Status:** Source-level page-family and alignment audit; rendered browser review at phone, tablet, and desktop widths is still pending.

## Audit findings

- The signed-in desktop navigation had five primary links, while the mobile bar repeated those destinations and added Guide as a sixth tab. Six narrow destinations made labels harder to read and duplicated a dashboard section link.
- On narrow screens, the dashboard kept 14-rem tool tiles, a 10-rem hero illustration, and a three-part progression panel stacked in one column. These fixed minimums used vertical space even when the content was shorter.
- Aralytics displayed the subject, topic, dated-session, check-in, and bonus-insight sections immediately below the summary and chart. Students had to pass every detail section to reach the next feature or the bottom navigation.
- Several simple subject/topic/session lists used individually bordered, shadowed cards. Repeated card boundaries made scanning slower and added visual weight to short rows.
- Common controls mostly met the 44-pixel touch target, but compact page-specific actions and form spacing did not follow one shared density rule. Responsive breakpoints had accumulated repeated overrides over time.
- The shared footer used different effective widths from the content shell: body-padded student/admin pages lost an extra gutter, while About/legal and landing content had different max-widths. This made the footer divider and navigation columns appear shifted relative to each page's content.
- The main route families share the footer partial; authentication and branded error pages use standalone shells without a footer. The role-landing view has no route/controller reference and is not part of the rendered page set.

## Local changes

- Reduced student bottom navigation to five labeled destinations: Home, Focus, Plan, Timer, and Aralytics. Moved Guide into the existing More menu; it remains on the dashboard too. Guest and admin navigation remain unchanged.
- Reduced unused mobile bottom padding and compacted the home hero, progression layout, and tool tiles. Tool cards retain their descriptions and destinations.
- Converted short subject/topic rows to aligned separators with less padding and removed repeated shadows. Unified common section, field, and action spacing through shared CSS tokens.
- Set coarse-pointer controls and menu links to at least 44 CSS pixels high, aligned to the existing control size. Preserved visible keyboard focus, safe-area padding, and reduced-motion behavior.
- Kept the Aralytics period summary and main chart visible. Placed subject, topic, and dated-session detail behind a native disclosure. The private check-in pattern and optional progression insights are also collapsible. Print styles reveal the required subject/topic/session breakdown even when the disclosure is closed; private check-ins and bonus insights remain excluded from print.
- Kept the implementation in CodeIgniter views, existing CSS, and native HTML disclosure controls. No new library or database changes.
- Standardized footer alignment inside the shared partial: centered brand, group headings, link lists, legal links, copyright, and icon credit. Matched the footer width to each page shell (74rem public landing, 72rem About, 64rem legal, 76rem student, and 84rem admin) so outer edges line up with page content on both wide and narrow layouts.
- Kept footer groups in even responsive columns, centered each phone disclosure label with its toggle, and made footer links meet the 44-pixel touch target.

## Design references considered

The review used platform guidance as principles rather than copying a platform skin:

- Apple recommends using tab bars for top-level navigation, keeping labels, and avoiding an unnecessary number of tabs: [Human Interface Guidelines: Tab bars](https://developer.apple.com/design/human-interface-guidelines/tab-bars).
- Material recommends compact app bars focused on the current page and essential actions, and consistent, scannable list rows: [Material 3 app bars](https://m3.material.io/components/app-bars/) and [Material 3 lists](https://m3.material.io/components/lists/overview).
- The 44-pixel touch target used for frequently used controls is a conservative web target; Apple cites 44 pt for touch controls, while WCAG 2.2 AA requires at least 24 by 24 CSS pixels or sufficient spacing: [Apple UI design tips](https://developer.apple.com/design/tips/) and [WCAG 2.2 target size](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum).
- Sampled core palette contrast ratios: muted text on canvas 5.84:1, deep purple on white 6.75:1, and primary purple button on white 4.57:1. The last is just above WCAG AA's 4.5:1 text threshold; do not lighten the primary purple without rechecking contrast.

## Verification and limits

- The full PHPUnit suite passed: 109 tests and 1,121 assertions. PHP syntax checks passed for the changed navigation, layout, and Aralytics views. `git diff --check` passed; Git printed line-ending conversion notices only.
- Browser was not reloaded during this source audit to preserve the owner's active local review session. The page families and breakpoints were checked in views/CSS, but rendered review at phone, tablet, and desktop widths plus keyboard-only checks remain open. Layout changes are not visually accepted based on source alone.
- No changes have been staged, committed, pushed, or deployed.
