-- Duno progression reward scale update for an existing hosted database.
-- Select the Duno database and export a backup before continuing.
-- Expected current ledger: migration 2026-10-30-000002, row 18, batch 13.
-- Stop if the preflight does not show that row exactly once or if the new
-- migration already exists. The update only converts existing 500-XP rows.

SELECT `version`, `class`, `batch`
FROM `migrations`
WHERE `version` IN ('2026-10-30-000002', '2026-11-01-000002');

SELECT COUNT(*) AS existing_500_xp_visit_rows
FROM `student_streak_days`
WHERE `xp_awarded` = 500;

-- Continue only when the preflight state matches the comments above.
START TRANSACTION;

UPDATE `student_streak_days`
SET `xp_awarded` = `xp_awarded` * 10
WHERE `xp_awarded` = 500;

INSERT INTO `migrations` (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT
  '2026-11-01-000002',
  'App\\Database\\Migrations\\ScaleProgressionXpRewards',
  'default',
  'App',
  UNIX_TIMESTAMP(),
  COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;

COMMIT;

SELECT `id`, `version`, `class`, `batch`
FROM `migrations`
WHERE `version` = '2026-11-01-000002';

SELECT COUNT(*) AS remaining_500_xp_visit_rows
FROM `student_streak_days`
WHERE `xp_awarded` = 500;

SELECT COUNT(*) AS converted_5000_xp_visit_rows
FROM `student_streak_days`
WHERE `xp_awarded` = 5000;
