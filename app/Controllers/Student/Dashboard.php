<?php

namespace App\Controllers\Student;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('role_landing', [
            'pageTitle' => 'Student workspace',
            'heading'   => 'Your Talas workspace',
            'message'   => 'Start by organizing the subjects you want to study.',
            'actionHref'  => site_url('student/subjects'),
            'actionLabel' => 'Manage subjects',
        ]);
    }
}
