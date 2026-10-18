<?php

namespace App\Services;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Auth;

class AdminDashboardSummaryService
{
    /**
     * Return aggregate counts for student accounts and their study records.
     * Accounts that also belong to the admin group are excluded, matching the
     * exclusive admin/student route boundary.
     *
     * @return array{student_count: int, subject_count: int, topic_count: int, saved_session_count: int, total_study_minutes: int}
     */
    public function summarize(): array
    {
        $db = db_connect();
        $authTables = config(Auth::class)->tables;
        $usersTable = $authTables['users'];
        $groupsUsersTable = $authTables['groups_users'];
        $studentIds = fn (): BaseBuilder => $this->studentIds($db, $groupsUsersTable);

        $studentCount = $db->table($usersTable)
            ->whereIn('id', $studentIds())
            ->countAllResults();

        $subjectCount = $db->table('subjects')
            ->whereIn('user_id', $studentIds())
            ->countAllResults();

        $topicCount = $db->table('topics')
            ->join('subjects', 'subjects.id = topics.subject_id')
            ->whereIn('subjects.user_id', $studentIds())
            ->countAllResults();

        $sessionTotals = $db->table('study_sessions')
            ->selectCount('id', 'saved_session_count')
            ->selectSum('duration_minutes', 'total_study_minutes')
            ->whereIn('user_id', $studentIds())
            ->get()->getRowArray();

        return [
            'student_count' => (int) $studentCount,
            'subject_count' => (int) $subjectCount,
            'topic_count' => (int) $topicCount,
            'saved_session_count' => (int) ($sessionTotals['saved_session_count'] ?? 0),
            'total_study_minutes' => (int) ($sessionTotals['total_study_minutes'] ?? 0),
        ];
    }

    private function studentIds(BaseConnection $db, string $groupsUsersTable): BaseBuilder
    {
        $adminIds = $db->table($groupsUsersTable)
            ->select('user_id')
            ->where('group', 'admin');

        return $db->table($groupsUsersTable . ' AS student_groups')
            ->select('student_groups.user_id')
            ->where('student_groups.group', 'student')
            ->whereNotIn('student_groups.user_id', $adminIds)
            ->distinct();
    }
}
