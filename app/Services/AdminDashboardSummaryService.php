<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Auth;
use DateTimeImmutable;
use DateTimeZone;

class AdminDashboardSummaryService
{
    private const MINIMUM_COHORT = 5;
    private const WEEK_COUNT = 8;
    private const MONTH_COUNT = 6;

    /**
     * Return system-wide, owner-free aggregates. Accounts that also belong to
     * the admin group are excluded from every student metric.
     *
     * @return array{
     *   student_count: int,
     *   subject_count: int,
     *   topic_count: int,
     *   saved_session_count: int,
     *   total_study_minutes: int,
     *   average_session_minutes: ?float,
     *   session_duration_stats: ?array{median: float, q1: float, q3: float},
     *   saved_plan_count: int,
     *   recorded_visits_30d: ?int,
     *   onboarding_completion_rate: ?float,
     *   weekly_visits: list<array{label: string, value: ?int}>,
     *   weekly_activity: array{visits: list<array{label: string, value: ?int}>, active_students: list<array{label: string, value: ?int}>},
     *   topic_status_snapshot: list<array{key: string, label: string, percent: ?float}>,
     *   workspace_setup_coverage: list<array{key: string, label: string, student_count: ?int, percent: ?float}>,
     *   planning_patterns: array{planner_count: ?int, coverage_percent: ?float, median_plan_days: ?float, median_planned_minutes: ?float, median_blocks_per_day: ?float, median_capacity_percent: ?float},
     *   upcoming_workload: list<array{key: string, label: string, percent: ?float}>,
     *   monthly_registrations: list<array{label: string, value: ?int}>
     * }
     */
    public function summarize(): array
    {
        $db = db_connect();
        $authTables = config(Auth::class)->tables;
        $usersTable = $authTables['users'];
        $groupsUsersTable = $authTables['groups_users'];
        $studentIds = fn (): BaseBuilder => $this->studentIds($db, $groupsUsersTable);
        $studentCount = (int) $db->table($usersTable)
            ->whereIn('id', $studentIds())
            ->countAllResults();
        $subjectCount = (int) $db->table('subjects')
            ->whereIn('user_id', $studentIds())
            ->countAllResults();
        $topicCount = (int) $db->table('topics')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->whereIn('subjects.user_id', $studentIds())
            ->countAllResults();

        $sessionTotals = $db->table('study_sessions')
            ->selectCount('id', 'saved_session_count')
            ->selectSum('duration_minutes', 'total_study_minutes')
            ->whereIn('user_id', $studentIds())
            ->get()->getRowArray() ?? [];
        $savedSessionCount = (int) ($sessionTotals['saved_session_count'] ?? 0);
        $totalStudyMinutes = (int) ($sessionTotals['total_study_minutes'] ?? 0);

        $savedPlanCount = (int) $db->table('study_plans')
            ->whereIn('user_id', $studentIds())
            ->countAllResults();

        $today = new DateTimeImmutable('today', new DateTimeZone(config('App')->appTimezone));
        $visits = $this->visitRows($db, $studentIds(), $today->modify('-29 days'), $today);
        $activeStudents30d = count(array_unique(array_column($visits, 'user_id')));
        $recordedVisits30d = $activeStudents30d >= self::MINIMUM_COHORT ? count($visits) : null;
        $weeklyActivity = $this->weeklyActivity($db, $studentIds(), $today);
        $workspaceSetupCoverage = $this->workspaceSetupCoverage($db, $studentIds(), $studentCount);
        $planningPatterns = $this->planningPatterns($db, $studentIds(), $studentCount, $today);

        return [
            'student_count' => $studentCount,
            'subject_count' => $subjectCount,
            'topic_count' => $topicCount,
            'saved_session_count' => $savedSessionCount,
            'total_study_minutes' => $totalStudyMinutes,
            'average_session_minutes' => $savedSessionCount > 0
                ? round($totalStudyMinutes / $savedSessionCount, 1)
                : null,
            'session_duration_stats' => $this->sessionDurationStats($db, $studentIds()),
            'saved_plan_count' => $savedPlanCount,
            'recorded_visits_30d' => $recordedVisits30d,
            'onboarding_completion_rate' => $this->onboardingCompletionRate($db, $usersTable, $studentIds(), $studentCount),
            'weekly_visits' => $weeklyActivity['visits'],
            'weekly_activity' => $weeklyActivity,
            'topic_status_snapshot' => $this->topicStatusSnapshot($db, $studentIds()),
            'workspace_setup_coverage' => $workspaceSetupCoverage,
            'planning_patterns' => $planningPatterns,
            'upcoming_workload' => $this->upcomingWorkload($db, $studentIds(), $today),
            'monthly_registrations' => $this->monthlyRegistrations($db, $usersTable, $studentIds(), $today),
        ];
    }

    private function studentIds(BaseConnection $db, string $groupsUsersTable): BaseBuilder
    {
        $adminIds = $db->table($groupsUsersTable)
            ->select('user_id')
            ->where('group', 'admin');

        return $db->table($groupsUsersTable)
            ->select('user_id')
            ->where('group', 'student')
            ->whereNotIn('user_id', $adminIds)
            ->distinct();
    }

    /** @return list<array{user_id: string, activity_date: string}> */
    private function visitRows(BaseConnection $db, BaseBuilder $studentIds, DateTimeImmutable $start, DateTimeImmutable $end): array
    {
        return $db->table('student_streak_days')
            ->select('user_id, activity_date')
            ->whereIn('user_id', $studentIds)
            ->where('activity_date >=', $start->format('Y-m-d'))
            ->where('activity_date <=', $end->format('Y-m-d'))
            ->orderBy('activity_date', 'ASC')
            ->get()->getResultArray();
    }

    /** @return array{visits: list<array{label: string, value: ?int}>, active_students: list<array{label: string, value: ?int}>} */
    private function weeklyActivity(BaseConnection $db, BaseBuilder $studentIds, DateTimeImmutable $today): array
    {
        $currentWeek = $today->modify('monday this week');
        $start = $currentWeek->modify('-' . self::WEEK_COUNT . ' weeks');
        $end = $currentWeek->modify('-1 day');
        $rows = $this->visitRows($db, $studentIds, $start, $end);
        $buckets = array_fill(0, self::WEEK_COUNT, ['visits' => 0, 'users' => []]);

        foreach ($rows as $row) {
            $activityDate = new DateTimeImmutable($row['activity_date'], new DateTimeZone(config('App')->appTimezone));
            $daysSinceStart = (int) $start->diff($activityDate)->format('%a');
            $bucket = intdiv($daysSinceStart, 7);
            if ($bucket < 0 || $bucket >= self::WEEK_COUNT) {
                continue;
            }
            $buckets[$bucket]['visits']++;
            $buckets[$bucket]['users'][(string) $row['user_id']] = true;
        }

        $visitSeries = [];
        $activeStudentSeries = [];
        for ($index = 0; $index < self::WEEK_COUNT; $index++) {
            $weekStart = $start->modify('+' . $index . ' weeks');
            $activeStudentCount = count($buckets[$index]['users']);
            $enoughStudents = $activeStudentCount >= self::MINIMUM_COHORT;
            $visitSeries[] = [
                'label' => $weekStart->format('M j'),
                'value' => $enoughStudents ? $buckets[$index]['visits'] : null,
            ];
            $activeStudentSeries[] = [
                'label' => $weekStart->format('M j'),
                'value' => $enoughStudents ? $activeStudentCount : null,
            ];
        }

        return ['visits' => $visitSeries, 'active_students' => $activeStudentSeries];
    }

    /** @return array{median: float, q1: float, q3: float}|null */
    private function sessionDurationStats(BaseConnection $db, BaseBuilder $studentIds): ?array
    {
        $rows = $db->table('study_sessions')
            ->select('user_id, duration_minutes')
            ->whereIn('user_id', $studentIds)
            ->get()->getResultArray();
        $contributingStudents = array_unique(array_column($rows, 'user_id'));

        if (count($contributingStudents) < self::MINIMUM_COHORT) {
            return null;
        }

        $durations = array_map(static fn (array $row): int => (int) $row['duration_minutes'], $rows);
        sort($durations, SORT_NUMERIC);

        return [
            'median' => $this->percentile($durations, 0.5),
            'q1' => $this->percentile($durations, 0.25),
            'q3' => $this->percentile($durations, 0.75),
        ];
    }

    /** @param list<int|float> $values */
    private function percentile(array $values, float $fraction): float
    {
        sort($values, SORT_NUMERIC);
        $position = (count($values) - 1) * $fraction;
        $lowerIndex = (int) floor($position);
        $upperIndex = (int) ceil($position);
        $weight = $position - $lowerIndex;

        return round($values[$lowerIndex] * (1 - $weight) + $values[$upperIndex] * $weight, 1);
    }

    /** @return list<array{key: string, label: string, percent: ?float}> */
    private function topicStatusSnapshot(BaseConnection $db, BaseBuilder $studentIds): array
    {
        $statuses = [
            'not_started' => 'Not started',
            'in_progress' => 'In progress',
            'done' => 'Done',
        ];
        $rows = $db->table('topics')
            ->select('subjects.user_id, topics.progress')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->whereIn('subjects.user_id', $studentIds)
            ->get()->getResultArray();
        $studentsWithTopics = [];
        $studentsByStatus = array_fill_keys(array_keys($statuses), []);

        foreach ($rows as $row) {
            $userId = (string) $row['user_id'];
            $progress = (string) $row['progress'];
            $studentsWithTopics[$userId] = true;
            if (isset($studentsByStatus[$progress])) {
                $studentsByStatus[$progress][$userId] = true;
            }
        }

        $cohortSize = count($studentsWithTopics);
        $cohortIsLargeEnough = $cohortSize >= self::MINIMUM_COHORT;
        $snapshot = [];

        foreach ($statuses as $key => $label) {
            $contributorCount = count($studentsByStatus[$key]);
            $isLargeEnough = $contributorCount >= self::MINIMUM_COHORT;
            $snapshot[] = [
                'key' => $key,
                'label' => $label,
                'percent' => $cohortIsLargeEnough && $isLargeEnough
                    ? round(($contributorCount / $cohortSize) * 100, 1)
                    : null,
            ];
        }

        return $snapshot;
    }

    /** @return list<array{key: string, label: string, student_count: ?int, percent: ?float}> */
    private function workspaceSetupCoverage(BaseConnection $db, BaseBuilder $studentIds, int $studentCount): array
    {
        $subjects = $db->table('subjects')
            ->select('user_id')
            ->whereIn('user_id', $studentIds)
            ->distinct()->get()->getResultArray();
        $topics = $db->table('topics')
            ->select('subjects.user_id')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->whereIn('subjects.user_id', $studentIds)
            ->distinct()->get()->getResultArray();
        $plans = $db->table('study_plans')
            ->select('user_id')
            ->whereIn('user_id', $studentIds)
            ->distinct()->get()->getResultArray();
        $sessions = $db->table('study_sessions')
            ->select('user_id')
            ->whereIn('user_id', $studentIds)
            ->distinct()->get()->getResultArray();

        $milestones = [
            'subjects' => ['label' => 'Has a subject', 'owners' => array_column($subjects, 'user_id')],
            'topics' => ['label' => 'Has a topic', 'owners' => array_column($topics, 'user_id')],
            'plans' => ['label' => 'Has a saved plan', 'owners' => array_column($plans, 'user_id')],
            'sessions' => ['label' => 'Has a saved session', 'owners' => array_column($sessions, 'user_id')],
        ];
        $coverage = [];

        foreach ($milestones as $key => $milestone) {
            $contributors = count(array_unique(array_map('strval', $milestone['owners'])));
            $visible = $contributors >= self::MINIMUM_COHORT
                && ($studentCount - $contributors) >= self::MINIMUM_COHORT;
            $coverage[] = [
                'key' => $key,
                'label' => $milestone['label'],
                'student_count' => $visible ? $contributors : null,
                'percent' => $visible && $studentCount > 0
                    ? round(($contributors / $studentCount) * 100, 1)
                    : null,
            ];
        }

        return $coverage;
    }

    /** @return array{planner_count: ?int, coverage_percent: ?float, median_plan_days: ?float, median_planned_minutes: ?float, median_blocks_per_day: ?float, median_capacity_percent: ?float} */
    private function planningPatterns(BaseConnection $db, BaseBuilder $studentIds, int $studentCount, DateTimeImmutable $today): array
    {
        $start = $today->modify('-29 days');
        $plans = $db->table('study_plans')
            ->select('id, user_id, available_minutes')
            ->whereIn('user_id', $studentIds)
            ->where('plan_date >=', $start->format('Y-m-d'))
            ->where('plan_date <=', $today->format('Y-m-d'))
            ->orderBy('user_id', 'ASC')
            ->get()->getResultArray();
        $plannerIds = array_unique(array_map('strval', array_column($plans, 'user_id')));
        $visible = count($plannerIds) >= self::MINIMUM_COHORT
            && ($studentCount - count($plannerIds)) >= self::MINIMUM_COHORT;

        if (! $visible) {
            return [
                'planner_count' => null,
                'coverage_percent' => null,
                'median_plan_days' => null,
                'median_planned_minutes' => null,
                'median_blocks_per_day' => null,
                'median_capacity_percent' => null,
            ];
        }

        $itemsByPlan = [];
        $planIds = array_column($plans, 'id');
        if ($planIds !== []) {
            $items = $db->table('study_plan_items')
                ->select('plan_id, SUM(duration_minutes) AS planned_minutes, COUNT(id) AS block_count')
                ->whereIn('plan_id', $planIds)
                ->groupBy('plan_id')
                ->get()->getResultArray();
            foreach ($items as $item) {
                $itemsByPlan[(string) $item['plan_id']] = [
                    'minutes' => (int) $item['planned_minutes'],
                    'blocks' => (int) $item['block_count'],
                ];
            }
        }

        $byStudent = [];
        foreach ($plans as $plan) {
            $userId = (string) $plan['user_id'];
            $items = $itemsByPlan[(string) $plan['id']] ?? ['minutes' => 0, 'blocks' => 0];
            $availableMinutes = (int) $plan['available_minutes'];
            $byStudent[$userId] ??= ['days' => 0, 'planned' => 0, 'blocks' => 0, 'available' => 0];
            $byStudent[$userId]['days']++;
            $byStudent[$userId]['planned'] += $items['minutes'];
            $byStudent[$userId]['blocks'] += $items['blocks'];
            $byStudent[$userId]['available'] += $availableMinutes;
        }

        $planDays = [];
        $plannedMinutesPerDay = [];
        $blocksPerDay = [];
        $capacityPercent = [];
        foreach ($byStudent as $studentPlans) {
            $planDays[] = $studentPlans['days'];
            $plannedMinutesPerDay[] = $studentPlans['planned'] / $studentPlans['days'];
            $blocksPerDay[] = $studentPlans['blocks'] / $studentPlans['days'];
            if ($studentPlans['available'] > 0) {
                $capacityPercent[] = ($studentPlans['planned'] / $studentPlans['available']) * 100;
            }
        }

        return [
            'planner_count' => count($plannerIds),
            'coverage_percent' => $studentCount > 0 ? round((count($plannerIds) / $studentCount) * 100, 1) : null,
            'median_plan_days' => $this->percentile($array_map('floatval', $planDays), 0.5),
            'median_planned_minutes' => $this->percentile($plannedMinutesPerDay, 0.5),
            'median_blocks_per_day' => $this->percentile($blocksPerDay, 0.5),
            'median_capacity_percent' => $capacityPercent === []
                ? null
                : $this->percentile($capacityPercent, 0.5),
        ];
    }

    /** @return list<array{key: string, label: string, percent: ?float}> */
    private function upcomingWorkload(BaseConnection $db, BaseBuilder $studentIds, DateTimeImmutable $today): array
    {
        $buckets = [
            'overdue' => ['label' => 'Overdue', 'users' => []],
            'due_soon' => ['label' => 'Due within 7 days', 'users' => []],
            'later' => ['label' => 'Due later', 'users' => []],
            'undated' => ['label' => 'No due date', 'users' => []],
        ];
        $rows = $db->table('topics')
            ->select('subjects.user_id, topics.due_date')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->whereIn('subjects.user_id', $studentIds)
            ->where('topics.progress !=', 'done')
            ->get()->getResultArray();
        $studentsWithUnfinishedTopics = [];
        $soonEnd = $today->modify('+6 days')->format('Y-m-d');
        $todayDate = $today->format('Y-m-d');

        foreach ($rows as $row) {
            $userId = (string) $row['user_id'];
            $studentsWithUnfinishedTopics[$userId] = true;
            $dueDate = $row['due_date'];
            if (! is_string($dueDate) || $dueDate === '') {
                $key = 'undated';
            } elseif ($dueDate < $todayDate) {
                $key = 'overdue';
            } elseif ($dueDate <= $soonEnd) {
                $key = 'due_soon';
            } else {
                $key = 'later';
            }
            $buckets[$key]['users'][$userId] = true;
        }

        $cohortSize = count($studentsWithUnfinishedTopics);
        foreach ($buckets as &$bucket) {
            $contributors = count($bucket['users']);
            $bucket['percent'] = $cohortSize >= self::MINIMUM_COHORT
                && $contributors >= self::MINIMUM_COHORT
                && ($cohortSize - $contributors) >= self::MINIMUM_COHORT
                    ? round(($contributors / $cohortSize) * 100, 1)
                    : null;
        }
        unset($bucket);

        $snapshot = [];
        foreach ($buckets as $key => $bucket) {
            $snapshot[] = ['key' => $key, 'label' => $bucket['label'], 'percent' => $bucket['percent']];
        }

        return $snapshot;
    }

    /** @return list<array{label: string, value: ?int}> */
    private function monthlyRegistrations(BaseConnection $db, string $usersTable, BaseBuilder $studentIds, DateTimeImmutable $today): array
    {
        $currentMonth = $today->modify('first day of this month')->setTime(0, 0);
        $start = $currentMonth->modify('-' . (self::MONTH_COUNT - 1) . ' months');
        $end = $today->setTime(23, 59, 59);
        $rows = $db->table($usersTable)
            ->select('created_at')
            ->whereIn('id', $studentIds)
            ->where('created_at >=', $start->format('Y-m-d H:i:s'))
            ->where('created_at <=', $end->format('Y-m-d H:i:s'))
            ->get()->getResultArray();
        $counts = array_fill(0, self::MONTH_COUNT, 0);

        foreach ($rows as $row) {
            if (! is_string($row['created_at'] ?? null) || $row['created_at'] === '') {
                continue;
            }
            $createdAt = new DateTimeImmutable($row['created_at'], new DateTimeZone(config('App')->appTimezone));
            $monthOffset = ((int) $createdAt->format('Y') - (int) $start->format('Y')) * 12
                + (int) $createdAt->format('n') - (int) $start->format('n');
            if ($monthOffset >= 0 && $monthOffset < self::MONTH_COUNT) {
                $counts[$monthOffset]++;
            }
        }

        $series = [];
        for ($index = 0; $index < self::MONTH_COUNT; $index++) {
            $month = $start->modify('+' . $index . ' months');
            $series[] = [
                'label' => $month->format('M'),
                'value' => $counts[$index] === 0 || $counts[$index] >= self::MINIMUM_COHORT
                    ? $counts[$index]
                    : null,
            ];
        }

        return $series;
    }

    private function onboardingCompletionRate(BaseConnection $db, string $usersTable, BaseBuilder $studentIds, int $studentCount): ?float
    {
        if ($studentCount < self::MINIMUM_COHORT || ! $db->fieldExists('academic_program_selected_at', $usersTable)) {
            return null;
        }

        $selected = (int) $db->table($usersTable)
            ->whereIn('id', $studentIds)
            ->where('academic_program_selected_at IS NOT NULL', null, false)
            ->countAllResults();
        $pending = $studentCount - $selected;
        if ($selected < self::MINIMUM_COHORT || $pending < self::MINIMUM_COHORT) {
            return null;
        }

        return round(($selected / $studentCount) * 100, 1);
    }
}
