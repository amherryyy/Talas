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
    protected $allowedFields    = ['user_id', 'name', 'program_id'];
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
    public function findAllForOwner(int $ownerId, ?string $programId = null): array
    {
        $builder = $this->where('user_id', $ownerId);
        if ($programId !== null && $programId !== '') {
            $builder->where('program_id', $programId);
        }

        return $builder->orderBy('name', 'ASC')->findAll();
    }

    /** @return list<array<string, mixed>> */
    public function searchForOwner(int $ownerId, string $query, ?string $programId = null): array
    {
        $builder = $this->builder()->where('user_id', $ownerId);

        if ($programId !== null && $programId !== '') {
            $builder->where('program_id', $programId);
        }

        if ($query !== '') {
            $builder->like('name', $query);
        }

        return $builder->orderBy('name', 'ASC')->get()->getResultArray();
    }

    /** @return array<string, mixed>|null */
    public function findForOwner(int $subjectId, int $ownerId): ?array
    {
        $subject = $this->where('id', $subjectId)
            ->where('user_id', $ownerId)
            ->first();

        return is_array($subject) ? $subject : null;
    }

    public function updateForOwner(int $subjectId, int $ownerId, string $name, ?string $programId): bool
    {
        return $this->where('id', $subjectId)
            ->where('user_id', $ownerId)
            ->update(null, ['name' => $name, 'program_id' => $programId]);
    }

    public function deleteForOwner(int $subjectId, int $ownerId): bool
    {
        if ($this->db->tableExists('study_sessions')) {
            $hasSavedSessions = $this->db->table('study_sessions')
                ->join('subjects', 'subjects.id = study_sessions.subject_id')
                ->where('subjects.id', $subjectId)
                ->where('subjects.user_id', $ownerId)
                ->limit(1)
                ->countAllResults() > 0;

            if ($hasSavedSessions) {
                return false;
            }
        }

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
