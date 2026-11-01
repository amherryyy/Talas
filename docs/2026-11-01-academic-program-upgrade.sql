-- Run in phpMyAdmin with the Duno database selected. Back up the database first.
-- Do not run if any of the three columns already exists or the migration ledger
-- already contains version 2026-11-01-000001.

SELECT `version`, `class`, `batch`
FROM `migrations`
WHERE `version` = '2026-11-01-000001';

SHOW COLUMNS FROM `users` LIKE 'academic_program_id';
SHOW COLUMNS FROM `users` LIKE 'academic_program_title';
SHOW COLUMNS FROM `users` LIKE 'academic_program_selected_at';

-- Continue only when the ledger query returns no row and all three SHOW COLUMNS
-- queries return no rows. These nullable fields do not modify existing accounts.
ALTER TABLE `users`
  ADD COLUMN `academic_program_id` VARCHAR(80) NULL,
  ADD COLUMN `academic_program_title` VARCHAR(255) NULL,
  ADD COLUMN `academic_program_selected_at` DATETIME NULL;

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT
  '2026-11-01-000001',
  'App\\Database\\Migrations\\AddAcademicProgramToUsers',
  'default',
  'App',
  UNIX_TIMESTAMP(),
  COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;

SELECT `id`, `version`, `class`, `batch`
FROM `migrations`
WHERE `version` = '2026-11-01-000001';

SHOW COLUMNS FROM `users` LIKE 'academic_program_id';
SHOW COLUMNS FROM `users` LIKE 'academic_program_title';
SHOW COLUMNS FROM `users` LIKE 'academic_program_selected_at';

-- Confirm this upgrade did not fill legacy user or subject data.
SELECT COUNT(*) AS users_with_program FROM `users` WHERE `academic_program_selected_at` IS NOT NULL;
SELECT COUNT(*) AS subjects_with_program FROM `subjects` WHERE `program_id` IS NOT NULL;
