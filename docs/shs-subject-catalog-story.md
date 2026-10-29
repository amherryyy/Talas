# R-14 DepEd Senior High School subject catalog

**Date / status:** 2026-10-26 / Implemented locally; browser review and upload deferred.

## User outcome

Students can browse the published subject-guide list for DepEd's strengthened Senior High School curriculum and add selected subjects to their Duno subject list.

## Current curriculum and transition

DepEd's [Strengthened SHS Program page](https://www.deped.gov.ph/strengthened-shs-program/) publishes the current curriculum guides for Core Subjects, Academic Electives, TechPro Electives, and new Grade 12 guides. The [full implementation memorandum, DM 012 s. 2026](https://www.deped.gov.ph/wp-content/uploads/DM_s2026_012r.pdf), directs all schools to implement the strengthened curriculum for incoming Grade 11 learners in SY 2026-2027. It also says Grade 12 learners at non-pilot schools continue the former SHS curriculum during the transition, while Grade 12 pilot schools continue the strengthened program. This catalog represents the strengthened curriculum only; legacy Grade 12 subjects are not included.

The strengthened program has five core subjects, streamlined from the old 15-subject core, and elective subjects grouped into Academic and Technical-Professional tracks. Clusters organize choices; they are not fixed strands. DepEd says a school may offer only tracks/electives for which it has the teachers and learning resources, and only subjects with officially released and approved curriculum guides may be offered. Thus this is a national subject-guide reference, not a promise that every school offers every elective.

The catalog is versioned to DepEd Order No. 017, s. 2026 and includes the five required core subjects, the published Academic and TechPro elective guides, and the listed new Grade 12 Academic and Field Experience guides. The DepEd guide page currently lists no guide under Sports, Health, and Wellness, so no subjects from that cluster are included. It uses the official titles where possible. The paired English/Filipino communication guide files are represented as one combined core course. No school-specific institutional subjects or inferred/unreleased subject guides are added.

## Interaction and data handling

- Browse the subject list by track, cluster, and guide group.
- Core courses start selected; elective courses start unselected. Students can select or clear the full list, then import selected titles.
- Import adds editable subjects to the authenticated student's own subject list, retains existing subjects, and skips case-insensitive duplicates. It does not save grade level, track, cluster, term, or prerequisite metadata to Duno subjects.
- Catalog IDs are the only accepted POST values. The student route is authenticated, student-role restricted, and protected by CSRF.
- Catalog data is static PHP. It adds no schema migration, runtime API call, or external package.

## Acceptance and limitations

- DepEd source and SY 2026-2027 version are visible, with transition notes for Grade 12.
- Course grouping distinguishes required Core Subjects from optional Academic and TechPro subjects and identifies new Grade 12 guides.
- The legacy SHS subject catalogue and school-specific elective availability are outside this initial entry. Students in Grade 12 at non-pilot schools must follow their school's legacy curriculum.
- Curriculum guides can change. Review the official DepEd page and current issuance before expanding or refreshing this catalog.

## Verification and release

Focused `StudentSubjectsTest` passed (11 tests, 139 assertions). The full PHPUnit suite passed (81 tests, 795 assertions). Changed PHP files passed syntax checks, `node --check` passed for the updated selection module, and `git diff --check` passed. Composer validation was unavailable because the Composer CLI is not installed in this shell; no dependency changed. Browser review is deferred until the accumulated changes are uploaded to InfinityFree. No upload or deployment was done.

## AI-use record

The agent reviewed DepEd's official Strengthened SHS curriculum page, DM 012 s. 2026, and DM 048 s. 2025; transcribed the published guide titles into a static catalog and added a student-owned additive import flow. The implementation does not claim that electives are universally offered or that it includes the legacy curriculum used by transitional Grade 12 cohorts.
