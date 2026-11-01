# F-10 curated study suggestions

**Requirement:** F-10 (Should)
**Status:** Owner-accepted on `feature/F-10-study-suggestions` on 2026-10-17; merged to `main` through [PR #5](https://github.com/amherryyy/Talas/pull/5) on 2026-10-17 (merge commit `ce44d12`).

## Approved product decision

On 2026-10-16, the owner approved a small set of concise, general study tips curated from reputable education sources, with source links, shown on the student dashboard. On 2026-10-31, the owner requested a more interactive Guide that gives students practical steps for a subject or topic. The Guide now lets a student explicitly choose a subject/topic and a session goal, then assembles a deterministic sequence from the same four curated methods. The sequence is user-directed; Duno does not infer a student's needs, and it uses no AI. It must not promise outcomes, diagnose, or infer personal traits.

## Proposal alignment update (2026-10-30)

The owner approved connecting StudyGuide content to relevant course/program learning outcomes. The first implementation uses verified CHED CMO No. 25, s. 2015 program outcomes for supported NVSU BSCS, BSIS, and BSIT subject references. It does not claim course-specific outcomes because those syllabi/outcome documents are not in the verified source set. Other programs remain on general study suggestions until an authoritative outcome source is verified.

The suggested connection between a study practice and an outcome is Duno's editorial mapping. CHED defines the program outcomes; it does not prescribe the WWC study methods. The interface labels this distinction and keeps source links to both the educational practice guide and CHED CMO.

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
- A student may choose one of their subjects and an optional topic, then choose whether they want to understand an idea, remember key points, or practice solving. The dashboard shows a practical, deterministic method sequence assembled from the same four curated methods.
- A student may select one of their NVSU BSCS/BSIS/BSIT-linked subjects to reveal relevant verified CHED program outcomes alongside the chosen methods. Subject and topic choices are scoped to that student's records.
- Subjects without verified program outcome data do not receive an invented mapping; the general suggestions remain available.
- Each card has short, practical copy and links to its source guide.
- The cards remain readable and usable with keyboard navigation and at mobile, tablet, and desktop widths.
- The admin workspace does not show student study suggestions.
- Suggestions, activity steps, and mappings are static content. The sequence changes only in response to the student's explicit subject/topic and goal selection. No AI generation, database storage, inferred traits, or guaranteed academic outcomes.
- Student/admin route authorization continues to be enforced by the existing route filters.

## Verification record

- 2026-11-01 Guide interaction update: PHP syntax passed for the dashboard controller, curated suggestion content, and dashboard view. JavaScript syntax passed for the Guide interaction module; `git diff --check` passed. Automated tests and browser review were not run for this update. Live r5 still has the previous Guide until a future package is uploaded.

- PHPUnit: passed, 49 tests and 431 assertions with coverage disabled; the default run also passed all tests but reported the expected missing coverage-driver warning.
- PHP syntax checks: passed for changed PHP files. JavaScript syntax and `git diff --check` passed.
- Composer audit: reached Packagist and reported no security advisories after clearing the injected proxy variables for the Composer process. Composer proceeded without its unwritable cache.
- Playwright: both tests passed with the synthetic review account at 320, 400, 768, and 1280 px widths. Keyboard navigation reaches a suggestion source link, and no horizontal overflow was found.
- Visual review: inspected the dashboard at 320 and 1280 px; all four cards and source links remained readable, with a one-column mobile layout and balanced two-column wide layout.
- No production deployment is included in this story. No PHP style tool is configured in the repository.
