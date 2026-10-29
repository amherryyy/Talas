<?php

namespace App\Models;

use App\Services\DailyPlanService;
use CodeIgniter\Model;

class StudyPlanModel extends Model
{
    protected $table = 'study_plans';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'plan_date', 'available_minutes'];
    protected $useTimestamps = true;

    /** @return array<string, mixed>|null */
    public function findForOwnerDate(int $ownerId, string $date): ?array
    {
        $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
        if (! is_array($plan)) {
            return null;
        }
        $plan['items'] = (new StudyPlanItemModel())->findForOwnerPlan((int) $plan['id'], $ownerId);

        return $plan;
    }

    /** @return array<string, int> Plan-item counts keyed by YYYY-MM-DD. */
    public function itemCountsForOwnerRange(int $ownerId, string $startDate, string $endDate): array
    {
        $rows = $this->db->table('study_plans sp')
            ->select('sp.plan_date, COUNT(spi.plan_id) AS item_count')
            ->join('study_plan_items spi', 'spi.plan_id = sp.id', 'left')
            ->where('sp.user_id', $ownerId)
            ->where('sp.plan_date >=', $startDate)
            ->where('sp.plan_date <=', $endDate)
            ->groupBy('sp.plan_date')
            ->get()->getResultArray();

        $counts = [];
        foreach ($rows as $row) {
            $counts[(string) $row['plan_date']] = (int) $row['item_count'];
        }

        return $counts;
    }

    /** @param list<array<string, mixed>> $items */
    public function replaceForOwnerDate(int $ownerId, string $date, int $availableMinutes, array $items, ?string $startTime = null): bool
    {
        if ($availableMinutes < DailyPlanService::MIN_AVAILABLE_MINUTES
            || $availableMinutes > DailyPlanService::MAX_AVAILABLE_MINUTES) {
            return false;
        }
        $total = 0;
        $topicIds = [];
        foreach ($items as $index => $item) {
            $duration = (int) ($item['duration_minutes'] ?? 0);
            $topicId = (int) ($item['topic_id'] ?? 0);
            if ($duration < 1 || $duration > DailyPlanService::MAX_AVAILABLE_MINUTES
                || $topicId < 1 || (int) ($item['position'] ?? 0) !== $index + 1
                || in_array($topicId, $topicIds, true)) {
                return false;
            }
            $topicIds[] = $topicId;
            $total += $duration;
        }
        if ($total > $availableMinutes) {
            return false;
        }
        $startMinute = $startTime === null ? null : $this->timeToMinutes($startTime);
        if ($startTime !== null && ($startMinute === null || $total > 1440 - $startMinute)) {
            return false;
        }

        $db = $this->db;
        $db->transBegin();
        try {
            if ($topicIds !== []) {
                $ownedTopicIds = $db->table('topics')
                    ->select('topics.id')
                    ->join('subjects', 'subjects.id = topics.subject_id')
                    ->where('subjects.user_id', $ownerId)
                    ->whereIn('topics.id', $topicIds)
                    ->get()->getResultArray();
                $ownedTopicIds = array_map(static fn (array $topic): int => (int) $topic['id'], $ownedTopicIds);
                sort($ownedTopicIds);
                $expectedTopicIds = $topicIds;
                sort($expectedTopicIds);
                if ($ownedTopicIds !== $expectedTopicIds) {
                    $db->transRollback();
                    return false;
                }
            }

            $existing = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
            if (is_array($existing)) {
                $planId = (int) $existing['id'];
                $this->update($planId, ['available_minutes' => $availableMinutes]);
                $db->table('study_plan_items')->where('plan_id', $planId)->delete();
            } else {
                $planId = (int) $this->insert([
                    'user_id' => $ownerId,
                    'plan_date' => $date,
                    'available_minutes' => $availableMinutes,
                ], true);
            }

            $now = date('Y-m-d H:i:s');
            $cursor = $startMinute;
            foreach ($items as $item) {
                $db->table('study_plan_items')->insert([
                    'plan_id' => $planId,
                    'topic_id' => $item['topic_id'],
                    'topic_title' => $item['topic_title'],
                    'subject_name' => $item['subject_name'],
                    'duration_minutes' => $item['duration_minutes'],
                    'start_time' => $cursor === null ? null : $this->minutesToTime($cursor),
                    'position' => $item['position'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
                if ($cursor !== null) {
                    $cursor += (int) $item['duration_minutes'];
                }
            }
            $db->transCommit();

            return $db->transStatus();
        } catch (\Throwable $exception) {
            $db->transRollback();

            return false;
        }
    }

    /** @return array{status: string, item_id?: int} */
    public function addTopicBlockForOwnerDate(
        int $ownerId,
        string $date,
        int $topicId,
        int $duration,
        string $startTime,
        ?int $availableMinutes = null,
    ): array {
        $startMinute = $this->timeToMinutes($startTime);
        if ($topicId < 1 || $duration < 1 || $duration > DailyPlanService::MAX_AVAILABLE_MINUTES
            || $startMinute === null || $duration > 1440 - $startMinute) {
            return ['status' => 'invalid'];
        }

        $topic = $this->db->table('topics')
            ->select('topics.id, topics.title, topics.progress, subjects.name AS subject_name')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->where('topics.id', $topicId)
            ->where('subjects.user_id', $ownerId)
            ->get()->getRowArray();
        if (! is_array($topic) || $topic['progress'] === 'done') {
            return ['status' => 'topic_unavailable'];
        }

        $db = $this->db;
        $db->transBegin();
        try {
            $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
            if (! is_array($plan)) {
                if ($availableMinutes === null || $availableMinutes < DailyPlanService::MIN_AVAILABLE_MINUTES
                    || $availableMinutes > DailyPlanService::MAX_AVAILABLE_MINUTES) {
                    $db->transRollback();
                    return ['status' => 'available_time_required'];
                }
                $planId = (int) $this->insert([
                    'user_id' => $ownerId,
                    'plan_date' => $date,
                    'available_minutes' => $availableMinutes,
                ], true);
                $capacity = $availableMinutes;
            } else {
                $planId = (int) $plan['id'];
                $capacity = (int) $plan['available_minutes'];
                $availableMinutes ??= $capacity;
            }

            $existingItems = (new StudyPlanItemModel())->findForOwnerPlan($planId, $ownerId);
            foreach ($existingItems as $item) {
                if ((int) ($item['topic_id'] ?? 0) === $topicId) {
                    $db->transRollback();
                    return ['status' => 'duplicate'];
                }
            }
            $total = array_sum(array_map(static fn (array $item): int => (int) $item['duration_minutes'], $existingItems));
            if ($total + $duration > $capacity) {
                $db->transRollback();
                return ['status' => 'over_capacity'];
            }
            foreach ($existingItems as $item) {
                $otherStart = $this->timeToMinutes((string) ($item['start_time'] ?? ''));
                if ($otherStart === null) {
                    continue;
                }
                $otherEnd = $otherStart + (int) $item['duration_minutes'];
                if ($startMinute < $otherEnd && $otherStart < $startMinute + $duration) {
                    $db->transRollback();
                    return ['status' => 'overlap'];
                }
            }

            $position = $existingItems === [] ? 1 : max(array_map(static fn (array $item): int => (int) $item['position'], $existingItems)) + 1;
            $now = date('Y-m-d H:i:s');
            $db->table('study_plan_items')->insert([
                'plan_id' => $planId,
                'topic_id' => $topicId,
                'topic_title' => $topic['title'],
                'subject_name' => $topic['subject_name'],
                'duration_minutes' => $duration,
                'start_time' => $this->minutesToTime($startMinute),
                'position' => $position,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $itemId = (int) $db->insertID();
            $db->table('study_plans')->where('id', $planId)->update(['updated_at' => $now]);
            $db->transCommit();

            return $db->transStatus() ? ['status' => 'added', 'item_id' => $itemId] : ['status' => 'failed'];
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    /** @return array{status: string, item_id?: int} */
    public function scheduleExistingBlockForOwnerDate(int $ownerId, string $date, int $itemId, string $startTime): array
    {
        $startMinute = $this->timeToMinutes($startTime);
        if ($itemId < 1 || $startMinute === null) {
            return ['status' => 'invalid'];
        }
        $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
        if (! is_array($plan)) {
            return ['status' => 'missing'];
        }
        $planId = (int) $plan['id'];
        $items = (new StudyPlanItemModel())->findForOwnerPlan($planId, $ownerId);
        $target = null;
        foreach ($items as $item) {
            if ((int) $item['id'] === $itemId) {
                $target = $item;
                break;
            }
        }
        if ($target === null || $target['topic_id'] === null) {
            return ['status' => 'missing'];
        }
        if ($target['start_time'] !== null) {
            return ['status' => 'already_scheduled'];
        }
        $duration = (int) $target['duration_minutes'];
        if ($duration > 1440 - $startMinute) {
            return ['status' => 'invalid'];
        }
        foreach ($items as $item) {
            if ((int) $item['id'] === $itemId) {
                continue;
            }
            $otherStart = $this->timeToMinutes((string) ($item['start_time'] ?? ''));
            if ($otherStart !== null && $startMinute < $otherStart + (int) $item['duration_minutes']
                && $otherStart < $startMinute + $duration) {
                return ['status' => 'overlap'];
            }
        }

        $now = date('Y-m-d H:i:s');
        $updated = $this->db->table('study_plan_items')
            ->where('id', $itemId)
            ->where('plan_id', $planId)
            ->where('start_time', null)
            ->update(['start_time' => $this->minutesToTime($startMinute), 'updated_at' => $now]);
        if (! $updated) {
            return ['status' => 'failed'];
        }
        $this->db->table('study_plans')->where('id', $planId)->update(['updated_at' => $now]);

        return ['status' => 'scheduled', 'item_id' => $itemId];
    }

    public function updateItemTopicProgressForOwnerDate(int $ownerId, string $date, int $itemId, string $progress): string
    {
        if (! in_array($progress, ['in_progress', 'done'], true)) {
            return 'invalid';
        }

        $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
        if (! is_array($plan)) {
            return 'missing';
        }

        foreach ((new StudyPlanItemModel())->findForOwnerPlan((int) $plan['id'], $ownerId) as $item) {
            if ((int) $item['id'] !== $itemId) {
                continue;
            }
            if ($item['topic_id'] === null) {
                return 'missing';
            }

            return (new TopicModel())->updateProgressForOwner((int) $item['topic_id'], $ownerId, $progress)
                ? 'updated'
                : 'missing';
        }

        return 'missing';
    }

    public function changeItemDurationForOwnerToday(int $ownerId, string $date, int $itemId, int $duration): string
    {
        if ($duration < 1 || $duration > DailyPlanService::MAX_AVAILABLE_MINUTES) {
            return 'invalid';
        }

        $db = $this->db;
        $db->transBegin();
        try {
            $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
            if (! is_array($plan)) {
                $db->transRollback();
                return 'missing';
            }
            $planId = (int) $plan['id'];
            $db->table('study_plans')->where('id', $planId)->update(['updated_at' => date('Y-m-d H:i:s')]);
            $items = (new StudyPlanItemModel())->findForOwnerPlan($planId, $ownerId);
            $current = null;
            foreach ($items as $item) {
                if ((int) $item['id'] === $itemId) {
                    $current = $item;
                    break;
                }
            }
            if ($current === null) {
                $db->transRollback();
                return 'missing';
            }
            $total = array_sum(array_map(static fn (array $item): int => (int) $item['duration_minutes'], $items));
            if ($total - (int) $current['duration_minutes'] + $duration > (int) $plan['available_minutes']) {
                $db->transRollback();
                return 'over_capacity';
            }
            $currentStart = $this->timeToMinutes((string) ($current['start_time'] ?? ''));
            if ($currentStart !== null) {
                if ($duration > 1440 - $currentStart) {
                    $db->transRollback();
                    return 'invalid';
                }
                foreach ($items as $item) {
                    if ((int) $item['id'] === $itemId) {
                        continue;
                    }
                    $otherStart = $this->timeToMinutes((string) ($item['start_time'] ?? ''));
                    if ($otherStart !== null && $currentStart < $otherStart + (int) $item['duration_minutes']
                        && $otherStart < $currentStart + $duration) {
                        $db->transRollback();
                        return 'overlap';
                    }
                }
            }
            $db->table('study_plan_items')->where('id', $itemId)->where('plan_id', $planId)
                ->update(['duration_minutes' => $duration, 'updated_at' => date('Y-m-d H:i:s')]);
            $db->transCommit();

            return $db->transStatus() ? 'updated' : 'failed';
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }

    public function removeItemForOwnerToday(int $ownerId, string $date, int $itemId): bool
    {
        $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
        if (! is_array($plan)) {
            return false;
        }
        $planId = (int) $plan['id'];
        $exists = $this->db->table('study_plan_items')->where('id', $itemId)->where('plan_id', $planId)->countAllResults() > 0;
        if (! $exists) {
            return false;
        }
        $removed = $this->db->table('study_plan_items')->where('id', $itemId)->where('plan_id', $planId)->delete();
        if ($removed) {
            $this->renumberItems($planId);
        }

        return (bool) $removed;
    }

    public function moveItemForOwnerToday(int $ownerId, string $date, int $itemId, string $direction): string
    {
        if (! in_array($direction, ['up', 'down'], true)) {
            return 'invalid';
        }
        $plan = $this->where('user_id', $ownerId)->where('plan_date', $date)->first();
        if (! is_array($plan)) {
            return 'missing';
        }
        $planId = (int) $plan['id'];
        $items = (new StudyPlanItemModel())->findForOwnerPlan($planId, $ownerId);
        $index = null;
        foreach ($items as $i => $item) {
            if ((int) $item['id'] === $itemId) {
                $index = $i;
                break;
            }
        }
        if ($index === null) {
            return 'missing';
        }
        $otherIndex = $direction === 'up' ? $index - 1 : $index + 1;
        if (! isset($items[$otherIndex])) {
            return 'unchanged';
        }

        $db = $this->db;
        $db->transBegin();
        $db->table('study_plan_items')->where('plan_id', $planId)->where('id', $items[$index]['id'])->update(['position' => 0]);
        $db->table('study_plan_items')->where('plan_id', $planId)->where('id', $items[$otherIndex]['id'])->update(['position' => $items[$index]['position']]);
        $db->table('study_plan_items')->where('plan_id', $planId)->where('id', $items[$index]['id'])->update(['position' => $items[$otherIndex]['position']]);
        $db->transCommit();

        return $db->transStatus() ? 'updated' : 'failed';
    }

    private function renumberItems(int $planId): void
    {
        $items = $this->db->table('study_plan_items')->where('plan_id', $planId)->orderBy('position', 'ASC')->get()->getResultArray();
        foreach ($items as $index => $item) {
            $this->db->table('study_plan_items')->where('id', $item['id'])->update(['position' => $index + 1]);
        }
    }

    private function timeToMinutes(string $time): ?int
    {
        if (! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/D', $time)) {
            return null;
        }
        [$hour, $minute] = array_map('intval', explode(':', $time));

        return $hour * 60 + $minute;
    }

    private function minutesToTime(int $minutes): string
    {
        return sprintf('%02d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    }
}
