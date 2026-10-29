<?php

namespace App\Services;

use App\Models\StudentStreakModel;
use DateTimeImmutable;
use DateTimeZone;

class StudentStreakService
{
    public const DAILY_XP = 500;

    /** @return array{awarded: bool, activity_date: string, streak_days: int, xp_awarded: int} */
    public function recordVisit(int $ownerId, DateTimeImmutable $now, DateTimeZone $timezone): array
    {
        $localNow = $now->setTimezone($timezone);
        $today = $localNow->format('Y-m-d');
        $model = new StudentStreakModel();
        $existing = $model->findForOwnerOnDate($ownerId, $today);

        if ($existing !== null) {
            return $this->result(false, $existing);
        }

        $previous = $model->findLatestBeforeDate($ownerId, $today);
        $yesterday = $localNow->modify('-1 day')->format('Y-m-d');
        $streakDays = self::nextStreakCount(
            $previous['activity_date'] ?? null,
            (int) ($previous['streak_days'] ?? 0),
            $today,
            $yesterday,
        );

        $inserted = $model->insertActivityDay(
            $ownerId,
            $today,
            $streakDays,
            self::DAILY_XP,
            $localNow->format('Y-m-d H:i:s'),
        );

        // The unique owner/date key makes simultaneous requests award XP only once.
        $recorded = $model->findForOwnerOnDate($ownerId, $today);
        if ($recorded === null) {
            throw new \RuntimeException('The student streak activity could not be recorded.');
        }

        return $this->result($inserted, $recorded);
    }

    public static function nextStreakCount(?string $previousDate, int $previousStreak, string $today, ?string $yesterday = null): int
    {
        $yesterday ??= (new DateTimeImmutable($today))->modify('-1 day')->format('Y-m-d');

        return $previousDate === $yesterday ? $previousStreak + 1 : 1;
    }

    /** @param array<string, mixed> $row
     *  @return array{awarded: bool, activity_date: string, streak_days: int, xp_awarded: int}
     */
    private function result(bool $awarded, array $row): array
    {
        return [
            'awarded' => $awarded,
            'activity_date' => (string) $row['activity_date'],
            'streak_days' => (int) $row['streak_days'],
            'xp_awarded' => (int) $row['xp_awarded'],
        ];
    }
}
