# Browser language preference

## Decision

The owner approved an optional interface language selector for English (default), Taglish, and Filipino (Tagalog), with the selected language remembered in the current browser only. No account setting or database migration is involved.

## Behavior

- A CSRF-protected `POST /language` accepts only `en`, `taglish`, and `fil`.
- The preference is stored in a one-year `duno_language` cookie, with `HttpOnly`, `SameSite=Lax`, and `Secure` on HTTPS requests.
- A global filter applies the validated cookie before rendering. An absent or invalid value falls back to English.
- The selector is available in public, authentication, student, and admin navigation. It returns to the current local page after changing languages.
- Navigation, authentication labels, the student tour, and the subject list/detail actions have English, Taglish, and Filipino copy. Official program/course names and user-entered subject/topic content stay as entered.

## Coverage and follow-up

This is an initial interface localization, not a translation of all long-form help, validation errors, historical notices, curriculum metadata, or user data. Remaining English copy in those areas should move into the language catalogs as those screens are revised. Review Filipino and Taglish wording with local users before treating it as final editorial copy.

## Verification

Implementation checks are recorded in the task handoff. Browser and hosted-cookie behavior require review after upload.

## InfinityFree query-style route correction (2026-10-29)

InfinityFree may provide a rewritten route as `QUERY_STRING=index.php?/student?...` without a leading slash. The return-path parser now collapses repeated front-controller prefixes in either leading-slash form before `site_url()` builds the redirect. This prevents a second `index.php?/` from being embedded in the destination after changing language. Route generation remains framework-based, and the posted return path is still constrained to a local relative path by the controller. No schema or cookie change is involved.

Review language switching from the home page, a student detail page, and a page with a query string after uploading the next package. Confirm each destination has at most one `index.php?/` prefix and remains on the same route.
