<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

final class AddAcademicSchoolNameToUsers extends Migration
{
    public function up(): void
    {
        $this->forge->addColumn('users', [
            'academic_school_name' => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'academic_school_name');
    }
}
