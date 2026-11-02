<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\TopicModel;
use App\Services\TopicPriorityService;
use App\Services\AcademicProgramCatalog;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Topics extends BaseController
{
    public function priority(): string
    {
        $ownerId = $this->ownerId();
        $program = $this->currentAcademicProgram();
        $topics = (new TopicModel())->findAllForAcademicProgramOwner($ownerId, $program['curriculum_id']);

        return view('student/topics/priority', [
            'topics' => (new TopicPriorityService())->rank($topics),
            'subjects' => (new SubjectModel())->findAllForAcademicProgram($ownerId, $program['curriculum_id']),
            'pageTitle' => 'Duno Focus',
        ]);
    }

    public function all(): string
    {
        $ownerId = $this->ownerId();
        $program = $this->currentAcademicProgram();
        $filters = [
            'query' => '',
            'subject_id' => null,
            'importance' => '',
            'progress' => '',
            'due_state' => '',
        ];
        $errors = [];

        if ($this->request->getMethod() === 'POST') {
            $query = $this->request->getPost('query');
            $subjectId = $this->request->getPost('subject_id');
            $importance = $this->request->getPost('importance');
            $progress = $this->request->getPost('progress');
            $dueState = $this->request->getPost('due_state');
            $ownedSubjectIds = array_map(
                static fn (array $subject): int => (int) $subject['id'],
                (new SubjectModel())->findAllForAcademicProgram($ownerId, $program['curriculum_id']),
            );

            if ($query === null) {
                $query = '';
            } elseif (! is_string($query)) {
                $errors[] = 'Enter a text search term.';
            } elseif (mb_strlen($query) > 120) {
                $errors[] = 'Search terms must be 120 characters or fewer.';
            }
            if ($subjectId !== null && $subjectId !== ''
                && (! is_string($subjectId) || ! ctype_digit($subjectId)
                    || ! in_array((int) $subjectId, $ownedSubjectIds, true))) {
                $errors[] = 'Choose one of your subjects.';
            }
            if ($importance !== null && $importance !== ''
                && (! is_string($importance) || ! in_array($importance, ['low', 'medium', 'high'], true))) {
                $errors[] = 'Choose a valid importance filter.';
            }
            if ($progress !== null && $progress !== ''
                && (! is_string($progress) || ! in_array($progress, ['not_started', 'in_progress', 'done'], true))) {
                $errors[] = 'Choose a valid progress filter.';
            }
            if ($dueState !== null && $dueState !== ''
                && (! is_string($dueState) || ! in_array($dueState, ['undated', 'upcoming', 'overdue'], true))) {
                $errors[] = 'Choose a valid due date filter.';
            }

            $filters = [
                'query' => is_string($query) ? mb_substr(trim($query), 0, 120) : '',
                'subject_id' => is_string($subjectId) && ctype_digit($subjectId) && (int) $subjectId > 0
                    ? (int) $subjectId
                    : null,
                'importance' => is_string($importance) ? $importance : '',
                'progress' => is_string($progress) ? $progress : '',
                'due_state' => is_string($dueState) ? $dueState : '',
            ];
        }

        return view('student/topics/all', [
            'topics' => (new TopicModel())->searchForAcademicProgramOwner($ownerId, $filters, $program['curriculum_id']),
            'subjects' => (new SubjectModel())->findAllForAcademicProgram($ownerId, $program['curriculum_id']),
            'filters' => $filters,
            'errors' => $errors,
        ]);
    }

    public function index(int $subjectId): string
    {
        $this->ownedSubject($subjectId);

        return view('student/topics/index', [
            'subject' => $this->ownedSubject($subjectId),
            'topics'  => (new TopicModel())->findAllForSubjectOwner($subjectId, $this->ownerId()),
        ]);
    }

    public function newTopic(int $subjectId): string
    {
        return view('student/topics/form', [
            'subject' => $this->ownedSubject($subjectId),
            'topic'   => null,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function create(int $subjectId): RedirectResponse
    {
        $this->ownedSubject($subjectId);
        $topics = new TopicModel();
        $topicId = $topics->insertForSubjectOwner($subjectId, $this->ownerId(), $this->submittedTopic());

        if ($topicId === false) {
            return redirect()->to(site_url('student/subjects/' . $subjectId . '/topics/new'))
                ->withInput()
                ->with('errors', $this->saveErrors($topics));
        }

        return redirect()->to(site_url('student/subjects/' . $subjectId . '/topics/' . $topicId))
            ->with('success', 'Topic created.');
    }

    public function show(int $subjectId, int $topicId): string
    {
        $subject = $this->ownedSubject($subjectId);

        return view('student/topics/show', [
            'subject' => $subject,
            'topic'   => $this->ownedTopic($subjectId, $topicId),
        ]);
    }

    public function edit(int $subjectId, int $topicId): string
    {
        return view('student/topics/form', [
            'subject' => $this->ownedSubject($subjectId),
            'topic'   => $this->ownedTopic($subjectId, $topicId),
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function update(int $subjectId, int $topicId): RedirectResponse
    {
        $this->ownedSubject($subjectId);
        $this->ownedTopic($subjectId, $topicId);

        $topics = new TopicModel();
        if (! $topics->updateForSubjectOwner($subjectId, $topicId, $this->ownerId(), $this->submittedTopic())) {
            return redirect()->to(site_url('student/subjects/' . $subjectId . '/topics/' . $topicId . '/edit'))
                ->withInput()
                ->with('errors', $this->saveErrors($topics));
        }

        return redirect()->to(site_url('student/subjects/' . $subjectId . '/topics/' . $topicId))
            ->with('success', 'Topic updated.');
    }

    public function delete(int $subjectId, int $topicId): RedirectResponse
    {
        $this->ownedSubject($subjectId);
        $this->ownedTopic($subjectId, $topicId);

        $topics = new TopicModel();
        if (! $topics->deleteForSubjectOwner($subjectId, $topicId, $this->ownerId())) {
            return redirect()->to(site_url('student/subjects/' . $subjectId . '/topics/' . $topicId))
                ->with('error', 'This topic is used by a saved study session and cannot be deleted.');
        }

        return redirect()->to(site_url('student/subjects/' . $subjectId))
            ->with('success', 'Topic deleted.');
    }

    private function ownerId(): int
    {
        $user = auth()->user();

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (int) $user->id;
    }

    /** @return array{id: string, title: string, campus: string, college: string, curriculum_id: ?string, school: string} */
    private function currentAcademicProgram(): array
    {
        $program = (new AcademicProgramCatalog())->forUser($this->ownerId());
        if ($program === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $program;
    }

    /** @return array<string, mixed> */
    private function ownedSubject(int $subjectId): array
    {
        $program = $this->currentAcademicProgram();
        $subject = (new SubjectModel())->findForAcademicProgramOwner($subjectId, $this->ownerId(), $program['curriculum_id']);

        if ($subject === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $subject;
    }

    /** @return array<string, mixed> */
    private function ownedTopic(int $subjectId, int $topicId): array
    {
        $program = $this->currentAcademicProgram();
        $topic = (new TopicModel())->findForAcademicProgramSubjectOwner($subjectId, $topicId, $this->ownerId(), $program['curriculum_id']);

        if ($topic === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $topic;
    }

    /** @return array{title: string, due_date: ?string, importance: string, difficulty: string, progress: string} */
    private function submittedTopic(): array
    {
        $title = $this->request->getPost('title');
        $dueDate = $this->request->getPost('due_date');
        $importance = $this->request->getPost('importance');
        $difficulty = $this->request->getPost('difficulty');
        $progress = $this->request->getPost('progress');

        return [
            'title'      => is_string($title) ? trim($title) : '',
            'due_date'   => is_string($dueDate) && $dueDate !== '' ? $dueDate : null,
            'importance' => is_string($importance) ? $importance : '',
            'difficulty' => is_string($difficulty) && $difficulty !== '' ? $difficulty : 'medium',
            'progress'   => is_string($progress) ? $progress : '',
        ];
    }

    /** @return array<string, string> */
    private function saveErrors(TopicModel $topics): array
    {
        $errors = $topics->errors();

        return $errors !== [] ? $errors : ['title' => 'The topic could not be saved. Please try again.'];
    }
}
