<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

/** Bind topics and saved plan blocks to the same student at the database layer. */
class EnforceStudyPlanOwnership extends Migration
{
    public function up(): void
    {
        if (! $this->db->fieldExists('user_id', 'topics')) {
            $this->forge->addColumn('topics', [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'subject_id',
                ],
            ]);
        }

        if (! $this->db->fieldExists('user_id', 'study_plan_items')) {
            $this->forge->addColumn('study_plan_items', [
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'plan_id',
                ],
            ]);
        }

        $this->backfillOwnerIds();
        $this->assertConsistentOwnership();

        // MySQL/MariaDB can add these composite constraints without rebuilding
        // tables. SQLite migrations still receive/backfill both columns; its
        // existing app-level owner checks remain active in the test runtime.
        if ($this->db->DBDriver !== 'MySQLi') {
            return;
        }

        $this->db->query('ALTER TABLE `topics` MODIFY `user_id` INT(10) UNSIGNED NOT NULL');
        $this->db->query('ALTER TABLE `study_plan_items` MODIFY `user_id` INT(10) UNSIGNED NOT NULL');

        $this->ensureUniqueIndex('subjects', 'subjects_user_id_id_unique', '`user_id`, `id`');
        $this->ensureUniqueIndex('topics', 'topics_user_id_id_unique', '`user_id`, `id`');
        $this->ensureUniqueIndex('study_plans', 'study_plans_user_id_id_unique', '`user_id`, `id`');

        $this->ensureForeignKey('topics_owner_subject_foreign', <<<'SQL'
            ALTER TABLE `topics`
              ADD CONSTRAINT `topics_owner_subject_foreign`
                FOREIGN KEY (`user_id`, `subject_id`)
                REFERENCES `subjects` (`user_id`, `id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            SQL);

        $this->ensureForeignKey('study_plan_items_owner_plan_foreign', <<<'SQL'
            ALTER TABLE `study_plan_items`
              ADD CONSTRAINT `study_plan_items_owner_plan_foreign`
                FOREIGN KEY (`user_id`, `plan_id`)
                REFERENCES `study_plans` (`user_id`, `id`)
                ON DELETE CASCADE ON UPDATE CASCADE
            SQL);

        $this->ensureForeignKey('study_plan_items_owner_topic_foreign', <<<'SQL'
            ALTER TABLE `study_plan_items`
              ADD CONSTRAINT `study_plan_items_owner_topic_foreign`
                FOREIGN KEY (`user_id`, `topic_id`)
                REFERENCES `topics` (`user_id`, `id`)
                ON DELETE RESTRICT ON UPDATE RESTRICT
            SQL);

        // Install the owner-aware constraints first; then remove redundant
        // single-column constraints. TopicModel clears live topic references
        // before deleting a topic, so historical plan snapshots remain intact.
        $this->dropForeignKeyIfPresent('topics', 'topics_subject_id_foreign');
        $this->dropForeignKeyIfPresent('study_plan_items', 'study_plan_items_plan_id_foreign');
        $this->dropForeignKeyIfPresent('study_plan_items', 'study_plan_items_topic_id_foreign');
    }

    public function down(): void
    {
        // Keep owner constraints and populated owner columns in place if the
        // migration ledger is rolled back; a destructive reverse needs a
        // separately reviewed data migration.
    }

    private function backfillOwnerIds(): void
    {
        $topics = $this->db->table('topics AS t')
            ->select('t.id, s.user_id')
            ->join('subjects AS s', 's.id = t.subject_id')
            ->where('t.user_id', null)
            ->get()->getResultArray();

        foreach ($topics as $topic) {
            $this->db->table('topics')
                ->where('id', (int) $topic['id'])
                ->update(['user_id' => (int) $topic['user_id']]);
        }

        $items = $this->db->table('study_plan_items AS i')
            ->select('i.id, p.user_id')
            ->join('study_plans AS p', 'p.id = i.plan_id')
            ->where('i.user_id', null)
            ->get()->getResultArray();

        foreach ($items as $item) {
            $this->db->table('study_plan_items')
                ->where('id', (int) $item['id'])
                ->update(['user_id' => (int) $item['user_id']]);
        }
    }

    private function assertConsistentOwnership(): void
    {
        // The PHPUnit connection uses a table prefix (`db_`). Protecting and
        // prefixing these identifiers keeps the preflight valid in tests and
        // on installations that configure a database prefix.
        $topicsTable = $this->db->protectIdentifiers('topics', true);
        $subjectsTable = $this->db->protectIdentifiers('subjects', true);
        $itemsTable = $this->db->protectIdentifiers('study_plan_items', true);
        $plansTable = $this->db->protectIdentifiers('study_plans', true);

        $checks = [
            'a topic without a matching subject owner' => "
                SELECT t.id
                FROM {$topicsTable} AS t
                LEFT JOIN {$subjectsTable} AS s ON s.id = t.subject_id
                WHERE s.id IS NULL OR t.user_id IS NULL OR t.user_id <> s.user_id
                LIMIT 1
                ",
            'a plan item without a matching plan owner' => "
                SELECT i.id
                FROM {$itemsTable} AS i
                LEFT JOIN {$plansTable} AS p ON p.id = i.plan_id
                WHERE p.id IS NULL OR i.user_id IS NULL OR i.user_id <> p.user_id
                LIMIT 1
                ",
            'a plan item linked to another student\'s topic' => "
                SELECT i.id
                FROM {$itemsTable} AS i
                JOIN {$topicsTable} AS t ON t.id = i.topic_id
                WHERE i.topic_id IS NOT NULL AND i.user_id <> t.user_id
                LIMIT 1
                ",
        ];

        foreach ($checks as $issue => $sql) {
            if ($this->db->query($sql)->getNumRows() > 0) {
                throw new RuntimeException('Cannot enforce study-plan ownership: ' . $issue . '. Repair the affected rows before retrying.');
            }
        }
    }

    private function ensureUniqueIndex(string $table, string $index, string $columns): void
    {
        $exists = $this->db->query(
            'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$table, $index],
        )->getNumRows() > 0;

        if (! $exists) {
            $this->db->query('ALTER TABLE `' . $table . '` ADD UNIQUE KEY `' . $index . '` (' . $columns . ')');
        }
    }

    private function ensureForeignKey(string $name, string $sql): void
    {
        $exists = $this->db->query(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\' LIMIT 1',
            [$name],
        )->getNumRows() > 0;

        if (! $exists) {
            $this->db->query($sql);
        }
    }

    private function dropForeignKeyIfPresent(string $table, string $name): void
    {
        $exists = $this->db->query(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? AND CONSTRAINT_TYPE = \'FOREIGN KEY\' LIMIT 1',
            [$table, $name],
        )->getNumRows() > 0;

        if ($exists) {
            $this->db->query('ALTER TABLE `' . $table . '` DROP FOREIGN KEY `' . $name . '`');
        }
    }
}
