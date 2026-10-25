<?php

namespace App\Models;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;
use DateTimeImmutable;
use DateTimeZone;

class CheckInModel extends Model
{
    protected $table = 'check_ins';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'workload_rating', 'energy_rating', 'recorded_at'];
    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function findAllForOwner(int $ownerId): array
    {
        return $this->ownerQuery($ownerId)
            ->orderBy('check_ins.recorded_at', 'DESC')
            ->orderBy('check_ins.id', 'DESC')
            ->get()->getResultArray();
    }

    /** @return list<array<string, mixed>> */
    public function findForOwnerPeriod(int $ownerId, ?string $start, ?string $end): array
    {
        $builder = $this->ownerQuery($ownerId);
        if ($start !== null) {
            $builder->where('check_ins.recorded_at >=', $start);
        }
        if ($end !== null) {
            $builder->where('check_ins.recorded_at <', $end);
        }

        return $builder
            ->orderBy('check_ins.recorded_at', 'DESC')
            ->orderBy('check_ins.id', 'DESC')
            ->get()->getResultArray();
    }

    public function hasRecordForOwnerOnDate(int $ownerId, string $date): bool
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);
        $start = new DateTimeImmutable($date . ' 00:00:00', $timezone);
        $end = $start->modify('+1 day');

        return $this->ownerQuery($ownerId)
            ->where('check_ins.recorded_at >=', $start->format('Y-m-d H:i:s'))
            ->where('check_ins.recorded_at <', $end->format('Y-m-d H:i:s'))
            ->countAllResults() > 0;
    }

    /** @return array<string, mixed>|null */
    public function findForOwner(int $checkInId, int $ownerId): ?array
    {
        $checkIn = $this->ownerQuery($ownerId)
            ->where('check_ins.id', $checkInId)
            ->get()->getRowArray();

        return is_array($checkIn) ? $checkIn : null;
    }

    public function createForOwner(int $ownerId, int $workloadRating, int $energyRating, string $recordedAt): int|false
    {
        if ($ownerId < 1 || ! $this->validRating($workloadRating) || ! $this->validRating($energyRating)) {
            return false;
        }

        return $this->insert([
            'user_id' => $ownerId,
            'workload_rating' => $workloadRating,
            'energy_rating' => $energyRating,
            'recorded_at' => $recordedAt,
        ], true);
    }

    public function updateForOwner(int $checkInId, int $ownerId, int $workloadRating, int $energyRating): bool
    {
        if ($this->findForOwner($checkInId, $ownerId) === null
            || ! $this->validRating($workloadRating) || ! $this->validRating($energyRating)) {
            return false;
        }

        return $this->builder()
            ->where('id', $checkInId)
            ->where('user_id', $ownerId)
            ->update([
                'workload_rating' => $workloadRating,
                'energy_rating' => $energyRating,
                'updated_at' => (new DateTimeImmutable('now', new DateTimeZone(config('App')->appTimezone)))->format('Y-m-d H:i:s'),
            ]);
    }

    public function deleteForOwner(int $checkInId, int $ownerId): bool
    {
        return $this->where('id', $checkInId)
            ->where('user_id', $ownerId)
            ->delete();
    }

    private function validRating(int $rating): bool
    {
        return $rating >= 1 && $rating <= 5;
    }

    private function ownerQuery(int $ownerId): BaseBuilder
    {
        return $this->db->table('check_ins')
            ->select('check_ins.*')
            ->where('check_ins.user_id', $ownerId);
    }
}
