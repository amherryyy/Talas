<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\CheckInModel;
use App\Models\StudySessionModel;
use App\Services\AralyticsService;
use App\Services\CheckInSummaryService;
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
        $summary = (new AralyticsService())->summarize(
            (new StudySessionModel())->findAllForOwner((int) $owner->id),
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
            'appTimezone' => $timezone->getName(),
            'pageTitle' => 'Aralytics',
        ]);
    }
}
