<?php

namespace App\Models;

use CodeIgniter\Model;

class StudyPlanItemModel extends Model
{
    protected $table = 'study_plan_items';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['plan_id', 'topic_id', 'topic_title', 'subject_name', 'duration_minutes', 'position'];
    protected $useTimestamps = true;

    /** @return list<array<string, mixed>> */
    public function findForOwnerPlan(int $planId, int $ownerId): array
    {
        return $this->db->table('study_plan_items')
            ->select('study_plan_items.*, topics.progress AS topic_progress')
            ->join('study_plans', 'study_plans.id = study_plan_items.plan_id')
            ->join('topics', 'topics.id = study_plan_items.topic_id', 'left')
            ->where('study_plan_items.plan_id', $planId)
            ->where('study_plans.user_id', $ownerId)
            ->orderBy('study_plan_items.position', 'ASC')
            ->get()->getResultArray();
    }
}
