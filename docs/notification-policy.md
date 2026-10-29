# Duno notification policy

Notifications use one message that says what happened, with Duno's tone adjusted to the outcome. The mascot card provides context; it must not add a second, unrelated message.

| Event | Mood | Copy and detail | Dismissal |
| --- | --- | --- | --- |
| Sign in or sign out succeeds | Success | Keep authentication feedback to one short sentence. Sign out: "You've logged out." Do not add a title or motivational dialogue. | Dismissible; short auto-dismiss is acceptable. |
| A study action saves or completes | Success | State the saved result. A brief encouraging line may appear when it adds useful context; omit it for routine redirects or repeated status messages. | Dismissible; auto-dismiss after enough reading time. |
| Helpful, non-blocking information | Info | Use a neutral title and one clear next step or fact. | Dismissible; may auto-dismiss. |
| Action needs attention before continuing | Warning | Explain what needs checking and what the user can do next. Do not label it a success. | Dismissible; remains visible until dismissed. |
| Action fails or input is rejected | Error | Use a calm, specific title, then show the actual validation or system message. Never praise the user for an error. | Dismissible; remains visible until dismissed. |
| Daily streak or earned reward | Success/reward | State XP and the updated streak or reward explicitly; preserve its reward styling and animation. | Dismissible; remains visible long enough to read. |

## Implementation rules

- Determine mood from an explicit notification type or semantic CSS class first. `role="alert"` controls assistive-technology urgency; by itself it does not mean the outcome is an error.
- Keep validation details intact. Avoid paraphrasing away field names or actionable server feedback.
- Keep login and logout feedback compact. Do not repeat the same meaning in the title, dialogue, and body.
- Errors and warnings use assertive live announcements and do not auto-dismiss. Success and information use polite live announcements.
- Every card has a real dismiss button, a visible focus style, and reduced-motion support. The original inline message remains available when JavaScript does not run.
- Keep notices within the viewport on phone, tablet, and desktop. Use localized copy for supported interface languages where a translation is available.

## Review status

The project now uses compact success feedback for logout and classifies Shield's success alert as success. Remaining notification scenarios should be reviewed in the browser after the owner uploads the package; server and client rendering must be checked for duplicate cards, focus behavior, and readable message length.