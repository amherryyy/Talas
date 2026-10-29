-- For the existing Duno database only when its migrations ledger has exactly
-- 10 rows, the latest row is batch 5, and this table does not already exist.
-- Back up the database first. Run once with the Duno database selected.
-- If the ledger differs, do not run the INSERT unchanged; reconcile the
-- migration ID and batch against the actual ledger.

CREATE TABLE `registration_verification_limits` (
  `user_id` int(11) unsigned NOT NULL,
  `failed_attempts` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `last_sent_at` datetime DEFAULT NULL,
  `sends_today` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `send_day` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `registration_verification_limits_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `migrations`
  (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES
  (11, '2026-10-27-000001', 'App\\Database\\Migrations\\CreateRegistrationVerificationLimits',
   'default', 'App', 1793059200, 6);
