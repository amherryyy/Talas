-- Existing Duno database only. Back up first.
-- Preconditions: migration 2026-10-29-000003 is absent and the named foreign
-- keys exist on the expected columns. Inspect information_schema first; stop
-- if the live constraint names or table definitions differ.
-- This upgrade preserves saved sessions and plan item snapshots.
-- Keep each DROP and ADD in separate ALTER statements. MySQL/MariaDB can raise
-- errno 121 if the same foreign key name is dropped and recreated in a single
-- ALTER TABLE statement. If an earlier attempt failed with that error, verify
-- the original constraints still exist and migration 14 remains absent before
-- running this corrected script.

ALTER TABLE `study_plan_items`
  DROP FOREIGN KEY `study_plan_items_topic_id_foreign`;

ALTER TABLE `study_plan_items`
  ADD CONSTRAINT `study_plan_items_topic_id_foreign`
    FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `study_sessions`
  DROP FOREIGN KEY `study_sessions_subject_id_foreign`,
  DROP FOREIGN KEY `study_sessions_topic_id_foreign`;

ALTER TABLE `study_sessions`
  ADD CONSTRAINT `study_sessions_subject_id_foreign`
    FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

ALTER TABLE `study_sessions`
  ADD CONSTRAINT `study_sessions_topic_id_foreign`
    FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE;

INSERT INTO `migrations`
  (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`)
VALUES
  (14, '2026-10-29-000003', 'App\\Database\\Migrations\\AlignStudyHistoryForeignKeys',
   'default', 'App', 1793232002, 9);
