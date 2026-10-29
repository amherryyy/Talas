<?php

namespace App\Controllers\Student;

use App\Content\StudySuggestions;
use App\Controllers\BaseController;
use App\Models\CheckInModel;
use App\Models\StudySessionModel;
use App\Models\StudentStreakModel;
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
