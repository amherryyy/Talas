<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

/** Ensure each saved session's student, subject, and optional topic agree. */
class EnforceStudySessionReferenceIntegrity extends Migration
{
    public function up(): void
    {
        if ($this->db->DBDriver !== 'MySQLi') {
            return;
        }

        $ownerMismatches = $this->db->query(<<<'SQL'
            SELECT `s`.`id`
            FROM `study_sessions` AS `s`
            LEFT JOIN `subjects` AS `subj` ON `subj`.`id` = `s`.`subject_id`
            WHERE `subj`.`id` IS NULL OR `subj`.`user_id` <> `s`.`user_id`
            LIMIT 1
            SQL)->getNumRows();
        $topicMismatches = $this->db->query(<<<'SQL'
            SELECT `s`.`id`
            FROM `study_sessions` AS `s`
            JOIN `topics` AS `t` ON `t`.`id` = `s`.`topic_id`
            WHERE `s`.`topic_id` IS NOT NULL AND `t`.`subject_id` <> `s`.`subject_id`
            LIMIT 1
            SQL)->getNumRows();

        if ($ownerMismatches > 0 || $topicMismatches > 0) {
            throw new RuntimeException('Study session references are inconsistent; repair the rows before applying this migration.');
        }

        $this->db->query(<<<'SQL'
            ALTER TABLE `subjects`
              ADD UNIQUE KEY `subjects_user_id_id_unique` (`user_id`, `id`)
            SQL);

        $this->db->query(<<<'SQL'
            ALTER TABLE `topics`
              ADD UNIQUE KEY `topics_id_subject_id_unique` (`id`, `subject_id`)
            SQL);

        $this->db->query(<<<'SQL'
            ALTER TABLE `study_sessions`
              ADD CONSTRAINT `study_sessions_user_subject_foreign`
                FOREIGN KEY (`user_id`, `subject_id`)
                REFERENCES `subjects` (`user_id`, `id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            SQL);

        $this->db->query(<<<'SQL'
            ALTER TABLE `study_sessions`
              ADD CONSTRAINT `study_sessions_topic_subject_foreign`
                FOREIGN KEY (`topic_id`, `subject_id`)
                REFERENCES `topics` (`id`, `subject_id`)
                ON DELETE RESTRICT ON UPDATE RESTRICT
            SQL);

        // Add the stronger constraints before removing the original single-key
        // constraints so a failed DDL operation never leaves fewer safeguards.
        $this->db->query(<<<'SQL'
            ALTER TABLE `study_sessions`
              DROP FOREIGN KEY `study_sessions_subject_id_foreign`,
              DROP FOREIGN KEY `study_sessions_topic_id_foreign`
            SQL);
    }

    public function down(): void
    {
        // Keep the integrity constraints in place if migration bookkeeping is rolled back.
    }
}
