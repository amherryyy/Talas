<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTopicDifficulty extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('topics', [
            'difficulty' => [
                'type'       => 'VARCHAR',
                'constraint' => 8,
                'default'    => 'medium',
                'after'      => 'importance',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('topics', 'difficulty');
    }
}
