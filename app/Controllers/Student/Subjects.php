<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\TopicModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Subjects extends BaseController
{
    public function index(): string
    {
        $subjects = new SubjectModel();

        return view('student/subjects/index', [
            'subjects' => $subjects->findAllForOwner($this->ownerId()),
            'query' => '',
            'errors' => [],
        ]);
    }

    public function search(): string
    {
        $query = $this->request->getPost('query');
        $errors = [];
        if ($query === null) {
            $query = '';
        } elseif (! is_string($query)) {
            $errors[] = 'Enter a text search term.';
            $query = '';
        } elseif (mb_strlen($query) > 120) {
            $errors[] = 'Search terms must be 120 characters or fewer.';
        }
        $query = mb_substr(trim($query), 0, 120);
        $ownerId = $this->ownerId();

        return view('student/subjects/index', [
            'subjects' => (new SubjectModel())->searchForOwner($ownerId, $query),
            'query' => $query,
            'errors' => $errors,
        ]);
    }

    public function newSubject(): string
    {
        return view('student/subjects/form', [
            'subject' => null,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function create(): RedirectResponse
    {
        $name = $this->submittedName();
        $subjects = new SubjectModel();
        $subjectId = $subjects->insert([
            'user_id' => $this->ownerId(),
            'name'    => $name,
        ], true);

        if ($subjectId === false) {
            return redirect()->to(site_url('student/subjects/new'))
                ->withInput()
                ->with('errors', $this->saveErrors($subjects));
        }

        return redirect()->to(site_url('student/subjects/' . $subjectId))
            ->with('success', 'Subject created.');
    }

    public function show(int $subjectId): string
    {
        return view('student/subjects/show', [
            'subject' => $this->ownedSubject($subjectId),
            'topics'  => (new TopicModel())->findAllForSubjectOwner($subjectId, $this->ownerId()),
        ]);
    }

    public function edit(int $subjectId): string
    {
        return view('student/subjects/form', [
            'subject' => $this->ownedSubject($subjectId),
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function update(int $subjectId): RedirectResponse
    {
        $this->ownedSubject($subjectId);

        $subjects = new SubjectModel();
        if (! $subjects->updateForOwner($subjectId, $this->ownerId(), $this->submittedName())) {
            return redirect()->to(site_url('student/subjects/' . $subjectId . '/edit'))
                ->withInput()
                ->with('errors', $this->saveErrors($subjects));
        }

        return redirect()->to(site_url('student/subjects/' . $subjectId))
            ->with('success', 'Subject updated.');
    }

    public function delete(int $subjectId): RedirectResponse
    {
        $this->ownedSubject($subjectId);

        $subjects = new SubjectModel();
        if (! $subjects->deleteForOwner($subjectId, $this->ownerId())) {
            return redirect()->to(site_url('student/subjects/' . $subjectId))
                ->with('error', 'This subject has topics or saved study sessions. Remove them before deleting the subject.');
        }

        return redirect()->to(site_url('student/subjects'))
            ->with('success', 'Subject deleted.');
    }

    private function ownerId(): int
    {
        $user = auth()->user();

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return (int) $user->id;
    }

    /** @return array<string, mixed> */
    private function ownedSubject(int $subjectId): array
    {
        $subject = (new SubjectModel())->findForOwner($subjectId, $this->ownerId());

        if ($subject === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $subject;
    }

    private function submittedName(): string
    {
        $name = $this->request->getPost('name');

        return is_string($name) ? trim($name) : '';
    }

    /** @return array<string, string> */
    private function saveErrors(SubjectModel $subjects): array
    {
        $errors = $subjects->errors();

        return $errors !== [] ? $errors : ['name' => 'The subject could not be saved. Please try again.'];
    }
}
