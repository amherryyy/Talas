<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;
use DateTimeImmutable;
use DateTimeZone;

class StudySessionModel extends Model
{
    protected $table = 'study_sessions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'subject_id', 'topic_id', 'duration_minutes', 'completed_at'];
    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function findAllForOwner(int $ownerId): array
    {
        return $this->ownerQuery($ownerId)
            ->orderBy('study_sessions.completed_at', 'DESC')
            ->orderBy('study_sessions.id', 'DESC')
            ->get()->getResultArray();
    }

    /** @return list<array<string, mixed>> */
    public function findForOwnerPeriod(int $ownerId, ?string $start, ?string $end): array
    {
        $builder = $this->ownerQuery($ownerId);
        if ($start !== null) {
            $builder->where('study_sessions.completed_at >=', $start);
        }
        if ($end !== null) {
            $builder->where('study_sessions.completed_at <', $end);
        }

        return $builder
            ->orderBy('study_sessions.completed_at', 'DESC')
            ->orderBy('study_sessions.id', 'DESC')
            ->get()->getResultArray();
    }

    public function totalMinutesForOwner(int $ownerId): int
    {
        $row = $this->db->table('study_sessions')
            ->select('COALESCE(SUM(duration_minutes), 0) AS total_minutes', false)
            ->where('user_id', $ownerId)
            ->get()->getRowArray();

        return (int) ($row['total_minutes'] ?? 0);
    }

    /** @return array{total_minutes: int, session_count: int, study_days: int} */
    public function progressionTotalsForOwner(int $ownerId): array
    {
        $totals = $this->db->table('study_sessions')
            ->select('COALESCE(SUM(duration_minutes), 0) AS total_minutes', false)
            ->select('COUNT(*) AS session_count', false)
            ->select('COUNT(DISTINCT DATE(completed_at)) AS study_days', false)
            ->where('user_id', $ownerId)
            ->get()->getRowArray() ?? [];

        return [
            'total_minutes' => (int) ($totals['total_minutes'] ?? 0),
            'session_count' => (int) ($totals['session_count'] ?? 0),
            'study_days' => (int) ($totals['study_days'] ?? 0),
        ];
    }

    /** @return array<string, mixed>|null */
    public function findForOwner(int $sessionId, int $ownerId): ?array
    {
        $session = $this->ownerQuery($ownerId)
            ->where('study_sessions.id', $sessionId)
            ->get()->getRowArray();

        return is_array($session) ? $session : null;
    }

    public function createForOwner(int $ownerId, int $subjectId, ?int $topicId, int $durationMinutes, string $completedAt): int|false
    {
        if (! $this->referencesBelongToOwner($ownerId, $subjectId, $topicId)
            || $durationMinutes < 1 || $durationMinutes > 480) {
            return false;
        }

        return $this->insert([
            'user_id' => $ownerId,
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'duration_minutes' => $durationMinutes,
            'completed_at' => $completedAt,
        ], true);
    }

    public function updateForOwner(int $sessionId, int $ownerId, int $subjectId, ?int $topicId, int $durationMinutes): bool
    {
        if (! $this->findForOwner($sessionId, $ownerId)
            || ! $this->referencesBelongToOwner($ownerId, $subjectId, $topicId)
            || $durationMinutes < 1 || $durationMinutes > 480) {
            return false;
        }

        return $this->builder()
            ->where('id', $sessionId)
            ->where('user_id', $ownerId)
            ->update([
                'subject_id' => $subjectId,
                'topic_id' => $topicId,
                'duration_minutes' => $durationMinutes,
                'updated_at' => (new DateTimeImmutable('now', new DateTimeZone(config('App')->appTimezone)))->format('Y-m-d H:i:s'),
            ]);
    }

    public function deleteForOwner(int $sessionId, int $ownerId): bool
    {
        return $this->where('id', $sessionId)
            ->where('user_id', $ownerId)
            ->delete();
    }

    private function referencesBelongToOwner(int $ownerId, int $subjectId, ?int $topicId): bool
    {
        if ((new SubjectModel())->findForOwner($subjectId, $ownerId) === null) {
            return false;
        }

        return $topicId === null
            || (new TopicModel())->findForSubjectOwner($subjectId, $topicId, $ownerId) !== null;
    }

    private function ownerQuery(int $ownerId): BaseBuilder
    {
        return $this->db->table('study_sessions')
            ->select('study_sessions.*, subjects.name AS subject_name, topics.title AS topic_title')
            ->join('subjects', 'subjects.id = study_sessions.subject_id')
            ->join('topics', 'topics.id = study_sessions.topic_id', 'left')
            ->where('study_sessions.user_id', $ownerId);
    }
}
