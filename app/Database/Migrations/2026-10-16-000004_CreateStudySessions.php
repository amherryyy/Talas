<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudySessions extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'subject_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'topic_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'null' => true],
            'duration_minutes' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'completed_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'completed_at']);
        $this->forge->addKey('subject_id');
        $this->forge->addKey('topic_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('subject_id', 'subjects', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('topic_id', 'topics', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('study_sessions');
    }

    public function down(): void
    {
        $this->forge->dropTable('study_sessions', true);
    }
}
