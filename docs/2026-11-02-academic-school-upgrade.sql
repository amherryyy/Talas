-- Duno migration 2026-11-02-000002_AddAcademicSchoolNameToUsers.
-- Back up the database and select the Duno database in phpMyAdmin first.
-- Run only if the migration ledger has no 2026-11-02-000002 row and
-- users.academic_school_name does not exist. Apply after 000001.

SELECT `version`, `class`, `batch`
FROM `migrations`
WHERE `version` = '2026-11-02-000002';

SHOW COLUMNS FROM `users` LIKE 'academic_school_name';

-- Continue only when the ledger query and SHOW COLUMNS return no rows.
ALTER TABLE `users`
  ADD COLUMN `academic_school_name` VARCHAR(160) NULL;

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT
  '2026-11-02-000002',
  'App\\Database\\Migrations\\AddAcademicSchoolNameToUsers',
  'default',
  'App',
  UNIX_TIMESTAMP(),
  COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;

SELECT `id`, `version`, `class`, `batch`
FROM `migrations`
WHERE `version` = '2026-11-02-000002';

SHOW COLUMNS FROM `users` LIKE 'academic_school_name';

-- If ALTER succeeds but the ledger insert fails, do not run ALTER again.
-- Confirm the column exists, then insert only the missing migration row.
