# NVSU program-linked subjects

**Status:** Implemented in the current workspace; migration and hosted review pending.

## Owner request

Let students select an NVSU program while adding a subject, choose a course from that program's sourced reference list, and filter their own subject list by program.

## Behavior

- Subject forms offer the nine NVSU programs that have a sourced course list. Program choices without transcribed course lists remain in the browse-only campus directory.
- Selecting a catalog course fills the subject name. The server validates the course against the selected program and stores the canonical course title and program ID.
- Students can still enter their own subject titles, attach them to one of those programs, or leave the program empty.
- The subject list can be filtered to one selected NVSU program. Existing subjects stay unchanged until a student edits them or imports them from a specific NVSU course list.
- NVSU course references retain their dated-source notices; this feature does not assert that a reference list is the current approved curriculum.

## Schema and rollout

The nullable `subjects.program_id` column preserves all current subjects. Fresh installs get it from the migration. Existing hosted databases need the reviewed SQL upgrade after a backup and a check that the ledger is at 11 rows/batch 6 and the column is absent. The upgrade records migration 12/batch 7. Do not run it twice.

## Verification

The full PHPUnit suite passes with 88 tests and 902 assertions. PHP syntax checks pass for 174 files, both JavaScript assets pass `node --check`, and `composer validate` passes. Manual browser review and hosted migration remain pending. No deployment has been made.
