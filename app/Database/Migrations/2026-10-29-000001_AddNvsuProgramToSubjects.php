<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNvsuProgramToSubjects extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('subjects', [
            'program_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 80,
                'null'       => true,
                'after'      => 'name',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('subjects', 'program_id');
    }
}
