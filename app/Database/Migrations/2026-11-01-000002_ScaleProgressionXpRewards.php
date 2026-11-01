<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ScaleProgressionXpRewards extends Migration
{
    public function up(): void
    {
        $this->db->table('student_streak_days')
            ->set('xp_awarded', 'xp_awarded * 10', false)
            ->where('xp_awarded', 500)
            ->update();
    }

    public function down(): void
    {
        // New 5,000 XP visits are indistinguishable from converted history.
        // Keep earned progression intact if migration bookkeeping is rolled back.
    }
}
