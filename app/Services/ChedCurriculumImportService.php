<?php

namespace App\Services;

use App\Models\SubjectModel;
use Config\Database;

class ChedCurriculumImportService
{
    public function __construct(
        private readonly ChedCurriculumCatalog $catalog = new ChedCurriculumCatalog(),
        private readonly SubjectModel $subjects = new SubjectModel(),
    ) {
    }

    /**
     * @param list<string> $courseIds
     * @return array{ok: bool, imported: int, duplicates: int}
     */
    public function import(string $programId, array $courseIds, int $ownerId): array
    {
        $courses = $this->catalog->selectedCourses($programId, $courseIds);
        if ($courses === []) {
            return ['ok' => false, 'imported' => 0, 'duplicates' => 0];
        }

        $existingNames = [];
        foreach ($this->subjects->findAllForOwner($ownerId) as $subject) {
            $existingNames[$this->normalize((string) $subject['name'])] = true;
        }

        $db = Database::connect();
        $db->transBegin();
        $imported = 0;
        $duplicates = 0;

        foreach ($courses as $course) {
            $key = $this->normalize($course['title']);
            if (isset($existingNames[$key])) {
                $duplicates++;
                continue;
            }

            if ($this->subjects->insert([
                'user_id' => $ownerId,
                'name'    => $course['title'],
            ]) === false) {
                $db->transRollback();

                return ['ok' => false, 'imported' => 0, 'duplicates' => 0];
            }

            $existingNames[$key] = true;
            $imported++;
        }

        if ($db->transStatus() === false) {
            $db->transRollback();

            return ['ok' => false, 'imported' => 0, 'duplicates' => 0];
        }

        $db->transCommit();

        return ['ok' => true, 'imported' => $imported, 'duplicates' => $duplicates];
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
