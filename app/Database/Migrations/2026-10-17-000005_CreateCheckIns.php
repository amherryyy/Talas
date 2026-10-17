<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCheckIns extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'workload_rating' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true],
            'energy_rating' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true],
            'recorded_at' => ['type' => 'DATETIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'recorded_at']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('check_ins');
    }

    public function down(): void
    {
        $this->forge->dropTable('check_ins', true);
    }
}
