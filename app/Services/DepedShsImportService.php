<?php

namespace App\Services;

use App\Models\SubjectModel;
use Config\Database;

class DepedShsImportService
{
    public function __construct(
        private readonly DepedShsCatalog $catalog = new DepedShsCatalog(),
        private readonly SubjectModel $subjects = new SubjectModel(),
    ) {
    }

    /** @param list<string> $subjectIds @return array{ok: bool, imported: int, duplicates: int} */
    public function import(array $subjectIds, int $ownerId): array
    {
        $selected = $this->catalog->selectedSubjects($subjectIds);
        if ($selected === []) {
            return ['ok' => false, 'imported' => 0, 'duplicates' => 0];
        }

        $existingNames = [];
        foreach ($this->subjects->findAllForOwner($ownerId) as $subject) {
            $existingNames[mb_strtolower(trim((string) $subject['name']))] = true;
        }

        $db = Database::connect();
        $db->transBegin();
        $imported = 0;
        $duplicates = 0;

        foreach ($selected as $subject) {
            $key = mb_strtolower(trim($subject['title']));
            if (isset($existingNames[$key])) {
                $duplicates++;
                continue;
            }

            if ($this->subjects->insert(['user_id' => $ownerId, 'name' => $subject['title']]) === false) {
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
}
