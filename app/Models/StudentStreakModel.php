<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class StudentStreakModel extends Model
{
    protected $table = 'student_streak_days';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'activity_date', 'streak_days', 'xp_awarded', 'created_at'];
    protected $useTimestamps = false;

    /** @return array<string, mixed>|null */
    public function findForOwnerOnDate(int $ownerId, string $date): ?array
    {
        $row = $this->ownerQuery($ownerId)->where('activity_date', $date)->get()->getRowArray();

        return is_array($row) ? $row : null;
    }

    /** @return array<string, mixed>|null */
    public function findLatestBeforeDate(int $ownerId, string $date): ?array
    {
        $row = $this->ownerQuery($ownerId)
            ->where('activity_date <', $date)
            ->orderBy('activity_date', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        return is_array($row) ? $row : null;
    }

    public function insertActivityDay(int $ownerId, string $date, int $streakDays, int $xpAwarded, string $createdAt): bool
    {
        $this->db->table($this->table)->ignore(true)->insert([
            'user_id' => $ownerId,
            'activity_date' => $date,
            'streak_days' => $streakDays,
            'xp_awarded' => $xpAwarded,
            'created_at' => $createdAt,
        ]);

        return $this->db->affectedRows() > 0;
    }

    public function totalXpForOwner(int $ownerId): int
    {
        $row = $this->ownerQuery($ownerId)
            ->select('COALESCE(SUM(xp_awarded), 0) AS streak_xp', false)
            ->get()->getRowArray();

        return (int) ($row['streak_xp'] ?? 0);
    }

    /** @return array{streak_xp: int, current_streak: int} */
    public function progressionTotalsForOwner(int $ownerId, string $today, string $yesterday): array
    {
        $totals = $this->ownerQuery($ownerId)
            ->select('COALESCE(SUM(xp_awarded), 0) AS streak_xp', false)
            ->get()->getRowArray() ?? [];
        $latest = $this->ownerQuery($ownerId)
            ->orderBy('activity_date', 'DESC')
            ->limit(1)
            ->get()->getRowArray();
        $currentStreak = 0;
        if (is_array($latest) && in_array($latest['activity_date'], [$today, $yesterday], true)) {
            $currentStreak = (int) $latest['streak_days'];
        }

        return [
            'streak_xp' => (int) ($totals['streak_xp'] ?? 0),
            'current_streak' => $currentStreak,
        ];
    }

    private function ownerQuery(int $ownerId): BaseBuilder
    {
        return $this->db->table($this->table)->where('user_id', $ownerId);
    }
}
