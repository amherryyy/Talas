# Story: first-visit student onboarding tour

**Requirement:** F-13
**State:** Implemented locally; browser review deferred until the accumulated update batch is uploaded to InfinityFree, per owner direction.
**Owner decisions:** Show a suggestive tour the first time a student enters the app; allow students who already know Duno to skip it; remember completion in the current browser only (2026-10-26).

## User story

As a new student, I can get a quick introduction to Duno's existing tools, skip it when I already know the app, and open it again later if I need a reminder.

## Product rules

- Automatically offer the tour on the first authenticated student app page in the current browser, scoped separately to the signed-in student. Do not show it to guests or admins.
- On automatic first-run only, show a three-slide Duno welcome carousel before the feature tour. Each slide has one illustration, a headline, one sentence, and a moving three-dot progress marker. Skipping or finishing the welcome flow counts as completing the tour.
- Explain the existing start page, Duno Focus, Duno Plan, Duno Timer, Aralytics, Duno Guide, and the More menu's Subjects, Topics, and private check-ins.
- Let students move forward/back, skip, or finish. Skipping and finishing both suppress automatic starts in that browser. Keep “Show app tour” available from the More menu so the tour can be replayed.
- Replaying from “Show app tour” starts at the feature walkthrough, so returning students can get straight to the part they need. During the initial flow, Back from the first feature step returns to the final welcome slide.
- Use local browser storage only; no profile field, migration, analytics, external service, or cross-device synchronization is added.
- If the daily optional check-in dialog is due, wait until it closes before opening the tour. Treat the tour as a modal dialog, keep keyboard focus inside it until it closes, label it for screen readers, and respect reduced-motion preferences.
- Use existing navigation targets and feature descriptions; do not change study calculations, saved data, privacy, or account permissions.

## Acceptance criteria

1. The tour appears automatically once for an authenticated student in a browser, including when the first route is a secondary student page.
2. The sequence describes the start space, Focus, Plan, Timer, Aralytics, Guide, and More; it dims the surrounding page and clearly outlines the matching card or navigation item.
3. Back, Next, Skip, Finish, keyboard focus, and Escape work; the background cannot receive interaction while the dialog is open, and closing returns focus to the highlighted target.
4. Skip and Finish persist per student in browser storage. “Show app tour” starts it again even after completion.
5. The tour waits for a due daily check-in prompt to close, so the two dialogs do not compete.
6. The tour has no effect for guest or admin users and adds no runtime dependency or schema change.
7. Reduced-motion preferences suppress smooth scrolling and entrance animation. The instruction card moves to avoid covering the active target when the viewport allows; the tour remains usable at phone, tablet, and desktop widths.
8. First-run slides describe Duno's study space, planning tools, and saved-session insights in the selected English, Taglish, or Filipino copy. The carousel fits mobile viewports and supports keyboard navigation without introducing external fonts, libraries, or sample data.
9. Students can select a welcome slide using clearly labeled, keyboard-accessible page-dot buttons or swipe horizontally across the illustration. Back/Next remain available, vertical scrolling is not captured by the swipe gesture, and reduced-motion settings are respected.

## Verification

- PHP syntax passed for the changed controllers/views/partials. JavaScript syntax passed for the tour and check-in prompt modules. `git diff --check` passed.
- The full PHPUnit suite passed: 77 tests and 717 assertions. Feature coverage confirms the account-scoped tour appears on the student dashboard/start page and stays absent for guests and admins.
- The 2026-10-29 welcome carousel changes were reviewed in source only; tests and browser visual review were not run for that update.
- Per the owner, visual browser review is deferred until the accumulated updates are uploaded to InfinityFree.
- No upload, deployment, database migration, staging, or commit was performed for this task.

## 2026-10-27 spotlight and accessibility refinement

The tour now uses a four-panel dimming layer that leaves a clear cutout around the active target, a contrasting outline/shadow, and viewport-aware placement for the tutorial card. It is exposed as a modal dialog, makes the page regions inert, traps Tab/Shift+Tab in its controls, and returns focus when skipped or finished. Mobile/tablet card placement and browser/screen-reader behavior have not yet been reviewed.

## 2026-10-29 welcome mascot

Added the transparent welcome pose beside the opening tour text. It is decorative and uses the existing reduced-motion behavior. Browser review of the updated tour card remains pending.

## 2026-10-29 first-run welcome carousel

Adapted the owner-provided BrewedOps carousel into a full-screen, three-slide first-run intro using Duno's mascot, colors, and study concepts. The slides cover the study space, planning a day, and reviewing saved progress; each has localized English, Taglish, and Filipino copy, a one-sentence description, and a sliding progress dot. Next opens the existing seven-step spotlight tour; Back from its first step returns to the last welcome slide. Manual tour replay continues to open the feature walkthrough directly. Keyboard arrows move between welcome slides, the modal keeps focus contained, and reduced-motion settings stop the decorative animation and transitions. Browser visual review remains pending.

## 2026-10-29 carousel touch controls

Made the three progress dots labeled buttons with a 44-pixel touch target and current-slide state. Added horizontal touch/pen swiping on the illustration panel; vertical gestures remain available to the browser, and clicking a dot keeps focus on that button while updating the live progress text. Back/Next, keyboard arrows, and reduced-motion behavior remain in place. JavaScript syntax review passed; browser and assistive-technology review remain pending.
