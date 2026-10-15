<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table            = 'subjects';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_id', 'name'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[120]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Enter a subject name.',
            'max_length' => 'Subject names must be 120 characters or fewer.',
        ],
    ];

    /** @return list<array<string, mixed>> */
    public function findAllForOwner(int $ownerId): array
    {
        return $this->where('user_id', $ownerId)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /** @return array<string, mixed>|null */
    public function findForOwner(int $subjectId, int $ownerId): ?array
    {
        $subject = $this->where('id', $subjectId)
            ->where('user_id', $ownerId)
            ->first();

        return is_array($subject) ? $subject : null;
    }

    public function updateForOwner(int $subjectId, int $ownerId, string $name): bool
    {
        return $this->where('id', $subjectId)
            ->where('user_id', $ownerId)
            ->update(null, ['name' => $name]);
    }

    public function deleteForOwner(int $subjectId, int $ownerId): bool
    {
        if ($this->db->tableExists('topics')) {
            $hasTopics = $this->db->table('subjects')
                ->join('topics', 'topics.subject_id = subjects.id')
                ->where('subjects.id', $subjectId)
                ->where('subjects.user_id', $ownerId)
                ->limit(1)
                ->countAllResults() > 0;

            if ($hasTopics) {
                return false;
            }
        }

        return $this->where('id', $subjectId)
            ->where('user_id', $ownerId)
            ->delete();
    }
}
