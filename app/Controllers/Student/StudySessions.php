<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\StudySessionModel;
use App\Models\SubjectModel;
use App\Models\TopicModel;
use App\Services\StudyProgressionService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use DateTimeImmutable;
use DateTimeZone;

class StudySessions extends BaseController
{
    public function index(): string
    {
        $ownerId = $this->ownerId();
        $rawDuration = old('duration_minutes', '');

        return view('student/sessions/index', [
            'sessions' => (new StudySessionModel())->findAllForOwner($ownerId),
            'subjects' => (new SubjectModel())->findAllForOwner($ownerId),
            'topics' => (new TopicModel())->findAllForOwner($ownerId),
            'errors' => session()->getFlashdata('errors') ?? [],
            'reviewing' => is_string($rawDuration) && $rawDuration !== '',
            'pageTitle' => 'Duno Timer',
        ]);
    }

    public function create(): RedirectResponse
    {
        $input = $this->validSessionInput('student/sessions');
        if ($input instanceof RedirectResponse) {
            return $input;
        }

        $ownerId = $this->ownerId();
        $sessionModel = new StudySessionModel();
        $xpBefore = $sessionModel->totalMinutesForOwner($ownerId);
        $completedAt = $this->now();
        $sessionId = $sessionModel->createForOwner(
            $ownerId,
            $input['subject_id'],
            $input['topic_id'],
            $input['duration_minutes'],
            $completedAt,
        );

        if ($sessionId === false) {
            return redirect()->to(site_url('student/sessions'))->withInput()->with('error', 'The session could not be saved. Please check your selections and try again.');
        }

        return $this->sessionSavedRedirect($xpBefore, $xpBefore + $input['duration_minutes']);
    }

    public function edit(int $sessionId): string
    {
        $ownerId = $this->ownerId();
        $session = (new StudySessionModel())->findForOwner($sessionId, $ownerId);
        if ($session === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('student/sessions/edit', [
            'studySession' => $session,
            'subjects' => (new SubjectModel())->findAllForOwner($ownerId),
            'topics' => (new TopicModel())->findAllForOwner($ownerId),
            'errors' => session()->getFlashdata('errors') ?? [],
            'pageTitle' => 'Edit timer session',
        ]);
    }

    public function update(int $sessionId): RedirectResponse
    {
        $ownerId = $this->ownerId();
        $session = (new StudySessionModel())->findForOwner($sessionId, $ownerId);
        if ($session === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $input = $this->validSessionInput('student/sessions/' . $sessionId . '/edit');
        if ($input instanceof RedirectResponse) {
            return $input;
        }

        $xpBefore = (new StudySessionModel())->totalMinutesForOwner($ownerId);
        if (! (new StudySessionModel())->updateForOwner(
            $sessionId,
            $ownerId,
            $input['subject_id'],
            $input['topic_id'],
            $input['duration_minutes'],
        )) {
            return redirect()->to(site_url('student/sessions/' . $sessionId . '/edit'))
                ->withInput()->with('error', 'The session could not be updated. Please check your selections and try again.');
        }

        $xpAfter = $xpBefore - (int) $session['duration_minutes'] + $input['duration_minutes'];

        return $this->sessionSavedRedirect($xpBefore, $xpAfter, 'Study session updated.');
    }

    public function delete(int $sessionId): string
    {
        $studySession = (new StudySessionModel())->findForOwner($sessionId, $this->ownerId());
        if ($studySession === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return view('student/sessions/delete', [
            'studySession' => $studySession,
            'pageTitle' => 'Delete timer session',
        ]);
    }

    public function confirmDelete(int $sessionId): RedirectResponse
    {
        $ownerId = $this->ownerId();
        if ((new StudySessionModel())->findForOwner($sessionId, $ownerId) === null) {
            throw PageNotFoundException::forPageNotFound();
        }
        if ($this->request->getPost('confirm_delete') !== '1') {
            return redirect()->to(site_url('student/sessions'))->with('error', 'Confirm deletion to remove the study session.');
        }
        if (! (new StudySessionModel())->deleteForOwner($sessionId, $ownerId)) {
            return redirect()->to(site_url('student/sessions'))->with('error', 'The study session could not be deleted.');
        }

        return redirect()->to(site_url('student/sessions'))->with('success', 'Study session deleted.');
    }

    /** @return array{subject_id: int, topic_id: int|null, duration_minutes: int}|RedirectResponse */
    private function validSessionInput(string $returnPath): array|RedirectResponse
    {
        $subject = $this->request->getPost('subject_id');
        $topic = $this->request->getPost('topic_id');
        $duration = $this->request->getPost('duration_minutes');
        $data = [
            'subject_id' => is_string($subject) ? $subject : '',
            'topic_id' => $topic === null ? '' : (is_string($topic) ? $topic : 'invalid'),
            'duration_minutes' => is_string($duration) ? $duration : '',
        ];
        $rules = [
            'subject_id' => 'required|is_natural_no_zero',
            'topic_id' => 'permit_empty|is_natural_no_zero',
            'duration_minutes' => 'required|is_natural_no_zero|greater_than_equal_to[1]|less_than_equal_to[480]',
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->to(site_url($returnPath))->withInput()->with('errors', $this->validator->getErrors());
        }

        $ownerId = $this->ownerId();
        $subjectId = (int) $data['subject_id'];
        $topicId = $data['topic_id'] === '' ? null : (int) $data['topic_id'];
        $errors = [];
        if ((new SubjectModel())->findForOwner($subjectId, $ownerId) === null) {
            $errors['subject_id'] = 'Choose one of your subjects.';
        } elseif ($topicId !== null && (new TopicModel())->findForSubjectOwner($subjectId, $topicId, $ownerId) === null) {
            $errors['topic_id'] = 'Choose a topic belonging to the selected subject.';
        }
        if ($errors !== []) {
            return redirect()->to(site_url($returnPath))->withInput()->with('errors', $errors);
        }

        return [
            'subject_id' => $subjectId,
            'topic_id' => $topicId,
            'duration_minutes' => (int) $data['duration_minutes'],
        ];
    }

    private function ownerId(): int
    {
        $user = auth()->user();
        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (int) $user->id;
    }

    private function now(): string
    {
        $timezone = new DateTimeZone(config('App')->appTimezone);

        return (new DateTimeImmutable('now', $timezone))->format('Y-m-d H:i:s');
    }

    private function sessionSavedRedirect(int $xpBefore, int $xpAfter, string $message = 'Study session saved.'): RedirectResponse
    {
        $progression = new StudyProgressionService();
        $previousLevel = $progression->levelForXp($xpBefore);
        $newLevel = $progression->levelForXp($xpAfter);
        $response = redirect()->to(site_url('student/sessions'))->with('success', $message);

        if ($newLevel['level'] <= $previousLevel['level']) {
            return $response;
        }

        $newUnlocks = array_values(array_map(
            static fn (array $unlock): string => $unlock['name'],
            array_filter(
                StudyProgressionService::UNLOCKS,
                static fn (array $unlock): bool => $unlock['level'] > $previousLevel['level'] && $unlock['level'] <= $newLevel['level'],
            ),
        ));

        return $response->with('level_up', [
            'level' => $newLevel['level'],
            'name' => $newLevel['name'],
            'total_xp' => $xpAfter,
            'unlocks' => $newUnlocks,
        ]);
    }
}
