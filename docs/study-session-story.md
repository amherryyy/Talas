# Story: study timer and saved sessions

**Requirement:** F-08

**Status:** Accepted by the owner on 2026-10-16 after automated checks and browser review; submitted as [PR #3](https://github.com/amherryyy/Talas/pull/3).

## User story

As a student, I can time a study session for one of my subjects and optionally a topic, review the result, and save it to my own history.

## Approved rules

- The saved duration is a whole number from 1 to 480 minutes inclusive.
- A timer result is not saved automatically. The student reviews the stopped timer and explicitly saves it.
- Refreshing, closing the page, or otherwise interrupting an unsaved timer never creates a study session.
- The student can edit or delete a saved session.
- The subject and optional topic must belong to the signed-in student; a selected topic must belong to the selected subject.

## Implementation decisions

- The timer state remains in the current page only. A refresh discards the unsaved timer; the browser may warn before leaving while a timer or unsaved result exists.
- Stop presents the elapsed whole minutes for review. The student can correct the duration within the approved range before saving.
- Editing a saved session changes its duration, subject, or optional topic. Its completion timestamp is set by the server when first saved and remains unchanged during edits.
- Deleting a saved session requires a separate confirmation step.
- Subject deletion is blocked while any saved session references the subject, preserving session history. The UI must explain the restriction.

## Acceptance checks

- The timer can start, pause, resume, stop, and discard without saving a record.
- Saving requires a valid subject, optional related topic, duration from 1–480, and an explicit POST with CSRF.
- Browser/client timing is not treated as an authorization or validation boundary; submitted duration is revalidated server-side.
- Refresh/interruption before explicit save creates no session.
- A student can list, edit, and delete their own saved sessions; attempts to access another student's sessions or references are denied.
- Invalid duration, foreign subject, foreign topic, and topic/subject mismatch are rejected without writes.
- Session deletion is confirmed and updates the session history accordingly.
- Deleting a subject with a saved-session reference is blocked; deleting it after the session is deleted remains subject to the existing topic restriction.
- Keyboard and narrow-screen review covers timer controls, review/save, edit, and deletion confirmation.
