<?php

namespace App\Services;

use DateTimeImmutable;

class TopicPriorityService
{
    /** @param list<array<string, mixed>> $topics
     *  @return list<array<string, mixed>>
     */
    public function rank(array $topics, ?string $today = null): array
    {
        $today ??= date('Y-m-d');
        $todayDate = new DateTimeImmutable($today);
        $ranked = [];

        foreach ($topics as $topic) {
            if (($topic['progress'] ?? '') === 'done') {
                continue;
            }

            $importance = (string) ($topic['importance'] ?? '');
            $importancePoints = ['low' => 1, 'medium' => 2, 'high' => 3][$importance] ?? 0;
            $importanceLabel = ucfirst($importance !== '' ? $importance : 'Unknown');

            $dueDate = $topic['due_date'] ?? null;
            $duePoints = 0;
            if (is_string($dueDate) && $dueDate !== '') {
                $due = new DateTimeImmutable($dueDate);
                $daysUntilDue = (int) $todayDate->diff($due)->format('%r%a');
                if ($daysUntilDue <= 0) {
                    $duePoints = 3;
                    $dueReason = $daysUntilDue < 0 ? 'Overdue (+3)' : 'Due today (+3)';
                } elseif ($daysUntilDue <= 7) {
                    $duePoints = 2;
                    $dueReason = 'Due within 7 days (+2)';
                } else {
                    $duePoints = 1;
                    $dueReason = 'Due later (+1)';
                }
            } else {
                $dueReason = 'No due date (+0)';
            }

            $progress = (string) ($topic['progress'] ?? '');
            $progressPoints = $progress === 'not_started' ? 1 : 0;
            $progressReason = match ($progress) {
                'not_started' => 'Not started (+1)',
                'in_progress' => 'In progress (+0)',
                default => 'Progress unavailable (+0)',
            };

            $topic['priority_score'] = $importancePoints + $duePoints + $progressPoints;
            $topic['priority_reasons'] = [
                'Importance: ' . $importanceLabel . ' (+' . $importancePoints . ')',
                $dueReason,
                $progressReason,
            ];
            $ranked[] = $topic;
        }

        usort($ranked, static function (array $left, array $right): int {
            $scoreOrder = (int) $right['priority_score'] <=> (int) $left['priority_score'];
            if ($scoreOrder !== 0) {
                return $scoreOrder;
            }

            $leftHasDue = is_string($left['due_date'] ?? null) && $left['due_date'] !== '';
            $rightHasDue = is_string($right['due_date'] ?? null) && $right['due_date'] !== '';
            if ($leftHasDue !== $rightHasDue) {
                return $leftHasDue ? -1 : 1;
            }
            if ($leftHasDue) {
                $dueOrder = strcmp((string) $left['due_date'], (string) $right['due_date']);
                if ($dueOrder !== 0) {
                    return $dueOrder;
                }
            }

            $titleOrder = strcasecmp((string) ($left['title'] ?? ''), (string) ($right['title'] ?? ''));
            if ($titleOrder !== 0) {
                return $titleOrder;
            }

            return (int) ($left['id'] ?? 0) <=> (int) ($right['id'] ?? 0);
        });

        return $ranked;
    }
}
