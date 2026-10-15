<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDailyPlans extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'plan_date' => ['type' => 'DATE'],
            'available_minutes' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'plan_date'], false, true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('study_plans');

        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'plan_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'topic_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'topic_title' => ['type' => 'VARCHAR', 'constraint' => 120],
            'subject_name' => ['type' => 'VARCHAR', 'constraint' => 120],
            'duration_minutes' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'position' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['plan_id', 'position']);
        $this->forge->addKey(['plan_id', 'topic_id'], false, true);
        $this->forge->addForeignKey('plan_id', 'study_plans', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('topic_id', 'topics', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('study_plan_items');
    }

    public function down(): void
    {
        $this->forge->dropTable('study_plan_items', true);
        $this->forge->dropTable('study_plans', true);
    }
}
