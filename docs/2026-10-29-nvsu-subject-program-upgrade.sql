-- For the live Duno database after confirming its migration ledger is at
-- 11 rows / batch 6 and subjects.program_id does not already exist.
-- Back up the database first. Run once with the Duno database selected.
-- Do not run if the ledger or table state differs.

ALTER TABLE `subjects`
  ADD COLUMN `program_id` varchar(80) DEFAULT NULL AFTER `name`;

INSERT INTO `migrations`
  (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES
  (12, '2026-10-29-000001', 'App\\Database\\Migrations\\AddNvsuProgramToSubjects',
   'default', 'App', 1793232000, 7);
