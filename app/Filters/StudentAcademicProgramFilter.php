<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Requires students to select one account program before entering the workspace. */
final class StudentAcademicProgramFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = auth()->user();
        if ($user === null || ! $user->inGroup('student') || $user->inGroup('admin')) {
            return null;
        }

        $matchedRoute = service('router')->getMatchedRoute();
        $handler = $matchedRoute[1] ?? '';
        $isSelectionRoute = is_string($handler) && str_contains($handler, 'Student\\AcademicProgram::');
        $profile = db_connect()->table('users')->select('academic_program_selected_at')
            ->where('id', (int) $user->id)->get()->getRowArray();
        $hasSelection = $profile !== null && $profile['academic_program_selected_at'] !== null;

        if (! $hasSelection && ! $isSelectionRoute) {
            return redirect()->to(site_url('student/academic-program'));
        }
        if ($hasSelection && $isSelectionRoute) {
            return redirect()->to(site_url('student'));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
