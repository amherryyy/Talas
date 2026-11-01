<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAcademicProgramToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'academic_program_id' => ['type' => 'VARCHAR', 'constraint' => 80, 'null' => true],
            'academic_program_title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'academic_program_selected_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['academic_program_id', 'academic_program_title', 'academic_program_selected_at']);
    }
}
