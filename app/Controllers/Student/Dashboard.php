<?php

namespace App\Controllers\Student;

use App\Content\StudySuggestions;
use App\Controllers\BaseController;
use App\Models\CheckInModel;
use App\Models\SubjectModel;
use App\Models\StudySessionModel;
use App\Models\StudentStreakModel;
use App\Models\TopicModel;
use App\Services\AcademicProgramCatalog;
use App\Services\StudyProgressionService;
use DateTimeImmutable;
use DateTimeZone;

class Dashboard extends BaseController
{
    public function index(): string
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);
        $today = new DateTimeImmutable('now', $timezone);
        $todayDate = $today->format('Y-m-d');
        $ownerId = (int) auth()->id();
        $academicProgram = (new AcademicProgramCatalog())->forUser($ownerId);
        $curriculumId = $academicProgram['curriculum_id'] ?? null;
        $subjects = (new SubjectModel())->findAllForAcademicProgram($ownerId, $curriculumId);
        $subjectIds = array_fill_keys(array_map(static fn (array $subject): int => (int) $subject['id'], $subjects), true);
        $guideTopics = array_values(array_filter(
            (new TopicModel())->findAllForOwner($ownerId),
            static fn (array $topic): bool => isset($subjectIds[(int) $topic['subject_id']]),
        ));
        $topicCounts = [];
        $topicPreviews = [];
        foreach ($guideTopics as $topic) {
            $subjectId = (int) $topic['subject_id'];
            $topicCounts[$subjectId] = ($topicCounts[$subjectId] ?? 0) + 1;
            if (count($topicPreviews[$subjectId] ?? []) < 2) {
                $topicPreviews[$subjectId][] = (string) $topic['title'];
            }
        }
        $dashboardSubjects = array_map(
            static fn (array $subject): array => $subject + [
                'topic_count' => $topicCounts[(int) $subject['id']] ?? 0,
                'topic_preview' => $topicPreviews[(int) $subject['id']] ?? [],
            ],
            array_slice($subjects, 0, 4),
        );
        $hasCheckInToday = (new CheckInModel())->hasRecordForOwnerOnDate($ownerId, $todayDate);
        $todayStart = $today->setTime(0, 0);
        $sessionModel = new StudySessionModel();
        $lifetimeTotals = $sessionModel->progressionTotalsForOwner($ownerId);
        $streakTotals = (new StudentStreakModel())->progressionTotalsForOwner(
            $ownerId,
            $today->format('Y-m-d'),
            $today->modify('-1 day')->format('Y-m-d'),
        );
        $recentSessions = $sessionModel->findForOwnerPeriod(
            $ownerId,
            $todayStart->modify('first day of this month')->modify('-5 months')->format('Y-m-d H:i:s'),
            $todayStart->modify('+1 day')->format('Y-m-d H:i:s'),
        );

        return view('student/dashboard', [
            'pageTitle' => 'Duno workspace',
            'suggestions' => StudySuggestions::ITEMS,
            'guideSubjects' => $subjects,
            'guideTopics' => $guideTopics,
            'dashboardSubjects' => $dashboardSubjects,
            'subjectCount' => count($subjects),
            'suggestionSourceUrl' => StudySuggestions::SOURCE_URL,
            'suggestionSourceName' => StudySuggestions::SOURCE_NAME,
            'checkInPromptDate' => $todayDate,
            'checkInPromptUserKey' => (string) auth()->id(),
            'checkInPromptSatisfied' => $hasCheckInToday || session()->getFlashdata('check_in_prompt_completed') === true,
            'checkInErrors' => session()->getFlashdata('check_in_errors') ?? [],
            'progression' => (new StudyProgressionService())->summarize(
                $recentSessions,
                $today,
                $timezone,
                array_merge($lifetimeTotals, $streakTotals),
            ),
        ]);
    }
}
