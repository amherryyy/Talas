<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudyPlanModel;
use App\Models\TopicModel;
use App\Services\DailyPlanService;
use App\Services\TopicPriorityService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\Response;
use DateTimeImmutable;
use DateTimeZone;

class Plans extends BaseController
{
    public function show(): string
    {
        $ownerId = $this->ownerId();
        $today = $this->today();
        $selectedDate = $this->validDate($this->request->getGet('date')) ?? $today;
        $selectedDay = new DateTimeImmutable($selectedDate, new DateTimeZone(config('App')->appTimezone));
        $weekStart = $selectedDay->modify('monday this week');
        $weekEnd = $weekStart->modify('+6 days');
        $plans = new StudyPlanModel();
        $weekCounts = $plans->itemCountsForOwnerRange($ownerId, $weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d'));
        $agendaPlan = $plans->findForOwnerDate($ownerId, $selectedDate);
        $todayPlan = $selectedDate === $today ? $agendaPlan : $plans->findForOwnerDate($ownerId, $today);
        $plannedTopicIds = array_map(static fn (array $item): int => (int) ($item['topic_id'] ?? 0), $agendaPlan['items'] ?? []);
        $topicOptions = [];
        foreach ((new TopicModel())->findAllForOwner($ownerId) as $topic) {
            if ($topic['progress'] !== 'done' && ! in_array((int) $topic['id'], $plannedTopicIds, true)) {
                $topicOptions[] = [
                    'value' => 'topic-' . $topic['id'],
                    'label' => $topic['title'] . ' · ' . $topic['subject_name'],
                    'duration' => DailyPlanService::DEFAULT_BLOCK_MINUTES,
                ];
            }
        }
        foreach ($agendaPlan['items'] ?? [] as $item) {
            if ($item['topic_id'] !== null && $item['start_time'] === null) {
                $topicOptions[] = [
                    'value' => 'block-' . $item['id'],
                    'label' => $item['topic_title'] . ' · ' . $item['subject_name'] . ' (set a time)',
                    'duration' => (int) $item['duration_minutes'],
                ];
            }
        }
        $now = new DateTimeImmutable('now', new DateTimeZone(config('App')->appTimezone));
        $nowMinutes = ((int) $now->format('H') * 60) + (int) $now->format('i');
        $agendaMinute = $selectedDate === $today ? (int) (ceil(($nowMinutes + 5) / 15) * 15) : 9 * 60;
        if ($agendaMinute > 23 * 60 + 30) {
            $agendaMinute = 22 * 60;
        }
        $agendaDefaultTime = sprintf('%02d:%02d', intdiv($agendaMinute, 60), $agendaMinute % 60);
        $firstScheduledTime = null;
        foreach ($agendaPlan['items'] ?? [] as $item) {
            $scheduledTime = $item['start_time'] === null ? null : substr($item['start_time'], 0, 5);
            if ($scheduledTime !== null && ($firstScheduledTime === null || $scheduledTime < $firstScheduledTime)) {
                $firstScheduledTime = $scheduledTime;
            }
        }
        $weekDays = [];
        for ($offset = 0; $offset < 7; $offset++) {
            $day = $weekStart->modify('+' . $offset . ' days');
            $date = $day->format('Y-m-d');
            $weekDays[] = [
                'date' => $date,
                'weekday' => $day->format('D'),
                'day' => $day->format('j'),
                'selected' => $date === $selectedDate,
                'today' => $date === $today,
                'count' => $weekCounts[$date] ?? 0,
            ];
        }

        return view('student/plans/show', [
            'plan' => $todayPlan,
            'agendaPlan' => $agendaPlan,
            'today' => $today,
            'desktopPlanDate' => $today,
            'selectedDate' => $selectedDate,
            'selectedDateLabel' => $selectedDay->format('l, F j'),
            'weekLabel' => $weekStart->format('F Y') === $weekEnd->format('F Y') ? $weekStart->format('F Y') : $weekStart->format('M') . ' – ' . $weekEnd->format('M Y'),
            'previousWeekDate' => $weekStart->modify('-7 days')->format('Y-m-d'),
            'nextWeekDate' => $weekStart->modify('+7 days')->format('Y-m-d'),
            'weekDays' => $weekDays,
            'topicOptions' => $topicOptions,
            'nowTime' => $now->format('H:i'),
            'isSelectedToday' => $selectedDate === $today,
            'newAgendaItemId' => session()->getFlashdata('agenda_new_item_id'),
            'errors' => session()->getFlashdata('errors') ?? [],
            'availableMinutes' => old('available_minutes', ''),
            'startTime' => old('start_time', $firstScheduledTime ?? $agendaDefaultTime),
            'agendaDefaultTime' => $agendaDefaultTime,
            'confirmReplacement' => session()->getFlashdata('confirm_replacement') === true,
            'pageTitle' => 'Duno Plan',
        ]);
    }

    public function generate(): string|RedirectResponse
    {
        $rawMinutes = $this->request->getPost('available_minutes');
        $minutes = is_string($rawMinutes) && ctype_digit($rawMinutes) ? (int) $rawMinutes : 0;
        $date = $this->validDate($this->request->getPost('plan_date')) ?? $this->today();
        $rawStartTime = $this->request->getPost('start_time');
        $startTime = is_string($rawStartTime) && $rawStartTime !== '' ? $rawStartTime : null;
        if ($minutes < DailyPlanService::MIN_AVAILABLE_MINUTES || $minutes > DailyPlanService::MAX_AVAILABLE_MINUTES) {
            return redirect()->to($this->planUrl($date))->withInput()->with('errors', [
                'available_minutes' => 'Enter between 15 and 480 minutes.',
            ]);
        }
        if ($startTime !== null && ! preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/D', $startTime)) {
            return redirect()->to($this->planUrl($date))->withInput()->with('errors', ['start_time' => 'Choose a valid start time.']);
        }

        $ownerId = $this->ownerId();
        $plans = new StudyPlanModel();
        $existing = $plans->findForOwnerDate($ownerId, $date);
        $confirmed = $this->request->getPost('confirm_replacement') === '1';
        if ($existing !== null && ! $confirmed) {
            return redirect()->to($this->planUrl($date))->withInput()->with('confirm_replacement', true);
        }

        $ranked = (new TopicPriorityService())->rank((new TopicModel())->findAllForOwner($ownerId), $date);
        $items = (new DailyPlanService())->buildItems($ranked, $minutes);
        if (! $plans->replaceForOwnerDate($ownerId, $date, $minutes, $items, $startTime)) {
            return redirect()->to($this->planUrl($date))->with('error', 'The plan could not be saved. Check the start time and try again.');
        }

        return redirect()->to($this->planUrl($date))->with('success', 'The plan is ready. You can adjust it below.');
    }

    public function addBlock(): RedirectResponse
    {
        $date = $this->validDate($this->request->getPost('plan_date'));
        if ($date === null) {
            return redirect()->to(site_url('student/plan'))->with('error', 'Choose a valid day first.');
        }
        $rawAgendaItem = $this->request->getPost('agenda_item');
        $rawDuration = $this->request->getPost('duration_minutes');
        $rawStartTime = $this->request->getPost('start_time');
        $rawAvailable = $this->request->getPost('available_minutes');
        $duration = is_string($rawDuration) && ctype_digit($rawDuration) ? (int) $rawDuration : 0;
        $available = is_string($rawAvailable) && ctype_digit($rawAvailable) ? (int) $rawAvailable : null;
        $startTime = is_string($rawStartTime) ? $rawStartTime : '';

        $plans = new StudyPlanModel();
        if (is_string($rawAgendaItem) && preg_match('/^block-(\d+)$/D', $rawAgendaItem, $matches)) {
            $result = $plans->scheduleExistingBlockForOwnerDate($this->ownerId(), $date, (int) $matches[1], $startTime);
        } elseif (is_string($rawAgendaItem) && preg_match('/^topic-(\d+)$/D', $rawAgendaItem, $matches)) {
            $result = $plans->addTopicBlockForOwnerDate($this->ownerId(), $date, (int) $matches[1], $duration, $startTime, $available);
        } else {
            $result = ['status' => 'invalid'];
        }
        if (in_array($result['status'], ['added', 'scheduled'], true)) {
            return redirect()->to($this->planUrl($date) . '#agenda-list')->with('agenda_new_item_id', $result['item_id']);
        }

        $message = match ($result['status']) {
            'available_time_required' => 'Enter the amount of study time available for this day (15–480 minutes).',
            'topic_unavailable' => 'Choose one of your unfinished topics.',
            'duplicate' => 'That topic is already in this day’s plan.',
            'already_scheduled' => 'That block already has a start time.',
            'over_capacity' => 'This block exceeds the study time available for the day.',
            'overlap' => 'This time overlaps another scheduled block. Choose a different time.',
            'invalid' => 'Check the start time and block duration, then try again.',
            default => 'The study block could not be added. Please try again.',
        };

        return redirect()->to($this->planUrl($date))->with('error', $message);
    }

    public function updateProgress(int $itemId): Response|RedirectResponse
    {
        $rawDone = $this->request->getPost('done');
        $validDone = $rawDone === null || (is_string($rawDone) && $rawDone === '1');
        $progress = $rawDone === '1' ? 'done' : 'in_progress';
        $date = $this->validDate($this->request->getPost('plan_date')) ?? $this->today();
        $result = $validDone
            ? (new StudyPlanModel())->updateItemTopicProgressForOwnerDate($this->ownerId(), $date, $itemId, $progress)
            : 'invalid';
        $updated = $result === 'updated';
        $message = match ($result) {
            'updated' => $progress === 'done' ? 'Topic marked done.' : 'Topic moved back to in progress.',
            'missing' => 'That plan topic is no longer available.',
            default => 'The topic progress could not be updated. Try again.',
        };

        if ($this->request->isAJAX()) {
            return $this->response
                ->setStatusCode($updated ? 200 : ($result === 'missing' ? 404 : 422))
                ->setJSON([
                    'ok' => $updated,
                    'progress' => $progress,
                    'message' => $message,
                    'csrf' => ['name' => csrf_token(), 'hash' => csrf_hash()],
                ]);
        }

        return redirect()->to($this->planUrl($date))->with($updated ? 'success' : 'error', $message);
    }

    public function updateDuration(int $itemId): RedirectResponse
    {
        $date = $this->validDate($this->request->getPost('plan_date')) ?? $this->today();
        $rawMinutes = $this->request->getPost('duration_minutes');
        $minutes = is_string($rawMinutes) && ctype_digit($rawMinutes) ? (int) $rawMinutes : 0;
        $result = (new StudyPlanModel())->changeItemDurationForOwnerToday($this->ownerId(), $date, $itemId, $minutes);
        $message = match ($result) {
            'updated' => ['success', 'Block duration updated.'],
            'over_capacity' => ['error', 'That change would exceed your available time.'],
            'overlap' => ['error', 'That duration would overlap another scheduled block.'],
            'missing' => ['error', 'That plan block is no longer available.'],
            'invalid' => ['error', 'Enter a block duration between 1 and 480 minutes.'],
            default => ['error', 'The block could not be updated. Please try again.'],
        };

        return redirect()->to($this->planUrl($date))->with($message[0], $message[1]);
    }

    public function removeItem(int $itemId): RedirectResponse
    {
        $date = $this->validDate($this->request->getPost('plan_date')) ?? $this->today();
        $removed = (new StudyPlanModel())->removeItemForOwnerToday($this->ownerId(), $date, $itemId);

        return redirect()->to($this->planUrl($date))->with(
            $removed ? 'success' : 'error',
            $removed ? 'Plan block removed.' : 'That plan block is no longer available.',
        );
    }

    public function moveItem(int $itemId, string $direction): RedirectResponse
    {
        $date = $this->validDate($this->request->getPost('plan_date')) ?? $this->today();
        $result = (new StudyPlanModel())->moveItemForOwnerToday($this->ownerId(), $date, $itemId, $direction);
        $message = match ($result) {
            'updated' => ['success', 'Plan order updated.'],
            'unchanged' => ['success', 'That block is already at the edge of the plan.'],
            'missing' => ['error', 'That plan block is no longer available.'],
            default => ['error', 'The plan order could not be updated.'],
        };

        return redirect()->to($this->planUrl($date))->with($message[0], $message[1]);
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

    private function validDate(mixed $date): ?string
    {
        if (! is_string($date) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/D', $date)) {
            return null;
        }
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date, new DateTimeZone(config('App')->appTimezone));
        $errors = DateTimeImmutable::getLastErrors();

        return $parsed !== false && ($errors === false || ($errors['warning_count'] === 0 && $errors['error_count'] === 0))
            && $parsed->format('Y-m-d') === $date ? $date : null;
    }

    private function planUrl(string $date): string
    {
        return site_url('student/plan') . '?date=' . rawurlencode($date);
    }
}
