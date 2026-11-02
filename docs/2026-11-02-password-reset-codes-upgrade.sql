-- Duno hosted password reset code table.
-- Back up the database first. Select the Duno database in phpMyAdmin.
-- Run only if password_reset_codes is absent and the migrations table does
-- not already contain version 2026-11-02-000001. Inspect the migration ledger
-- first; this records the next batch after the current highest batch.
-- Do not upload this documentation SQL file to the public web root.

CREATE TABLE `password_reset_codes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `code_hash` varchar(255) NOT NULL,
  `failed_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `password_reset_codes_user_id_unique` (`user_id`),
  CONSTRAINT `password_reset_codes_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migrations`
  (`version`, `class`, `group`, `namespace`, `time`, `batch`)
SELECT
  '2026-11-02-000001',
  'App\\Database\\Migrations\\CreatePasswordResetCodes',
  'default',
  'App',
  UNIX_TIMESTAMP(),
  COALESCE(MAX(`batch`), 0) + 1
FROM `migrations`;
