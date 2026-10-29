<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Aligns the hosted schema-only import's history constraints with the original
 * application migrations. Existing study history must survive topic changes.
 */
class AlignStudyHistoryForeignKeys extends Migration
{
    public function up(): void
    {
        // The production target is MySQL/MariaDB. Fresh SQLite test schemas
        // already get the intended actions from the original table migrations.
        if ($this->db->DBDriver !== 'MySQLi') {
            return;
        }

        // MySQL/MariaDB can raise errno 121 when a foreign key is dropped
        // and recreated with the same name in one ALTER TABLE statement.
        $this->db->query('ALTER TABLE `study_plan_items` DROP FOREIGN KEY `study_plan_items_topic_id_foreign`');
        $this->db->query(<<<'SQL'
            ALTER TABLE `study_plan_items`
              ADD CONSTRAINT `study_plan_items_topic_id_foreign`
                FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`)
                ON DELETE SET NULL ON UPDATE CASCADE
            SQL);

        $this->db->query(<<<'SQL'
            ALTER TABLE `study_sessions`
              DROP FOREIGN KEY `study_sessions_subject_id_foreign`,
              DROP FOREIGN KEY `study_sessions_topic_id_foreign`
            SQL);

        $this->db->query(<<<'SQL'
            ALTER TABLE `study_sessions`
              ADD CONSTRAINT `study_sessions_subject_id_foreign`
                FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            SQL);

        $this->db->query(<<<'SQL'
            ALTER TABLE `study_sessions`
              ADD CONSTRAINT `study_sessions_topic_id_foreign`
                FOREIGN KEY (`topic_id`) REFERENCES `topics` (`id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            SQL);
    }

    public function down(): void
    {
        // Keep the data-preserving constraints in place if this bookkeeping
        // migration is rolled back. Reverting them could silently erase history.
    }
}
