<?php

namespace App\Models;

use CodeIgniter\Model;

class StudyPlanItemModel extends Model
{
    protected $table = 'study_plan_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'plan_id', 'topic_id', 'topic_title', 'subject_name', 'duration_minutes', 'start_time', 'position'];
    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function findForOwnerPlan(int $planId, int $ownerId): array
    {
        return $this->db->table('study_plan_items')
            ->select('study_plan_items.*, topics.progress AS topic_progress')
            ->join('study_plans', 'study_plans.id = study_plan_items.plan_id')
            ->join('topics', 'topics.id = study_plan_items.topic_id', 'left')
            ->join('subjects AS topic_subjects', 'topic_subjects.id = topics.subject_id', 'left')
            ->where('study_plan_items.plan_id', $planId)
            ->where('study_plan_items.user_id', $ownerId)
            ->where('study_plans.user_id', $ownerId)
            ->groupStart()
                ->where('study_plan_items.topic_id', null)
                ->orGroupStart()
                    ->where('topics.user_id', $ownerId)
                    ->where('topic_subjects.user_id', $ownerId)
                ->groupEnd()
            ->groupEnd()
            ->orderBy('study_plan_items.position', 'ASC')
            ->get()->getResultArray();
    }
}
