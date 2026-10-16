<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudySessionModel;
use App\Services\AralyticsService;
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
        $summary = (new AralyticsService())->summarize(
            (new StudySessionModel())->findAllForOwner((int) $owner->id),
            $period,
            new DateTimeImmutable('now', $timezone),
            $timezone,
        );

        return view('student/aralytics/index', [
            'summary' => $summary,
            'periods' => AralyticsService::PERIODS,
            'appTimezone' => $timezone->getName(),
            'pageTitle' => 'Aralytics',
        ]);
    }
}
