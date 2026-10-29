<?php

namespace App\Filters;

use App\Models\StudySessionModel;
use App\Services\StudentStreakService;
use App\Services\StudyProgressionService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use DateTimeImmutable;
use DateTimeZone;

class StudentStreakFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = auth()->user();
        if (strtoupper($request->getMethod()) !== 'GET' || $user === null || ! $user->inGroup('student') || $user->inGroup('admin')) {
            return null;
        }

        $timezone = new DateTimeZone(config('App')->appTimezone);
        $now = new DateTimeImmutable('now', $timezone);
        $result = (new StudentStreakService())->recordVisit((int) $user->id, $now, $timezone);
        if (! $result['awarded']) {
            session()->remove('daily_streak_reward');

            return null;
        }

        session()->setFlashdata('daily_streak_reward', [
            'xp' => $result['xp_awarded'],
            'streak_days' => $result['streak_days'],
        ]);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Streak activity is recorded before the requested student page is rendered.
    }
}
