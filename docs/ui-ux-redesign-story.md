# R-08: Duno UI and UX redesign

**State:** Accepted with mascot follow-up in progress
**Priority:** High
**Scope:** Visual system and navigation improvements across guest, authentication, student, and admin pages. Preserve the approved Duno brand and existing product behavior.

## Goal

Make the product feel like one coherent study companion. Help students find a useful next action quickly, understand where they are, and recover from empty or filtered lists without dead ends.

## Design assumptions

- Keep the approved `duno.` name, mascot, tagline, and purple/mint/gold palette from [`brand-identity.md`](brand-identity.md).
- Use a consistent light interface across browsers and pages. The previous guest-only automatic dark appearance conflicted with the light authentication and workspace screens.
- Keep CodeIgniter views, local CSS, and small plain JavaScript. Do not add fonts, UI libraries, or build steps.
- Change presentation and navigation only. Keep routes, authorization, validation, data ownership, privacy, and product calculations intact.

## Acceptance criteria

1. Public, sign-in/register, student, and admin surfaces share a consistent type scale, color tokens, controls, and spacing.
2. Students see a small set of primary tools in desktop navigation, with secondary pages grouped under a clear menu; mobile navigation remains usable and indicates the current section.
3. The student dashboard gives direct, distinct entry points to Focus, Plan, Timer, and Aralytics, and retains the curated Guide content.
4. Empty Focus, empty subjects, and empty topic-search states explain why the list is empty and give a working next step.
5. Forms, tables, alerts, timer controls, plan blocks, and report sections remain readable at phone and desktop widths.
6. Keyboard focus stays visible, the main-content skip link is available, reduced-motion preferences are respected, and no interaction depends on color alone.
7. No new feature claims, account roles, external runtime dependencies, or changes to calculation/security behavior are introduced.
8. Relevant templates pass PHP syntax checks and the changed screens receive a manual browser review at mobile and desktop widths before the story is accepted.

## Current implementation

- A shared local stylesheet now supplies the light Duno UI tokens and common workspace, authentication, admin, and navigation styles.
- The public page no longer switches to a dark palette based on the browser preference, aligning it with the sign-in and signed-in areas.
- Student navigation now foregrounds Focus, Plan, Timer, and Aralytics on desktop and groups the remaining pages under More. The mobile tool bar remains available up to tablet widths.
- The student dashboard has a new welcome area and direct tool cards. Empty Focus, subjects, and topic-search states now point to usable next steps.
- The Timer now has a separate focus-session card with a stronger running state, plus optional 25/5 Pomodoro and 50/10 rhythm suggestions. Breaks are paused, so only active timer time counts; the existing review and explicit-save flow is unchanged.
- The dashboard now opens an optional workload/energy check-in dialog on a student's first dashboard visit each app-local day if they have not already checked in that day. It can be dismissed for that day, is keyed separately per signed-in account in browser storage, and posts through the existing owner-scoped check-in endpoint only after explicit submission.
- Topic detail facts now sit in a responsive summary panel, and the add/edit form uses the same contained surface as the topic filters. Keyboard focus has a 3px outline across links, controls, fields, and the skip link; a field rule that suppressed the keyboard outline was fixed. Existing reduced-motion handling was confirmed in the browser.
- PHP syntax checks and `git diff --check` passed. The Playwright suite passed 3 tests with 400px/1280px topic detail and form checks, focus-ring and reduced-motion checks, and the existing responsive, admin-denial, timer, check-in, and print checks. The screens were reviewed on the local synthetic student/admin accounts. Review screenshots are in ignored `.tools/ui-review-20261025/`; the local router suppresses the development toolbar. No hosted application or configuration was changed.

## Owner-requested mascot follow-up (2026-10-25)

- The existing Duno mascot appears on the check-in prompt and Timer rhythm card, alongside the public page, authentication pages, and dashboard.
- The existing artwork has a gentle float animation; the Timer mascot animates a little more while a session is actively running.
- The mascot remains decorative and has empty alt text. `prefers-reduced-motion` disables motion through the shared accessibility rule.
- No new artwork states or assets were created.
