<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlanItemStartTimes extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('study_plan_items', [
            'start_time' => ['type' => 'TIME', 'null' => true, 'after' => 'duration_minutes'],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('study_plan_items', 'start_time');
    }
}
