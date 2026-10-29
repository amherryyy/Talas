<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ScaleProgressionXp extends Migration
{
    public function up(): void
    {
        $this->db->table('student_streak_days')
            ->set('xp_awarded', 'xp_awarded * 100', false)
            ->where('xp_awarded', 5)
            ->update();
    }

    public function down(): void
    {
        $this->db->table('student_streak_days')
            ->set('xp_awarded', 'xp_awarded / 100', false)
            ->update();
    }
}
