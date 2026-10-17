# F-10 curated study suggestions

**Requirement:** F-10 (Should)
**Status:** Owner-accepted on `feature/F-10-study-suggestions` on 2026-10-17; PR publication in progress.

## Approved product decision

On 2026-10-16, the owner approved a small set of concise, general study tips curated from reputable education sources, with source links, shown as fixed cards on the student dashboard. They are not personalized, use no AI, and must not promise outcomes, diagnose, or infer personal traits.

## Content source and wording

The four suggestions are paraphrased from the What Works Clearinghouse practice guide [Organizing Instruction and Study to Improve Student Learning](https://ies.ed.gov/ncee/wwc/practiceguide/1) (2007). The guide is written for content-heavy subjects including social studies, science, and mathematics, and includes recommendations for spaced review, interleaving worked examples and problem solving, quizzing to revisit key content, and deep explanatory questions. Its page labels evidence levels separately for recommendations; the Talas cards do not make a broader guarantee.

Current card text:

| Card | Guidance | Guide recommendation |
| --- | --- | --- |
| Revisit a topic later | Pick one topic to review in a later session instead of doing every review in one sitting. | 1: Space learning over time. |
| Try recalling before rereading | Close notes, write or say what you remember, then check materials for gaps. | 5b: Use quizzes to revisit key content. |
| Explain why or how | Ask why a key idea works or how ideas connect, then write a short explanation. | 7: Ask deep explanatory questions. |
| Read an example, then try one | Review a worked solution, try a similar problem, and check the steps afterward. | 2: Alternate worked examples and problem solving. |

## Acceptance criteria

- The signed-in student dashboard shows the same four curated cards to every student.
- Each card has short, practical copy and links to its source guide.
- The cards remain readable and usable with keyboard navigation and at mobile, tablet, and desktop widths.
- The admin workspace does not show student study suggestions.
- Suggestions are static content: no profile inference, personalization, AI generation, database storage, or guaranteed academic outcomes.
- Student/admin route authorization continues to be enforced by the existing route filters.

## Verification record

- PHPUnit: passed, 49 tests and 431 assertions with coverage disabled; the default run also passed all tests but reported the expected missing coverage-driver warning.
- PHP syntax checks: passed for changed PHP files. JavaScript syntax and `git diff --check` passed.
- Composer audit: reached Packagist and reported no security advisories after clearing the injected proxy variables for the Composer process. Composer proceeded without its unwritable cache.
- Playwright: both tests passed with the synthetic review account at 320, 400, 768, and 1280 px widths. Keyboard navigation reaches a suggestion source link, and no horizontal overflow was found.
- Visual review: inspected the dashboard at 320 and 1280 px; all four cards and source links remained readable, with a one-column mobile layout and balanced two-column wide layout.
- No production deployment is included in this story. No PHP style tool is configured in the repository.
