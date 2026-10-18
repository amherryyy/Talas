<?php

namespace App\Services;

use DateTimeImmutable;
use DateTimeZone;

class CheckInSummaryService
{
    /**
     * Summarize owner-scoped check-ins for an Aralytics period using application-local dates.
     *
     * @param list<array<string, mixed>> $checkIns
     * @return array{start_date: string|null, end_date: string, period_label: string, check_in_count: int, high_workload_count: int, low_energy_count: int, high_workload_low_energy_count: int, workload_distribution: array<int, int>, energy_distribution: array<int, int>}
     */
    public function summarize(array $checkIns, string $period, DateTimeImmutable $now, DateTimeZone $timezone): array
    {
        if (! isset(AralyticsService::PERIODS[$period])) {
            $period = '7_days';
        }

        $today = $now->setTimezone($timezone)->setTime(0, 0);
        $start = match ($period) {
            'today' => $today,
            '7_days' => $today->modify('-6 days'),
            '30_days' => $today->modify('-29 days'),
            default => null,
        };
        $end = $today->modify('+1 day');
        $recent = [];

        foreach ($checkIns as $checkIn) {
            $recordedAt = new DateTimeImmutable((string) $checkIn['recorded_at'], $timezone);
            if (($start !== null && $recordedAt < $start) || $recordedAt >= $end) {
                continue;
            }

            $recent[] = $checkIn;
        }

        $highWorkloadLowEnergyCount = 0;
        $highWorkloadCount = 0;
        $lowEnergyCount = 0;
        $workloadDistribution = array_fill(1, 5, 0);
        $energyDistribution = array_fill(1, 5, 0);
        foreach ($recent as $checkIn) {
            $workloadRating = (int) $checkIn['workload_rating'];
            $energyRating = (int) $checkIn['energy_rating'];
            $workloadDistribution[$workloadRating]++;
            $energyDistribution[$energyRating]++;
            $highWorkload = $workloadRating >= 4;
            $lowEnergy = $energyRating <= 2;
            if ($highWorkload) {
                $highWorkloadCount++;
            }
            if ($lowEnergy) {
                $lowEnergyCount++;
            }
            if ($highWorkload && $lowEnergy) {
                $highWorkloadLowEnergyCount++;
            }
        }

        return [
            'start_date' => $start?->format('Y-m-d'),
            'end_date' => $today->format('Y-m-d'),
            'period_label' => AralyticsService::PERIODS[$period],
            'check_in_count' => count($recent),
            'high_workload_count' => $highWorkloadCount,
            'low_energy_count' => $lowEnergyCount,
            'high_workload_low_energy_count' => $highWorkloadLowEnergyCount,
            'workload_distribution' => $workloadDistribution,
            'energy_distribution' => $energyDistribution,
        ];
    }
}
