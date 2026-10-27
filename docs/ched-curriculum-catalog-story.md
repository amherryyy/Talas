# R-13 CHED program curriculum templates

**Date / status:** 2026-10-27 / Doing — the NVSU undergraduate offerings directory for Bayombong and Bambang is transcribed from official campus pages. Three CHED sample curriculum previews and nine NVSU reference course lists can be imported. Current NVSU-approved semester-by-semester study plans remain unverified.

## User outcome

A student can browse NVSU undergraduate degree offerings by campus and start a personal subject list from a separately labeled CHED template or one of nine sourced NVSU reference course lists. An NVSU degree listing is not an NVSU subject plan. The six CAS references are effective S.Y. 2019–2020; none confirms a current approved plan.

## Owner decision

The owner initially selected **CHED baseline templates by program**, rather than a national directory of programs at each university or college. The owner has now narrowed the first school directory to NVSU. Its program directory is campus-specific; the CHED samples remain separate national references. Imported items are a starting point that the student can adapt; importing must never replace existing subjects.

## Source findings and current coverage

NVSU publishes separate undergraduate degree lists for [Bayombong](https://www.nvsu.edu.ph/academics/degree-programs-bay) and [Bambang](https://www.nvsu.edu.ph/academics/degree-programs-bam), including colleges, programs, and majors. The local catalog transcribes these offerings and links each campus to its source. These pages do not provide a complete subject-by-semester plan for every program. The Bayombong page repeats “Bachelor of Early Childhood Education” where the specialization list appears to describe Bachelor of Secondary Education; the local listing labels BSEd with a note and cross-checks this against [CHED Region II's recognized-program list](https://region2.ched.gov.ph/index.php/recognized-programs-of-state-universities-and-colleges-sucs-in-region-ii/). The university remains the authority on current admissions availability and official curricula.

CHED's [sample or suggested curricula annex](https://legacy.ched.gov.ph/sample-suggested-curricula-undergraduate-programs-different-disciplines-aligned-outcomes-based-education/) lists 35 undergraduate programs. It is a selected list of sample curricula, not an exhaustive national inventory. The linked documents describe themselves as sample or suggested curricula and allow HEIs room to adapt course arrangements to institutional/program outcomes. For example, the [BA Communication curriculum](https://legacy.ched.gov.ph/wp-content/uploads/2017/10/Sample-Curricula-Bachelor-of-Arts-in-Communication1.pdf) calls some core courses non-negotiable while allowing selection/modification of required courses and electives.

CHED also publishes program-specific Policies, Standards, and Guidelines (PSGs) through [CMO series pages](https://legacy.ched.gov.ph/issuances/). PSGs can be revised or supplemented by later issuances; a single historical sample list cannot establish what is currently required across all Philippine HEIs. For example, the [BSCS/BSIS/BSIT PSG (CMO 25, s. 2015)](https://legacy.ched.gov.ph/wp-content/uploads/2017/10/CMO-no.-25-s.-2015.pdf) sets minimum standards and gives HEIs room to shape curricula to their context.

Therefore, do not label all listed subjects “CHED mandated.” The catalog should represent only what the cited current program issuance supports, distinguish minimum/required components from suggested/model courses and electives, and mark unresolved or superseded sources. A program with no verified source is not available for import yet. The 35-program annex is a useful discovery index, not a completeness claim.

The three available templates are BA Communication (CMO No. 35, s. 2017), BS Accountancy (CMO No. 27, s. 2017), and BS Business Administration (CMO No. 17, s. 2017), based on CHED's published sample program-of-study PDFs. They are explicitly labeled as CHED samples and are not represented as NVSU curricula. The preview distinguishes core courses where the source does, generic required-course slots, electives, and courses listed in a sample plan. Other programs are not yet represented as course templates.

## Acceptance criteria

- A student can browse source-linked NVSU undergraduate program offerings grouped by Bayombong/Bambang campus and college. (Implemented from the university's published degree pages.)
- NVSU degree offerings are distinguished from reference course lists. Three lists come from an undated PDF; six CAS lists come from 2019–2020 curriculum PDFs. None is claimed as a current approved curriculum.
- A student can preview a source-checked CHED sample by program name. (Three templates currently available; these are distinct from the NVSU offerings directory.)
- Each template records the program title, source URL, CMO number/series or source publication date, source type, verification date, and whether it is current, superseded, or awaiting review.
- Each course entry distinguishes mandated/minimum, required, suggested/model, and elective status only where the source supports that distinction; otherwise it is labeled unclassified and is not represented as mandatory.
- The UI clearly says that institution curricula can differ and links to the underlying CHED source.
- Import adds selected template subjects to the authenticated student's own list, preserves existing subjects/topics/sessions, skips case-insensitive duplicate names, and leaves imported subjects editable. (Implemented; year/semester is shown in the preview but is not stored on Duno subjects.)
- Program, source, course, and import data are reviewed for completeness before a template is marked available. Do not fabricate missing requirements or imply that the catalog covers every CHED program until an official inventory and all applicable issuances have been reconciled.
- No AI-generated advice, campus-by-campus HEI directory, international-only curricula, or automated background updates are introduced.

## Data and implementation notes

Obtain and review current NVSU-approved curricula/prospectuses before describing reference lists as current NVSU curricula; also verify campus page changes against NVSU Admissions. Continue the CHED source register, identify amendments/replacements, and compare it with the 35-program sample annex and programs outside that annex. Keep source metadata with every version of a template. Import uses the existing subject owner boundary and framework escaping/CSRF; it accepts catalog course IDs only, adds to the signed-in student's own list, and skips existing names. No migration or runtime dependency was added. Catalog data is plain PHP so the feature remains deployable by FTP.

## Verification and release

Focused subject feature tests passed (10 tests, 118 assertions), covering the NVSU campus directory, CHED source/course labels, catalog ID validation, non-destructive import, duplicate handling, and student/admin route access. The full PHPUnit suite passed (80 tests, 774 assertions); changed PHP files passed syntax checks and `git diff --check` passed. Manual browser review remains deferred until the accumulated updates are uploaded to InfinityFree. There is no migration, deployment, or hosted update in this task. Broader catalog source review remains required before R-13 can be accepted.

## AI-use record

The agent researched the official NVSU Bayombong/Bambang program pages and CHED Region II recognized-program listing, then added a campus-grouped NVSU undergraduate offerings directory. The agent kept three CHED sample curriculum imports separate and did not infer that their semester plans are used by NVSU. No NVSU-approved curriculum documents were found/verified for these programs in this task; this directory is not a subject-level NVSU catalog.

## 2026-10-27 NVSU reference course-list expansion

An NVSU-hosted [Program descriptions PDF](https://sdg.nvsu.edu.ph/storage/files/OaEbS4Z7GeKxTX5c9o1kvzmC0rplTKzWi9fj2W5b.pdf) contains course descriptions and curriculum maps for Forestry, Agroforestry, Environmental Science, and Ecotourism. The app now exposes reference titles for BS Forestry, BS Agroforestry, and BS Ecotourism. The PDF is undated and does not verify current approved course sequencing; titles are imported with their source codes, without semester assignment. Environmental Science requires additional transcription/review before import. Other NVSU programs remain browse-only until course-level source documents are reviewed.

The 2026-10-27 additions have not been tested or browser-reviewed. There is no migration, deployment, or hosted update.

## 2026-10-27 NVSU CAS links for BSCS, BSIT, and BSIS

The campus directory links these degree entries to their corresponding [BSCS](https://cas.nvsu.edu.ph/courses/bscomsci), [BSIT](https://cas.nvsu.edu.ph/courses/bsinfotech), and [BSIS](https://cas.nvsu.edu.ph/courses/infosys) CAS pages. The earlier pass could not read the linked Drive PDF contents; the owner has since supplied curriculum PDF copies directly.

The CAS directory links have not been browser-reviewed. No files were staged, committed, pushed, or deployed.

## 2026-10-27 NVSU CAS curriculum transcription

The owner provided five local curriculum PDFs. Their searchable text was extracted locally and the program title, major, effective year, codes, and course titles were reviewed before entry. The files cover BSIS Service Management, BSIS Business Analytics (the attached file is named `BSIT-Business-Analytics.pdf`, but its cover identifies the program as BSIS), BSIT Web and Mobile App Development, BSIT Network Design and Management, and a BSCS curriculum with separate Game Development and Robotics majors. The six major-specific reference lists are now available to preview and import with their NVSU source document links.

All six documents identify Effective S.Y. 2019–2020 and cite CMO No. 25, s. 2015; the two BSIT PDFs also cite CMO No. 20, s. 2013 for general education. Duno imports course code and title only; it does not assign the curriculum's year/semester to a student's subjects. Elective slots and listed elective alternatives are included as source reference options. The copies supplied are historical and do not establish the current NVSU-approved plan. No schema migration or runtime network request was added.

Verification for the accumulated release on 2026-10-27: the full PHPUnit suite passed (85 tests, 860 assertions), Playwright passed (3/3 responsive browser tests), all changed PHP and JavaScript syntax checks passed, and the staged diff passed `git diff --cached --check`. The responsive browser review confirmed the NVSU Web and Mobile App Development preview and its source link at phone and desktop widths. Composer validation passed. Composer audit reported no known advisories in the lockfile, but Packagist was unreachable, so advisory freshness could not be confirmed. Production upload and hosted migration remain pending release handoff. The user-provided PDFs remain local source material and are not copied into the repository.
