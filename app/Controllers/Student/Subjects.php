<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Models\SubjectModel;
use App\Models\TopicModel;
use App\Services\NvsuCurriculumCatalog;
use App\Services\NvsuCurriculumImportService;
use App\Services\AcademicProgramCatalog;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

class Subjects extends BaseController
{
    public function index(): string
    {
        $program = $this->currentAcademicProgram();
        $curriculum = $program['curriculum_id'] === null ? null : (new NvsuCurriculumCatalog())->find($program['curriculum_id']);

        return view('student/subjects/index', [
            'subjects' => (new SubjectModel())->findAllForAcademicProgram($this->ownerId(), $program['curriculum_id']),
            'query' => '',
            'programId' => $program['curriculum_id'],
            'academicProgram' => $program,
            'nvsuCurricula' => $curriculum === null ? [] : [$curriculum],
            'errors' => [],
        ]);
    }

    public function templates(): string
    {
        $academicProgram = $this->currentAcademicProgram();
        $curriculum = $academicProgram['curriculum_id'] === null ? null : (new NvsuCurriculumCatalog())->find($academicProgram['curriculum_id']);

        return view('student/subjects/curriculum', [
            'academicProgram' => $academicProgram,
            'curriculum' => $curriculum,
            'nvsuErrors' => session()->getFlashdata('nvsu_errors') ?? [],
        ]);
    }

    public function importNvsuCurriculum(): RedirectResponse
    {
        $programId = $this->request->getPost('nvsu_program_id');
        $courseIds = $this->request->getPost('course_ids');
        $catalog = new NvsuCurriculumCatalog();
        $accountProgram = $this->currentAcademicProgram();

        if (! is_string($programId) || $programId !== $accountProgram['curriculum_id'] || $catalog->find($programId) === null) {
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
        return redirect()->to(site_url('student/subjects/templates'))
            ->with('error', 'Only subjects from your selected NVSU program can be added here.');
    }

    public function importShsSubjects(): RedirectResponse
    {
        return redirect()->to(site_url('student/subjects/templates'))
            ->with('error', 'Only subjects from your selected NVSU program can be added here.');
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
        $academicProgram = $this->currentAcademicProgram();
        $curriculum = $academicProgram['curriculum_id'] === null ? null : (new NvsuCurriculumCatalog())->find($academicProgram['curriculum_id']);

        return view('student/subjects/index', [
            'subjects' => (new SubjectModel())->searchForAcademicProgram($ownerId, $query, $academicProgram['curriculum_id']),
            'query' => $query,
            'programId' => $academicProgram['curriculum_id'],
            'academicProgram' => $academicProgram,
            'nvsuCurricula' => $curriculum === null ? [] : [$curriculum],
            'errors' => $errors,
        ]);
    }

    public function newSubject(): string
    {
        $academicProgram = $this->currentAcademicProgram();
        $catalog = new NvsuCurriculumCatalog();
        $curriculum = $academicProgram['curriculum_id'] === null ? null : $catalog->find($academicProgram['curriculum_id']);
        return view('student/subjects/form', [
            'subject' => null,
            'academicProgram' => $academicProgram,
            'nvsuCurricula' => $curriculum === null ? [] : [$curriculum],
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
        $academicProgram = $this->currentAcademicProgram();
        $catalog = new NvsuCurriculumCatalog();
        $selectedCourseId = '';
        $program = $academicProgram['curriculum_id'] === null ? null : $catalog->find($academicProgram['curriculum_id']);
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
            'academicProgram' => $academicProgram,
            'nvsuCurricula' => $program === null ? [] : [$program],
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
        $program = $this->currentAcademicProgram();
        $subject = (new SubjectModel())->findForAcademicProgramOwner($subjectId, $this->ownerId(), $program['curriculum_id']);

        if ($subject === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $subject;
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

    /** @return array{string, ?string, array<string, string>} */
    private function submittedSubjectSelection(): array
    {
        $errors = [];
        $program = $this->currentAcademicProgram();
        $programId = $program['curriculum_id'];
        $catalog = new NvsuCurriculumCatalog();

        $courseId = $this->request->getPost('course_id');
        if ($programId === null) {
            $name = $this->request->getPost('name');
            if (! is_string($name) || trim($name) === '' || mb_strlen(trim($name)) > 120) {
                $errors['name'] = 'Enter a subject name of 1 to 120 characters.';
                return ['', null, $errors];
            }

            if (is_string($courseId) && $courseId !== '') {
                $errors['course_id'] = 'This program has no NVSU course list. Enter the subject name instead.';
                return ['', null, $errors];
            }

            return [trim($name), null, $errors];
        }

        if (! is_string($courseId) || $courseId === '') {
            $errors['course_id'] = 'Choose a subject from your program’s NVSU course list.';
            return ['', $programId, $errors];
        }
        $courses = $catalog->selectedCourses($programId, [$courseId]);
        if ($courses === []) {
            $errors['course_id'] = 'Choose a subject from your program’s NVSU course list.';
            return ['', $programId, $errors];
        }

        return [$courses[0]['title'], $programId, $errors];
    }

    /** @return array<string, string> */
    private function saveErrors(SubjectModel $subjects): array
    {
        $errors = $subjects->errors();

        return $errors !== [] ? $errors : ['name' => 'The subject could not be saved. Please try again.'];
    }
}
