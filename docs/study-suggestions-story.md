# F-10 curated study suggestions

**Requirement:** F-10 (Should)
**Status:** Owner-accepted on `feature/F-10-study-suggestions` on 2026-10-17; merged to `main` through [PR #5](https://github.com/amherryyy/Talas/pull/5) on 2026-10-17 (merge commit `ce44d12`).

## Approved product decision

On 2026-10-16, the owner approved concise, general study tips curated from reputable education sources, with source links, shown on the student dashboard. On 2026-10-31, the owner requested a more interactive Guide that gives students practical steps for a subject or topic. The Guide lets a student explicitly choose a subject/topic and a session goal, then assembles a deterministic sequence from curated methods. The sequence is user-directed; Duno does not infer a student's needs, and it uses no AI. It must not promise outcomes, diagnose, or infer personal traits.

## Proposal alignment update (2026-10-30)

The owner approved connecting StudyGuide content to relevant course/program learning outcomes. The first implementation uses verified CHED CMO No. 25, s. 2015 program outcomes for supported NVSU BSCS, BSIS, and BSIT subject references. It does not claim course-specific outcomes because those syllabi/outcome documents are not in the verified source set. Other programs remain on general study suggestions until an authoritative outcome source is verified.

The suggested connection between a study practice and an outcome is Duno's editorial mapping. CHED defines the program outcomes; it does not prescribe study methods. The interface labels this distinction and keeps source links to educational research and CHED CMO.

## Content source and wording

The Guide uses familiar student terms such as “blurting” and “Feynman technique” as plain-language labels for established activities, not as separately validated branded methods. It includes active recall, spaced retrieval, self-explanation, worked examples, interleaved practice, relevant diagrams, and a confidence check. Each method links to the source that supports the associated practice. A recent school-classroom meta-analysis found average benefits for retrieval, distributed, and interleaved practice, while also identifying moderators and boundary conditions; because it covered primary and secondary school classrooms, it is relevant context rather than direct evidence for every college task. The interface presents context-sensitive choices, not a one-size-fits-all prescription. Interleaving is offered for related problem types once the basics are familiar; a simple diagram is recommended only when it clarifies a process or relationships, and should be explained and checked rather than copied decoratively.

Key sources:

- What Works Clearinghouse, [Organizing Instruction and Study to Improve Student Learning](https://ies.ed.gov/ncee/wwc/practiceguide/1) (2007).
- Ruitenburg et al., [2026 classroom meta-analysis of retrieval, distributed, and interleaved practice](https://link.springer.com/article/10.1007/s10648-026-10225-5) (53 studies, 7,303 learners; effects were moderated by learning/test characteristics and feedback).
- Brunmair and Richter, [2019 meta-analysis of interleaved learning](https://pubmed.ncbi.nlm.nih.gov/31556629/) (effects varied by material; interleaving had ambiguous results for expository text and words).
- Banks et al., [2026 meta-analysis of dual coding in classrooms](https://www.ambition.org.uk/when-and-how-to-use-dual-coding-effectively-in-teaching/) (visuals should connect clearly to explanation and avoid irrelevant detail).
- Carpenter et al., [The science of effective learning with spacing and retrieval practice](https://www.nature.com/articles/s44159-022-00089-1) (2022 review).

Current card text:

| Card | Guidance | Evidence framing |
| --- | --- | --- |
| Space the second look | Schedule a later retrieval attempt through Duno Plan and correct gaps. | Spaced retrieval; classroom meta-analysis linked in card. |
| Try a two-minute brain dump | Recall freely, verify, and turn misses into questions. “Blurting” is retrieval practice. | Retrieval; classroom meta-analysis linked in card. |
| Teach it back in plain language | Explain a concept aloud, include the why, then verify against course material. | Self-explanation; WWC practice guide linked in card. |
| Fade the worked example | Study a solution, cover steps, then attempt a similar problem. | Worked examples; WWC practice guide linked in card. |
| Mix up related problem types | Once basics are known, mix related problems and identify the clue for each method. | Interleaving; source notes subject/task boundary conditions. |
| Sketch the idea from memory | Draw and explain relevant relationships; correct the model from course material. | Dual coding; 2026 classroom meta-analysis linked in card. |
| Check your confidence against recall | Compare confidence with an actual closed-notes answer and plan around the gap. | Metacognitive monitoring; retrieval and confidence study linked in card. |

## Acceptance criteria

- The signed-in student dashboard shows the same seven curated method cards to every student.
- A student may choose one of their subjects and an optional topic, then choose whether they want to build understanding, remember material, or solve problems. The dashboard shows a practical, deterministic sequence assembled from the same curated methods.
- A student may select one of their NVSU BSCS/BSIS/BSIT-linked subjects to reveal relevant verified CHED program outcomes alongside the chosen methods. Subject and topic choices are scoped to that student's records.
- Subjects without verified program outcome data do not receive an invented mapping; the general suggestions remain available.
- Each card has short, practical copy and links to the relevant source. Method names do not imply that a popular label is independently validated.
- Method rationale, evidence notes, CHED associations, and research links are tucked under a collapsed “Why this can help” disclosure in the student playbook. Public guide cards initially show only the title and brief description; steps and sources are behind “How to try it.”
- Guidance notes relevant limits: interleave related problem types after initial learning; use diagrams for relevant relationships rather than decorative copying; and verify answers after retrieval.
- The cards remain readable and usable with keyboard navigation and at mobile, tablet, and desktop widths.
- The admin workspace does not show student study suggestions.
- Suggestions, activity steps, and mappings are static content. The sequence changes only in response to the student's explicit subject/topic and goal selection. No AI generation, database storage, inferred traits, or guaranteed academic outcomes.
- Student/admin route authorization continues to be enforced by the existing route filters.

## Verification record

- 2026-11-02 evidence-led expansion: added seven method cards and three user-selected playbooks, with current classroom meta-analysis links, method-specific sources, confidence checking, and context limits. Updated the existing Guide expectations. No tests or browser checks were run, and no release archive was built or uploaded.

- 2026-11-01 Guide interaction update: PHP syntax passed for the dashboard controller, curated suggestion content, and dashboard view. JavaScript syntax passed for the Guide interaction module; `git diff --check` passed. Automated tests and browser review were not run for this update. Live r5 still has the previous Guide until a future package is uploaded.

- 2026-11-02 content refinement: rewrote the Guide's goal choices and four method cards as concrete actions, added a topic-framed “Try this” prompt, and connected delayed review to Duno Plan. That interim content update was not packaged or uploaded; the evidence-led expansion below supersedes its four-method set.

- PHPUnit: passed, 49 tests and 431 assertions with coverage disabled; the default run also passed all tests but reported the expected missing coverage-driver warning.
- PHP syntax checks: passed for changed PHP files. JavaScript syntax and `git diff --check` passed.
- Composer audit: reached Packagist and reported no security advisories after clearing the injected proxy variables for the Composer process. Composer proceeded without its unwritable cache.
- Playwright: both tests passed with the synthetic review account at 320, 400, 768, and 1280 px widths. Keyboard navigation reaches a suggestion source link, and no horizontal overflow was found.
- Visual review: inspected the dashboard at 320 and 1280 px; all four cards and source links remained readable, with a one-column mobile layout and balanced two-column wide layout.
- No production deployment is included in this story. No PHP style tool is configured in the repository.
