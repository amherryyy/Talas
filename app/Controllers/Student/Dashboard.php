<?php

namespace App\Controllers\Student;

use App\Content\StudySuggestions;
use App\Controllers\BaseController;
use App\Models\CheckInModel;
use App\Models\StudySessionModel;
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
        $sessions = (new StudySessionModel())->findAllForOwner($ownerId);

        return view('student/dashboard', [
            'pageTitle' => 'Duno workspace',
            'suggestions' => StudySuggestions::ITEMS,
            'suggestionSourceUrl' => StudySuggestions::SOURCE_URL,
            'suggestionSourceName' => StudySuggestions::SOURCE_NAME,
            'checkInPromptDate' => $todayDate,
            'checkInPromptUserKey' => (string) auth()->id(),
            'checkInPromptSatisfied' => $hasCheckInToday || session()->getFlashdata('check_in_prompt_completed') === true,
            'checkInErrors' => session()->getFlashdata('check_in_errors') ?? [],
            'progression' => (new StudyProgressionService())->summarize($sessions, $today, $timezone),
        ]);
    }
}
