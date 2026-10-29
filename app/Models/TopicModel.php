<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class TopicModel extends Model
{
    protected $table            = 'topics';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['subject_id', 'title', 'due_date', 'importance', 'progress'];
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $validationRules = [
        'title'      => 'required|max_length[120]',
        'due_date'   => 'permit_empty|valid_date[Y-m-d]',
        'importance' => 'required|in_list[low,medium,high]',
        'progress'   => 'required|in_list[not_started,in_progress,done]',
    ];

    protected $validationMessages = [
        'title' => [
            'required'   => 'Enter a topic title.',
            'max_length' => 'Topic titles must be 120 characters or fewer.',
        ],
        'due_date' => [
            'valid_date' => 'Enter a valid due date.',
        ],
        'importance' => [
            'in_list' => 'Choose low, medium, or high importance.',
        ],
        'progress' => [
            'in_list' => 'Choose not started, in progress, or done.',
        ],
    ];

    /** @return list<array<string, mixed>> */
    public function findAllForSubjectOwner(int $subjectId, int $ownerId): array
    {
        return $this->db->table('topics')
            ->select('topics.*')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->where('topics.subject_id', $subjectId)
            ->where('subjects.user_id', $ownerId)
            ->orderBy('topics.due_date', 'ASC')
            ->orderBy('topics.title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** @param array{query?: string, subject_id?: int|null, importance?: string, progress?: string, due_state?: string} $filters
     *  @return list<array<string, mixed>>
     */
    public function searchForOwner(int $ownerId, array $filters = [], ?string $today = null): array
    {
        $builder = $this->db->table('topics')
            ->select('topics.*, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->where('subjects.user_id', $ownerId);

        $query = trim((string) ($filters['query'] ?? ''));
        if ($query !== '') {
            $builder->groupStart()
                ->like('topics.title', $query)
                ->orLike('subjects.name', $query)
                ->groupEnd();
        }

        $subjectId = $filters['subject_id'] ?? null;
        if (is_int($subjectId) && $subjectId > 0) {
            $builder->where('topics.subject_id', $subjectId);
        }

        $importance = $filters['importance'] ?? '';
        if (in_array($importance, ['low', 'medium', 'high'], true)) {
            $builder->where('topics.importance', $importance);
        }

        $progress = $filters['progress'] ?? '';
        if (in_array($progress, ['not_started', 'in_progress', 'done'], true)) {
            $builder->where('topics.progress', $progress);
        }

        $dueState = $filters['due_state'] ?? '';
        if ($dueState === 'undated') {
            $builder->where('topics.due_date', null);
        } elseif (in_array($dueState, ['upcoming', 'overdue'], true)) {
            $today ??= date('Y-m-d');
            $builder->where('topics.progress !=', 'done');
            if ($dueState === 'upcoming') {
                $builder->where('topics.due_date >=', $today);
            } else {
                $builder->where('topics.due_date <', $today);
            }
        }

        return $builder
            ->orderBy('topics.due_date', 'ASC')
            ->orderBy('subjects.name', 'ASC')
            ->orderBy('topics.title', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** @return list<array<string, mixed>> */
    public function findAllForOwner(int $ownerId): array
    {
        return $this->db->table('topics')
            ->select('topics.*, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->where('subjects.user_id', $ownerId)
            ->orderBy('topics.id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /** @return array<string, mixed>|null */
    public function findForSubjectOwner(int $subjectId, int $topicId, int $ownerId): ?array
    {
        $topic = $this->db->table('topics')
            ->select('topics.*')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->where('topics.subject_id', $subjectId)
            ->where('topics.id', $topicId)
            ->where('subjects.user_id', $ownerId)
            ->get()
            ->getRowArray();

        return is_array($topic) ? $topic : null;
    }

    /** @param array<string, mixed> $topic */
    public function insertForSubjectOwner(int $subjectId, int $ownerId, array $topic): int|false
    {
        if ((new SubjectModel())->findForOwner($subjectId, $ownerId) === null) {
            return false;
        }

        $topic['subject_id'] = $subjectId;

        return $this->insert($topic, true);
    }

    /** @param array<string, mixed> $topic */
    public function updateForSubjectOwner(int $subjectId, int $topicId, int $ownerId, array $topic): bool
    {
        if (! $this->validate($topic)) {
            return false;
        }

        $ownedSubjects = $this->db->table('subjects')
            ->select('id')
            ->where('user_id', $ownerId);

        return $this->builder()
            ->where('id', $topicId)
            ->where('subject_id', $subjectId)
            ->whereIn('subject_id', $ownedSubjects)
            ->update($topic);
    }

    public function updateProgressForOwner(int $topicId, int $ownerId, string $progress): bool
    {
        if (! in_array($progress, ['not_started', 'in_progress', 'done'], true)) {
            return false;
        }

        $ownedTopic = $this->db->table('topics')
            ->select('topics.id')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->where('topics.id', $topicId)
            ->where('subjects.user_id', $ownerId)
            ->get()
            ->getRowArray();
        if (! is_array($ownedTopic)) {
            return false;
        }

        $ownedSubjects = $this->db->table('subjects')
            ->select('id')
            ->where('user_id', $ownerId);

        return $this->builder()
            ->where('id', $topicId)
            ->whereIn('subject_id', $ownedSubjects)
            ->update(['progress' => $progress, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    public function deleteForSubjectOwner(int $subjectId, int $topicId, int $ownerId): bool
    {
        $ownedSubjects = $this->db->table('subjects')
            ->select('id')
            ->where('user_id', $ownerId);

        if ($this->hasSavedSessionReference($topicId)) {
            return false;
        }

        try {
            return $this->builder()
                ->where('id', $topicId)
                ->where('subject_id', $subjectId)
                ->whereIn('subject_id', $ownedSubjects)
                ->delete();
        } catch (DatabaseException $exception) {
            if ($this->hasSavedSessionReference($topicId)) {
                return false;
            }

            throw $exception;
        }
    }

    private function hasSavedSessionReference(int $topicId): bool
    {
        $sessionTable = $this->db->prefixTable('study_sessions');
        if (! $this->db->tableExists($sessionTable, false)) {
            return false;
        }

        $sessionFields = $this->db->getFieldNames('study_sessions');
        if (! is_array($sessionFields) || ! in_array('topic_id', $sessionFields, true)) {
            return false;
        }

        return $this->db->table('study_sessions')
            ->where('topic_id', $topicId)
            ->limit(1)
            ->countAllResults() > 0;
    }
}
