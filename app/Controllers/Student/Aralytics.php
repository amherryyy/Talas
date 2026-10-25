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
        $today = $now->setTime(0, 0);
        $tomorrow = $today->modify('+1 day');
        $periodStart = match ($period) {
            'today' => $today,
            '7_days' => $today->modify('-6 days'),
            '30_days' => $today->modify('-29 days'),
            default => null,
        };
        $sessionModel = new StudySessionModel();
        $ownerId = (int) $owner->id;
        $lifetimeTotals = $sessionModel->progressionTotalsForOwner($ownerId);
        $progressionStart = $today->modify('first day of this month')->modify('-5 months');
        $sessions = $period === 'all_time'
            ? $sessionModel->findAllForOwner($ownerId)
            : $sessionModel->findForOwnerPeriod(
                $ownerId,
                $progressionStart->format('Y-m-d H:i:s'),
                $tomorrow->format('Y-m-d H:i:s'),
            );
        $checkIns = (new CheckInModel())->findForOwnerPeriod(
            $ownerId,
            $periodStart?->format('Y-m-d H:i:s'),
            $tomorrow->format('Y-m-d H:i:s'),
        );
        $summary = (new AralyticsService())->summarize(
            $sessions,
            $period,
            $now,
            $timezone,
        );
        $checkInSummary = (new CheckInSummaryService())->summarize(
            $checkIns,
            $period,
            $now,
            $timezone,
        );

        return view('student/aralytics/index', [
            'summary' => $summary,
            'checkInSummary' => $checkInSummary,
            'periods' => AralyticsService::PERIODS,
            'progression' => (new StudyProgressionService())->summarize(
                $sessions,
                $now,
                $timezone,
                $lifetimeTotals,
            ),
            'appTimezone' => $timezone->getName(),
            'pageTitle' => 'Aralytics',
        ]);
    }
}
