<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CheckInModel;
use App\Models\StudySessionModel;
use App\Services\AralyticsService;
use App\Services\CheckInSummaryService;
use App\Services\StudyProgressionService;
use CodeIgniter\Exceptions\PageNotFoundException;
use DateTimeImmutable;
use DateTimeZone;

class Aralytics extends BaseController
{
    public function index(): string
    {
        $owner = auth()->user();
        if ($owner === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $requestedPeriod = $this->request->getGet('period');
        $period = is_string($requestedPeriod) && isset(AralyticsService::PERIODS[$requestedPeriod])
            ? $requestedPeriod
            : '7_days';
        $timezone = new DateTimeZone(config('App')->appTimezone);
        $now = new DateTimeImmutable('now', $timezone);
        $sessions = (new StudySessionModel())->findAllForOwner((int) $owner->id);
        $summary = (new AralyticsService())->summarize(
            $sessions,
            $period,
            $now,
            $timezone,
        );
        $checkInSummary = (new CheckInSummaryService())->summarize(
            (new CheckInModel())->findAllForOwner((int) $owner->id),
            $period,
            $now,
            $timezone,
        );

        return view('student/aralytics/index', [
            'summary' => $summary,
            'checkInSummary' => $checkInSummary,
            'periods' => AralyticsService::PERIODS,
            'progression' => (new StudyProgressionService())->summarize($sessions, $now, $timezone),
            'appTimezone' => $timezone->getName(),
            'pageTitle' => 'Aralytics',
        ]);
    }
}
