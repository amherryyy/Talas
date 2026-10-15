<?php

namespace App\Services;

class DailyPlanService
{
    public const MIN_AVAILABLE_MINUTES = 15;
    public const MAX_AVAILABLE_MINUTES = 480;
    public const DEFAULT_BLOCK_MINUTES = 25;

    /** @param list<array<string, mixed>> $rankedTopics
     *  @return list<array{topic_id: int, topic_title: string, subject_name: string, duration_minutes: int, position: int}>
     */
    public function buildItems(array $rankedTopics, int $availableMinutes): array
    {
        if ($availableMinutes < self::MIN_AVAILABLE_MINUTES || $availableMinutes > self::MAX_AVAILABLE_MINUTES) {
            throw new \InvalidArgumentException('Available time must be between 15 and 480 minutes.');
        }

        $items = [];
        $remaining = $availableMinutes;
        foreach ($rankedTopics as $topic) {
            if ($remaining <= 0) {
                break;
            }
            if (($topic['progress'] ?? '') === 'done') {
                continue;
            }

            $duration = min(self::DEFAULT_BLOCK_MINUTES, $remaining);
            $items[] = [
                'topic_id' => (int) $topic['id'],
                'topic_title' => (string) $topic['title'],
                'subject_name' => (string) ($topic['subject_name'] ?? ''),
                'duration_minutes' => $duration,
                'position' => count($items) + 1,
            ];
            $remaining -= $duration;
        }

        return $items;
    }

    /** @param list<array<string, mixed>> $items */
    public function totalMinutes(array $items): int
    {
        return array_sum(array_map(static fn (array $item): int => (int) $item['duration_minutes'], $items));
    }
}
