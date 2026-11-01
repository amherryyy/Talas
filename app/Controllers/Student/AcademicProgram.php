<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use App\Services\AcademicProgramCatalog;
use CodeIgniter\HTTP\RedirectResponse;

final class AcademicProgram extends BaseController
{
    private const CURRENT_CAMPUS = 'Bayombong Campus';

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
        $college = $this->request->getPost('college');
        $selection = $this->request->getPost('academic_program_id');
        $confirmed = $this->request->getPost('confirm_current_program');
        $catalog = new AcademicProgramCatalog();
        $program = is_string($selection) ? $catalog->find($selection) : null;
        $errors = [];
        if (! is_string($college) || ! in_array($college, $catalog->collegesAtCampus(self::CURRENT_CAMPUS), true)) {
            $errors['college'] = 'Choose a college at NVSU Bayombong.';
        }
        if ($program === null || $program['campus'] !== self::CURRENT_CAMPUS) {
            $errors['academic_program_id'] = 'Choose a program offered at NVSU Bayombong.';
        } elseif ($program['college'] !== $college) {
            $errors['academic_program_id'] = 'Choose a program from the selected college.';
        }
        if ($confirmed !== '1') {
            $errors['confirm_current_program'] = 'Confirm that this is your current program.';
        }

        if ($errors !== []) {
            return redirect()->to(site_url('student/academic-program'))->withInput()->with('errors', $errors);
        }

        // Atomic condition keeps the initial choice immutable, including concurrent submissions.
        $db = db_connect();
        $db->table('users')->where('id', $userId)->where('academic_program_selected_at', null)->update([
            'academic_program_id' => $program['id'],
            'academic_program_title' => $program['title'],
            'academic_program_selected_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('student'))->with('success', 'Your current program is saved to your profile.');
    }
}
