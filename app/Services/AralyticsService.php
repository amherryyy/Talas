<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class AralyticsService
{
    /** @var array<string, string> */
    public const PERIODS = [
        'today' => 'Today',
        '7_days' => 'Last 7 days',
        '30_days' => 'Last 30 days',
        'all_time' => 'All time',
    ];

    /**
     * @param list<array<string, mixed>> $sessions Owner-scoped session rows with subject/topic labels.
     * @return array{period: string, period_label: string, start_date: string|null, end_date: string, sessions: list<array<string, mixed>>, total_minutes: int, session_count: int, average_minutes: float|null, subject_totals: list<array{subject_id: int, subject_name: string, total_minutes: int, session_count: int}>, topic_totals: list<array{subject_id: int, subject_name: string, topic_id: int|null, topic_title: string, total_minutes: int, session_count: int}>, activity_granularity: 'day'|'month', activity_series: list<array{key: string, label: string, minutes: int, session_count: int}>}
     */
    public function summarize(array $sessions, string $period, DateTimeImmutable $now, DateTimeZone $timezone): array
    {
        if (! isset(self::PERIODS[$period])) {
            $period = '7_days';
        }

        $today = $now->setTimezone($timezone)->setTime(0, 0);
        $start = match ($period) {
            'today' => $today,
            '7_days' => $today->modify('-6 days'),
            '30_days' => $today->modify('-29 days'),
            default => null,
        };

        $selected = [];
        foreach ($sessions as $session) {
            $completedAt = new DateTimeImmutable((string) $session['completed_at'], $timezone);
            if ($start !== null && ($completedAt < $start || $completedAt >= $today->modify('+1 day'))) {
                continue;
            }
            $selected[] = $session;
        }

        $subjectTotals = [];
        $topicTotals = [];
        $activityTotals = [];
        $totalMinutes = 0;
        foreach ($selected as $session) {
            $completedAt = new DateTimeImmutable((string) $session['completed_at'], $timezone);
            $activityKey = $period === 'all_time' ? $completedAt->format('Y-m') : $completedAt->format('Y-m-d');
            $activityTotals[$activityKey] ??= ['minutes' => 0, 'session_count' => 0];
            $activityTotals[$activityKey]['minutes'] += (int) $session['duration_minutes'];
            $activityTotals[$activityKey]['session_count']++;

            $subjectId = (int) $session['subject_id'];
            $subjectName = (string) $session['subject_name'];
            $topicId = $session['topic_id'] === null ? null : (int) $session['topic_id'];
            $topicTitle = $session['topic_title'] === null ? 'No topic' : (string) $session['topic_title'];
            $minutes = (int) $session['duration_minutes'];

            $subjectTotals[$subjectId] ??= [
                'subject_id' => $subjectId,
                'subject_name' => $subjectName,
                'total_minutes' => 0,
                'session_count' => 0,
            ];
            $subjectTotals[$subjectId]['total_minutes'] += $minutes;
            $subjectTotals[$subjectId]['session_count']++;

            $topicKey = $subjectId . ':' . ($topicId ?? 'none');
            $topicTotals[$topicKey] ??= [
                'subject_id' => $subjectId,
                'subject_name' => $subjectName,
                'topic_id' => $topicId,
                'topic_title' => $topicTitle,
                'total_minutes' => 0,
                'session_count' => 0,
            ];
            $topicTotals[$topicKey]['total_minutes'] += $minutes;
            $topicTotals[$topicKey]['session_count']++;
            $totalMinutes += $minutes;
        }

        $activityGranularity = $period === 'all_time' ? 'month' : 'day';
        $activitySeries = [];
        if ($period !== 'all_time' || $selected !== []) {
            $activityStart = $start;
            if ($period === 'all_time') {
                $firstMonth = min(array_keys($activityTotals));
                $activityStart = DateTimeImmutable::createFromFormat('!Y-m', $firstMonth, $timezone) ?: $today;
                $activityStart = $activityStart->modify('first day of this month');
            }
            $activityEnd = $period === 'all_time' ? $today->modify('first day of this month') : $today;
            $step = $activityGranularity === 'month' ? '+1 month' : '+1 day';
            for ($bucketDate = $activityStart; $bucketDate <= $activityEnd; $bucketDate = $bucketDate->modify($step)) {
                $key = $activityGranularity === 'month' ? $bucketDate->format('Y-m') : $bucketDate->format('Y-m-d');
                $activitySeries[] = [
                    'key' => $key,
                    'label' => match ($period) {
                        'today' => 'Today',
                        '7_days' => $bucketDate->format('D'),
                        '30_days' => $bucketDate->format('M j'),
                        default => $bucketDate->format('M y'),
                    },
                    'minutes' => $activityTotals[$key]['minutes'] ?? 0,
                    'session_count' => $activityTotals[$key]['session_count'] ?? 0,
                ];
            }
        }

        usort($subjectTotals, static fn (array $left, array $right): int => $right['total_minutes'] <=> $left['total_minutes'] ?: strcasecmp($left['subject_name'], $right['subject_name']));
        usort($topicTotals, static fn (array $left, array $right): int => $right['total_minutes'] <=> $left['total_minutes'] ?: strcasecmp($left['subject_name'] . $left['topic_title'], $right['subject_name'] . $right['topic_title']));

        return [
            'period' => $period,
            'period_label' => self::PERIODS[$period],
            'start_date' => $start?->format('Y-m-d'),
            'end_date' => $today->format('Y-m-d'),
            'sessions' => $selected,
            'total_minutes' => $totalMinutes,
            'session_count' => count($selected),
            'average_minutes' => $selected === [] ? null : round($totalMinutes / count($selected), 1),
            'subject_totals' => array_values($subjectTotals),
            'topic_totals' => array_values($topicTotals),
            'activity_granularity' => $activityGranularity,
            'activity_series' => $activitySeries,
        ];
    }
}
