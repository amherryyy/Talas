<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudyPlanModel;
use App\Models\TopicModel;
use App\Services\DailyPlanService;
use App\Services\TopicPriorityService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use DateTimeZone;

class Plans extends BaseController
{
    public function show(): string
    {
        return view('student/plans/show', [
            'plan' => (new StudyPlanModel())->findForOwnerDate($this->ownerId(), $this->today()),
            'today' => $this->today(),
            'errors' => session()->getFlashdata('errors') ?? [],
            'availableMinutes' => old('available_minutes', ''),
            'confirmReplacement' => false,
        ]);
    }

    public function generate(): string|RedirectResponse
    {
        $rawMinutes = $this->request->getPost('available_minutes');
        $minutes = is_string($rawMinutes) && ctype_digit($rawMinutes) ? (int) $rawMinutes : 0;
        if ($minutes < DailyPlanService::MIN_AVAILABLE_MINUTES || $minutes > DailyPlanService::MAX_AVAILABLE_MINUTES) {
            return redirect()->to(site_url('student/plan'))->withInput()->with('errors', [
                'available_minutes' => 'Enter between 15 and 480 minutes.',
            ]);
        }

        $ownerId = $this->ownerId();
        $date = $this->today();
        $plans = new StudyPlanModel();
        $existing = $plans->findForOwnerDate($ownerId, $date);
        $confirmed = $this->request->getPost('confirm_replacement') === '1';
        if ($existing !== null && ! $confirmed) {
            return view('student/plans/show', [
                'plan' => $existing,
                'today' => $date,
                'errors' => [],
                'availableMinutes' => $minutes,
                'confirmReplacement' => true,
            ]);
        }

        $ranked = (new TopicPriorityService())->rank((new TopicModel())->findAllForOwner($ownerId), $date);
        $items = (new DailyPlanService())->buildItems($ranked, $minutes);
        if (! $plans->replaceForOwnerDate($ownerId, $date, $minutes, $items)) {
            return redirect()->to(site_url('student/plan'))->with('error', 'The plan could not be saved. Please try again.');
        }

        return redirect()->to(site_url('student/plan'))->with('success', 'Today’s plan is ready. You can adjust it below.');
    }

    public function updateDuration(int $itemId): RedirectResponse
    {
        $rawMinutes = $this->request->getPost('duration_minutes');
        $minutes = is_string($rawMinutes) && ctype_digit($rawMinutes) ? (int) $rawMinutes : 0;
        $result = (new StudyPlanModel())->changeItemDurationForOwnerToday($this->ownerId(), $this->today(), $itemId, $minutes);
        $message = match ($result) {
            'updated' => ['success', 'Block duration updated.'],
            'over_capacity' => ['error', 'That change would exceed your available time.'],
            'missing' => ['error', 'That plan block is no longer available.'],
            'invalid' => ['error', 'Enter a block duration between 1 and 480 minutes.'],
            default => ['error', 'The block could not be updated. Please try again.'],
        };

        return redirect()->to(site_url('student/plan'))->with($message[0], $message[1]);
    }

    public function removeItem(int $itemId): RedirectResponse
    {
        $removed = (new StudyPlanModel())->removeItemForOwnerToday($this->ownerId(), $this->today(), $itemId);

        return redirect()->to(site_url('student/plan'))->with(
            $removed ? 'success' : 'error',
            $removed ? 'Plan block removed.' : 'That plan block is no longer available.',
        );
    }

    public function moveItem(int $itemId, string $direction): RedirectResponse
    {
        $result = (new StudyPlanModel())->moveItemForOwnerToday($this->ownerId(), $this->today(), $itemId, $direction);
        $message = match ($result) {
            'updated' => ['success', 'Plan order updated.'],
            'unchanged' => ['success', 'That block is already at the edge of the plan.'],
            'missing' => ['error', 'That plan block is no longer available.'],
            default => ['error', 'The plan order could not be updated.'],
        };

        return redirect()->to(site_url('student/plan'))->with($message[0], $message[1]);
    }

    private function ownerId(): int
    {
        $user = auth()->user();
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (int) $user->id;
    }

    private function today(): string
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);

        return (new DateTimeImmutable('now', $timezone))->format('Y-m-d');
    }
}
