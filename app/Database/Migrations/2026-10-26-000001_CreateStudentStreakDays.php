<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentStreakDays extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'activity_date' => ['type' => 'DATE'],
            'streak_days' => ['type' => 'INT', 'constraint' => 10, 'unsigned' => true],
            'xp_awarded' => ['type' => 'SMALLINT', 'constraint' => 5, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'activity_date']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('student_streak_days');
    }

    public function down(): void
    {
        $this->forge->dropTable('student_streak_days', true);
    }
}
