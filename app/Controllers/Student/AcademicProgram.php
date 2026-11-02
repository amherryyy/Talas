<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Services\AcademicProgramCatalog;
use CodeIgniter\HTTP\RedirectResponse;

final class AcademicProgram extends BaseController
{
    private const CURRENT_CAMPUS = 'Bayombong Campus';
    private const CURRENT_SCHOOL = AcademicProgramCatalog::NVSU_BAYOMBONG_SCHOOL;
    private const MANUAL_PROGRAM_ID = AcademicProgramCatalog::MANUAL_ENTRY_ID;

    public function show(): string|RedirectResponse
    {
        $userId = (int) auth()->id();
        $user = db_connect()->table('users')->select('academic_program_selected_at')
            ->where('id', $userId)->get()->getRowArray();
        if ($user !== null && $user['academic_program_selected_at'] !== null) {
            return redirect()->to(site_url('student'));
        }

        $catalog = new AcademicProgramCatalog();

        return view('student/academic_program', [
            'pageTitle' => 'Choose your current program',
            'colleges' => $catalog->collegesAtCampus(self::CURRENT_CAMPUS),
            'programs' => $catalog->forCampus(self::CURRENT_CAMPUS),
            'errors' => session()->getFlashdata('errors') ?? [],
            'isAcademicProgramSelection' => true,
        ]);
    }

    public function save(): RedirectResponse
    {
        $userId = (int) auth()->id();
        $mode = $this->request->getPost('program_selection_mode');
        $college = $this->request->getPost('college');
        $selection = $this->request->getPost('academic_program_id');
        $schoolName = $this->request->getPost('school_name');
        $manualProgramTitle = $this->request->getPost('manual_program_title');
        $confirmed = $this->request->getPost('confirm_current_program');
        $catalog = new AcademicProgramCatalog();
        $program = $mode === 'catalog' && is_string($selection) ? $catalog->find($selection) : null;
        $errors = [];
        if (! in_array($mode, ['catalog', 'manual'], true)) {
            $errors['program_selection_mode'] = 'Choose an NVSU program or enter your school and program.';
        } elseif ($mode === 'catalog') {
            if (! is_string($college) || ! in_array($college, $catalog->collegesAtCampus(self::CURRENT_CAMPUS), true)) {
                $errors['college'] = 'Choose a college at NVSU Bayombong.';
            }
            if ($program === null || $program['campus'] !== self::CURRENT_CAMPUS) {
                $errors['academic_program_id'] = 'Choose a program offered at NVSU Bayombong.';
            } elseif ($program['college'] !== $college) {
                $errors['academic_program_id'] = 'Choose a program from the selected college.';
            }
        } else {
            $school = is_string($schoolName) ? trim($schoolName) : '';
            $manualTitle = is_string($manualProgramTitle) ? trim($manualProgramTitle) : '';
            if (! $this->validateData(
                ['school_name' => $school, 'manual_program_title' => $manualTitle],
                [
                    'school_name' => ['label' => 'School', 'rules' => 'required|max_length[160]'],
                    'manual_program_title' => ['label' => 'Program', 'rules' => 'required|max_length[180]'],
                ],
            )) {
                $errors = array_merge($errors, $this->validator->getErrors());
            }
        }
        if ($confirmed !== '1') {
            $errors['confirm_current_program'] = 'Confirm that this is your current program.';
        }

        if ($errors !== []) {
            return redirect()->to(site_url('student/academic-program'))->withInput()->with('errors', $errors);
        }

        // Atomic condition keeps the initial choice immutable, including concurrent submissions.
        $db = db_connect();
        $savedProgram = $mode === 'manual'
            ? [
                'id' => self::MANUAL_PROGRAM_ID,
                'title' => trim((string) $manualProgramTitle),
                'school' => trim((string) $schoolName),
            ]
            : [
                'id' => $program['id'],
                'title' => $program['title'],
                'school' => self::CURRENT_SCHOOL,
            ];
        $db->table('users')->where('id', $userId)->where('academic_program_selected_at', null)->update([
            'academic_program_id' => $savedProgram['id'],
            'academic_program_title' => $savedProgram['title'],
            'academic_school_name' => $savedProgram['school'],
            'academic_program_selected_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('student'))->with('success', 'Your current program is saved to your profile.');
    }
}
