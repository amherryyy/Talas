<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTopics extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'subject_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
            ],
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'importance' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'default'    => 'medium',
            ],
            'progress' => [
                'type'       => 'VARCHAR',
                'constraint' => 12,
                'default'    => 'not_started',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['subject_id', 'due_date']);
        $this->forge->addForeignKey('subject_id', 'subjects', 'id', 'RESTRICT', 'RESTRICT');
        $this->forge->createTable('topics');
    }

    public function down(): void
    {
        $this->forge->dropTable('topics', true);
    }
}
