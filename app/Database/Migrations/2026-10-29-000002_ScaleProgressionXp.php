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
        // The upgraded value (500 XP) is also awarded by the live application,
        // so it is impossible to distinguish new awards from migrated rows.
        // Keep saved progression intact if the migration is rolled back.
    }
}
