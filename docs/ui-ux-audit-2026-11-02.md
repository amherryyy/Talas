# Duno UI/UX audit — 2026-11-02

## Coverage

Inventoried all 62 files under `app/Views` and reviewed the shared `public/assets/css/duno-ui.css` rules plus templates where they use those shared components. The inventory covered the public landing and role pages; sign-in, registration, activation, and password recovery; student dashboard, subjects/curricula, topics and Focus, Plan, Timer/sessions, check-ins, Aralytics, academic-program setup, and profile; admin analytics and charts; About and legal pages; shared navigation, footer, notifications, PWA notices, tour, and error views.

This was a source and consistency audit, with a rendered desktop check of the local registration page. Authenticated student/admin screens and updated phone layouts were not all opened in a live browser during this pass, so the note does not claim full visual acceptance at every viewport.

## Findings and changes

| Area | Finding | Change |
| --- | --- | --- |
| Student workspace forms | Shared control styling covered text/search/date/number but omitted email, password, time, and several other standard input types. Those controls could fall back to browser-default sizing and borders; intrinsic minimum widths could also force grid children wider at tablet and phone widths. | Added the missing input types and `min-width: 0`/`max-width: 100%` guards to shared Duno controls so profile, session, and other workspace forms use the same dimensions, typography, border, focus treatment, and responsive bounds. |
| Saved topic, session, and check-in lists | A later compact-spacing override removed the card border and fill from `.subject-list`, making the content read like ungrouped link rows. Later glass-surface selectors could also override list cards. | Restored compact, low-contrast card rows at the shared component level and removed those list rows from the unrelated glass-surface override groups. |
| Account recovery | Forgot-password, reset-password, and email-activation templates still used older floating-label markup while registration and login used the current Duno field pattern. | Converted these forms to the same label-first `.form-field` structure and removed redundant placeholders. |

## Consistency checks

- Shared footer markup is a single partial across landing, student, admin, About, and legal pages. The current layout centers its brand, navigation groups, legal links, and copyright consistently; mobile uses two columns and collapses to one at narrow widths.
- Shared workspace controls, buttons, focus indicators, touch targets, card/list surfaces, responsive breakpoints, reduced-motion handling, and print-specific admin rules were checked for conflicting overrides.
- Responsive rules were reviewed across the existing 52rem, 40rem, 32rem, and 22rem breakpoints, with fluid min/max control widths so form fields can shrink inside tablet and phone grids instead of pushing the page wider.
- Subject detail topics have a distinct numbered row treatment; the restored `.subject-list` style is intentionally compact so the list remains scannable without making every item a large panel.
- No database behavior, authorization, or navigation routes changed. The release marker was advanced to r11 for the evaluation package.

## Remaining review

- Open student and admin pages with representative data at phone, tablet, and desktop widths, including empty states, long translated labels, and the print layout.
- Check actual mail-recovery and registration-activation states in the browser after the host mail path is available.
- Recheck keyboard focus and zoom/reflow on the long Plan, Aralytics, and admin analytics pages.

The responsive Playwright check could not start because this Windows environment returned `spawn EPERM`. The updated auth templates were visually checked only on the currently available registration route at desktop width; the recovery views have source-level consistency changes but still need a rendered browser review. No migration is needed.
