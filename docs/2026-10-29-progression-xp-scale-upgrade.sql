-- For the existing Duno database after confirming the migration ledger has
-- 12 rows / latest batch 7 and the NVSU subjects.program_id column exists.
-- Back up the database first. Run once with the Duno database selected.
-- Do not run if the ledger or table state differs.

UPDATE `student_streak_days`
SET `xp_awarded` = `xp_awarded` * 100
WHERE `xp_awarded` = 5;

INSERT INTO `migrations`
  (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES
  (13, '2026-10-29-000002', 'App\\Database\\Migrations\\ScaleProgressionXp',
   'default', 'App', 1793232001, 8);
