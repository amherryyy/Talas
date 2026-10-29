<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\TopicModel;
use App\Services\ChedCurriculumCatalog;
use App\Services\ChedCurriculumImportService;
use App\Services\DepedShsCatalog;
use App\Services\DepedShsImportService;
use App\Services\NvsuProgramCatalog;
use App\Services\NvsuCurriculumCatalog;
use App\Services\NvsuCurriculumImportService;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Subjects extends BaseController
{
    public function index(): string
    {
        $subjects = new SubjectModel();
        $nvsuCurricula = new NvsuCurriculumCatalog();
        $programId = $this->request->getGet('program');
        $programId = is_string($programId) && $nvsuCurricula->find($programId) !== null ? $programId : '';

        return view('student/subjects/index', [
            'subjects' => $subjects->findAllForOwner($this->ownerId(), $programId),
            'query' => '',
            'programId' => $programId,
            'nvsuCurricula' => $nvsuCurricula->all(),
            'errors' => [],
        ]);
    }

    public function templates(): string
    {
        $catalog = new ChedCurriculumCatalog();
        $nvsuCatalog = new NvsuCurriculumCatalog();
        $programId = $this->request->getGet('program');
        $programId = is_string($programId) ? $programId : '';
        $nvsuProgramId = $this->request->getGet('nvsu');
        $nvsuProgramId = is_string($nvsuProgramId) ? $nvsuProgramId : '';

        return view('student/subjects/templates', [
            'programs' => $catalog->all(),
            'nvsuCampuses' => (new NvsuProgramCatalog())->all(),
            'nvsuCurricula' => $nvsuCatalog->all(),
            'nvsuProgramId' => $nvsuProgramId,
            'nvsuProgram' => $nvsuProgramId === '' ? null : $nvsuCatalog->find($nvsuProgramId),
            'nvsuErrors' => session()->getFlashdata('nvsu_errors') ?? [],
            'shsGroups' => (new DepedShsCatalog())->groups(),
            'program' => $programId === '' ? null : $catalog->find($programId),
            'programId' => $programId,
            'errors' => session()->getFlashdata('errors') ?? [],
            'shsErrors' => session()->getFlashdata('shs_errors') ?? [],
        ]);
    }

    public function importNvsuCurriculum(): RedirectResponse
    {
        $programId = $this->request->getPost('nvsu_program_id');
        $courseIds = $this->request->getPost('course_ids');
        $catalog = new NvsuCurriculumCatalog();

        if (! is_string($programId) || $catalog->find($programId) === null) {
            return redirect()->to(site_url('student/subjects/templates#nvsu-curricula'))
                ->with('nvsu_errors', ['Choose an NVSU course list from the available references.']);
        }

        if (! is_array($courseIds) || count($courseIds) > 150
            || array_filter($courseIds, static fn ($id): bool => ! is_string($id)) !== []) {
            return redirect()->to(site_url('student/subjects/templates?nvsu=' . rawurlencode($programId) . '#nvsu-curricula'))
                ->with('nvsu_errors', ['Select courses from the NVSU course list.']);
        }

        $result = (new NvsuCurriculumImportService())->import($programId, array_values($courseIds), $this->ownerId());
        if (! $result['ok']) {
            return redirect()->to(site_url('student/subjects/templates?nvsu=' . rawurlencode($programId) . '#nvsu-curricula'))
                ->with('nvsu_errors', ['Select at least one course from this NVSU reference list.']);
        }

        $message = $result['imported'] . ' NVSU course' . ($result['imported'] === 1 ? '' : 's') . ' added to your subjects.';
        if ($result['duplicates'] > 0) {
            $message .= ' ' . $result['duplicates'] . ' duplicate' . ($result['duplicates'] === 1 ? ' was' : 's were') . ' skipped.';
        }

        return redirect()->to(site_url('student/subjects'))->with('success', $message);
    }

    public function importTemplate(): RedirectResponse
    {
        $programId = $this->request->getPost('program_id');
        $courseIds = $this->request->getPost('course_ids');

        if (! is_string($programId) || (new ChedCurriculumCatalog())->find($programId) === null) {
            return redirect()->to(site_url('student/subjects/templates'))
                ->with('errors', ['Choose a curriculum template from the list.']);
        }

        if (! is_array($courseIds) || count($courseIds) > 100
            || array_filter($courseIds, static fn ($id): bool => ! is_string($id)) !== []) {
            return redirect()->to(site_url('student/subjects/templates?program=' . rawurlencode($programId)))
                ->with('errors', ['Select courses from the curriculum preview.']);
        }

        $result = (new ChedCurriculumImportService())->import($programId, array_values($courseIds), $this->ownerId());
        if (! $result['ok']) {
            return redirect()->to(site_url('student/subjects/templates?program=' . rawurlencode($programId)))
                ->with('errors', ['Select at least one course from this template. If you selected courses, try again.']);
        }

        $message = $result['imported'] . ' course' . ($result['imported'] === 1 ? '' : 's') . ' added to your subjects.';
        if ($result['duplicates'] > 0) {
            $message .= ' ' . $result['duplicates'] . ' duplicate' . ($result['duplicates'] === 1 ? ' was' : 's were') . ' skipped.';
        }

        return redirect()->to(site_url('student/subjects'))
            ->with('success', $message);
    }

    public function importShsSubjects(): RedirectResponse
    {
        $subjectIds = $this->request->getPost('subject_ids');
        if (! is_array($subjectIds) || count($subjectIds) > 150
            || array_filter($subjectIds, static fn ($id): bool => ! is_string($id)) !== []) {
            return redirect()->to(site_url('student/subjects/templates#deped-shs'))
                ->with('shs_errors', ['Select subjects from the DepEd SHS list.']);
        }

        $result = (new DepedShsImportService())->import(array_values($subjectIds), $this->ownerId());
        if (! $result['ok']) {
            return redirect()->to(site_url('student/subjects/templates#deped-shs'))
                ->with('shs_errors', ['Select at least one subject from the DepEd SHS list.']);
        }

        $message = $result['imported'] . ' SHS subject' . ($result['imported'] === 1 ? '' : 's') . ' added to your subjects.';
        if ($result['duplicates'] > 0) {
            $message .= ' ' . $result['duplicates'] . ' duplicate' . ($result['duplicates'] === 1 ? ' was' : 's were') . ' skipped.';
        }

        return redirect()->to(site_url('student/subjects'))->with('success', $message);
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
        $nvsuCurricula = new NvsuCurriculumCatalog();
        $programId = $this->request->getPost('program_id');
        $programId = is_string($programId) && $nvsuCurricula->find($programId) !== null ? $programId : '';

        return view('student/subjects/index', [
            'subjects' => (new SubjectModel())->searchForOwner($ownerId, $query, $programId),
            'query' => $query,
            'programId' => $programId,
            'nvsuCurricula' => $nvsuCurricula->all(),
            'errors' => $errors,
        ]);
    }

    public function newSubject(): string
    {
        $catalog = new NvsuCurriculumCatalog();
        return view('student/subjects/form', [
            'subject' => null,
            'nvsuCurricula' => $catalog->all(),
            'selectedCourseId' => '',
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function create(): RedirectResponse
    {
        [$name, $programId, $selectionErrors] = $this->submittedSubjectSelection();
        if ($selectionErrors !== []) {
            return redirect()->to(site_url('student/subjects/new'))
                ->withInput()
                ->with('errors', $selectionErrors);
        }

        $subjects = new SubjectModel();
        $subjectId = $subjects->insert([
            'user_id' => $this->ownerId(),
            'name'    => $name,
            'program_id' => $programId,
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
        $subject = $this->ownedSubject($subjectId);
        $program = (new NvsuCurriculumCatalog())->find((string) ($subject['program_id'] ?? ''));

        return view('student/subjects/show', [
            'subject' => $subject,
            'programTitle' => $program['title'] ?? null,
            'topics'  => (new TopicModel())->findAllForSubjectOwner($subjectId, $this->ownerId()),
        ]);
    }

    public function edit(int $subjectId): string
    {
        $subject = $this->ownedSubject($subjectId);
        $catalog = new NvsuCurriculumCatalog();
        $selectedCourseId = '';
        $program = $catalog->find((string) ($subject['program_id'] ?? ''));
        if ($program !== null) {
            foreach ($program['courses'] as $course) {
                if ($course['title'] === $subject['name']) {
                    $selectedCourseId = $course['id'];
                    break;
                }
            }
        }

        return view('student/subjects/form', [
            'subject' => $subject,
            'nvsuCurricula' => $catalog->all(),
            'selectedCourseId' => $selectedCourseId,
            'errors'  => session()->getFlashdata('errors') ?? [],
        ]);
    }

    public function update(int $subjectId): RedirectResponse
    {
        $this->ownedSubject($subjectId);
        [$name, $programId, $selectionErrors] = $this->submittedSubjectSelection();
        if ($selectionErrors !== []) {
            return redirect()->to(site_url('student/subjects/' . $subjectId . '/edit'))
                ->withInput()
                ->with('errors', $selectionErrors);
        }

        $subjects = new SubjectModel();
        if (! $subjects->updateForOwner($subjectId, $this->ownerId(), $name, $programId)) {
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

    /** @return array{string, ?string, array<string, string>} */
    private function submittedSubjectSelection(): array
    {
        $errors = [];
        $programId = $this->request->getPost('program_id');
        $programId = is_string($programId) ? trim($programId) : '';
        $catalog = new NvsuCurriculumCatalog();

        if ($programId !== '' && $catalog->find($programId) === null) {
            $errors['program_id'] = 'Choose a program from the NVSU course lists.';
            $programId = '';
        }

        $courseId = $this->request->getPost('course_id');
        $name = $this->submittedName();
        if ($courseId !== null && $courseId !== '') {
            if (! is_string($courseId) || $programId === '') {
                $errors['course_id'] = 'Choose an NVSU program before selecting one of its courses.';
            } else {
                $courses = $catalog->selectedCourses($programId, [$courseId]);
                if ($courses === []) {
                    $errors['course_id'] = 'Choose a course from the selected NVSU program.';
                } else {
                    $name = $courses[0]['title'];
                }
            }
        }

        return [$name, $programId === '' ? null : $programId, $errors];
    }

    /** @return array<string, string> */
    private function saveErrors(SubjectModel $subjects): array
    {
        $errors = $subjects->errors();

        return $errors !== [] ? $errors : ['name' => 'The subject could not be saved. Please try again.'];
    }
}
