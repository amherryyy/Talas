<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class StudyProgressionService
{
    /** @var list<array{level: int, name: string, xp: int}> */
    public const LEVELS = [
        ['level' => 1, 'name' => 'Fresh Start', 'xp' => 0],
        ['level' => 2, 'name' => 'Finding Your Rhythm', 'xp' => 120],
        ['level' => 3, 'name' => 'Building Momentum', 'xp' => 300],
        ['level' => 4, 'name' => 'Steady Scholar', 'xp' => 600],
        ['level' => 5, 'name' => 'Long-Haul Learner', 'xp' => 1200],
    ];

    /** @var list<array{key: string, name: string, description: string, level: int, xp: int}> */
    public const UNLOCKS = [
        ['key' => 'rhythm', 'name' => '30-day study rhythm', 'description' => 'See which days you made time to study over the past month.', 'level' => 2, 'xp' => 120],
        ['key' => 'weekdays', 'name' => 'Weekday patterns', 'description' => 'Compare saved study minutes across the days of the week.', 'level' => 3, 'xp' => 300],
        ['key' => 'momentum', 'name' => 'Monthly momentum', 'description' => 'Compare your recent 30 days with the 30 days before them.', 'level' => 4, 'xp' => 600],
        ['key' => 'six_months', 'name' => 'Six-month study trend', 'description' => 'See how your saved study time has changed month by month.', 'level' => 5, 'xp' => 1200],
    ];

    /**
     * Summarize progression and deterministic activity insights from owner-scoped saved sessions.
     *
     * @param list<array<string, mixed>> $sessions Sessions in the six-month display window.
     * @param array{total_minutes: int, session_count: int, study_days: int, streak_xp?: int, current_streak?: int}|null $lifetimeTotals
     * @return array<string, mixed>
     */
    public function summarize(array $sessions, DateTimeImmutable $now, DateTimeZone $timezone, ?array $lifetimeTotals = null): array
    {
        $today = $now->setTimezone($timezone)->setTime(0, 0);
        $currentStart = $today->modify('-29 days');
        $previousStart = $today->modify('-59 days');
        $tomorrow = $today->modify('+1 day');
        $streakXp = $lifetimeTotals['streak_xp'] ?? 0;
        $totalStudyMinutes = $lifetimeTotals['total_minutes'] ?? 0;
        $xp = $totalStudyMinutes + $streakXp;
        $studyDates = [];
        $dailyMinutes = [];
        $weekdayMinutes = array_fill(1, 7, 0);
        $currentThirtyDayMinutes = 0;
        $previousThirtyDayMinutes = 0;
        $monthStart = $today->modify('first day of this month')->setTime(0, 0);
        $sixMonthStart = $monthStart->modify('-5 months');
        $monthlyMinutes = [];

        for ($offset = 29; $offset >= 0; $offset--) {
            $date = $today->modify('-' . $offset . ' days')->format('Y-m-d');
            $dailyMinutes[$date] = 0;
        }
        for ($offset = 5; $offset >= 0; $offset--) {
            $month = $monthStart->modify('-' . $offset . ' months')->format('Y-m');
            $monthlyMinutes[$month] = 0;
        }

        foreach ($sessions as $session) {
            $minutes = max(0, (int) $session['duration_minutes']);
            $completedAt = new DateTimeImmutable((string) $session['completed_at'], $timezone);
            $localCompletedAt = $completedAt->setTimezone($timezone);
            $date = $localCompletedAt->format('Y-m-d');
            $month = $localCompletedAt->format('Y-m');
            if ($lifetimeTotals === null) {
                $xp += $minutes;
                $totalStudyMinutes += $minutes;
            }
            $studyDates[$date] = true;

            if ($localCompletedAt >= $currentStart && $localCompletedAt < $tomorrow) {
                $dailyMinutes[$date] = ($dailyMinutes[$date] ?? 0) + $minutes;
                $weekday = (int) $localCompletedAt->format('N');
                $weekdayMinutes[$weekday] += $minutes;
                $currentThirtyDayMinutes += $minutes;
            } elseif ($localCompletedAt >= $previousStart && $localCompletedAt < $currentStart) {
                $previousThirtyDayMinutes += $minutes;
            }
            if ($localCompletedAt >= $sixMonthStart && $localCompletedAt < $tomorrow) {
                $monthlyMinutes[$month] = ($monthlyMinutes[$month] ?? 0) + $minutes;
            }
        }

        $currentLevel = $this->levelForXp($xp);
        $levelIndex = $currentLevel['level'] - 1;
        $nextLevel = self::LEVELS[$levelIndex + 1] ?? null;
        $levelStartXp = $currentLevel['xp'];
        $nextLevelXp = $nextLevel['xp'] ?? $levelStartXp;
        $unlocks = [];
        $nextUnlock = null;
        foreach (self::UNLOCKS as $unlock) {
            $unlock['unlocked'] = $currentLevel['level'] >= $unlock['level'];
            $unlocks[] = $unlock;
            if (! $unlock['unlocked'] && $nextUnlock === null) {
                $nextUnlock = $unlock;
            }
        }

        $badges = [
            ['key' => 'first-session', 'name' => 'First Session', 'description' => 'Saved your first study session.', 'unlocked' => ($lifetimeTotals['session_count'] ?? count($sessions)) >= 1],
            ['key' => 'two-hours', 'name' => 'Two Hours', 'description' => 'Saved 120 minutes of study time.', 'unlocked' => $totalStudyMinutes >= 120],
            ['key' => 'five-study-days', 'name' => 'Five Study Days', 'description' => 'Studied on five different days.', 'unlocked' => ($lifetimeTotals['study_days'] ?? count($studyDates)) >= 5],
            ['key' => 'ten-hours', 'name' => 'Ten Hours', 'description' => 'Saved 600 minutes of study time.', 'unlocked' => $totalStudyMinutes >= 600],
            ['key' => 'long-haul', 'name' => 'Long Haul', 'description' => 'Saved 1,200 minutes of study time.', 'unlocked' => $totalStudyMinutes >= 1200],
        ];

        $weekdayNames = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
        $weekdaySummary = [];
        foreach ($weekdayNames as $weekday => $name) {
            $weekdaySummary[] = ['day' => $name, 'minutes' => $weekdayMinutes[$weekday]];
        }

        $dailySummary = [];
        foreach ($dailyMinutes as $date => $minutes) {
            $dailySummary[] = ['date' => $date, 'minutes' => $minutes];
        }
        $monthlySummary = [];
        foreach ($monthlyMinutes as $month => $minutes) {
            $monthlySummary[] = ['month' => $month, 'minutes' => $minutes];
        }

        return [
            'xp' => $xp,
            'study_xp' => $totalStudyMinutes,
            'streak_xp' => $streakXp,
            'current_streak' => $lifetimeTotals['current_streak'] ?? 0,
            'level' => $currentLevel['level'],
            'level_name' => $currentLevel['name'],
            'level_start_xp' => $levelStartXp,
            'next_level_name' => $nextLevel['name'] ?? null,
            'next_level_xp' => $nextLevelXp,
            'level_progress_xp' => $nextLevel === null ? 0 : max(0, $xp - $levelStartXp),
            'level_progress_max' => $nextLevel === null ? 0 : $nextLevelXp - $levelStartXp,
            'level_progress_percent' => $nextLevel === null
                ? 100
                : (int) round((($xp - $levelStartXp) / ($nextLevelXp - $levelStartXp)) * 100),
            'unlocks' => $unlocks,
            'next_unlock' => $nextUnlock,
            'badges' => $badges,
            'study_days_all_time' => $lifetimeTotals['study_days'] ?? count($studyDates),
            'study_days_last_30' => count(array_filter($dailyMinutes, static fn (int $minutes): bool => $minutes > 0)),
            'daily_minutes_last_30' => $dailySummary,
            'weekday_minutes_last_30' => $weekdaySummary,
            'monthly_minutes_last_6' => $monthlySummary,
            'current_30_day_minutes' => $currentThirtyDayMinutes,
            'previous_30_day_minutes' => $previousThirtyDayMinutes,
            '30_day_minutes_change' => $currentThirtyDayMinutes - $previousThirtyDayMinutes,
            'previous_30_day_start' => $previousStart->format('Y-m-d'),
            'previous_30_day_end' => $currentStart->modify('-1 day')->format('Y-m-d'),
            'current_30_day_start' => $currentStart->format('Y-m-d'),
            'current_30_day_end' => $today->format('Y-m-d'),
            'period_end' => $today->format('Y-m-d'),
        ];
    }

    /** @return array{level: int, name: string, xp: int} */
    public function levelForXp(int $xp): array
    {
        $currentLevel = self::LEVELS[0];
        foreach (self::LEVELS as $level) {
            if ($xp < $level['xp']) {
                break;
            }
            $currentLevel = $level;
        }

        return $currentLevel;
    }
}
