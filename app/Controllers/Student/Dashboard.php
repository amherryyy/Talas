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
            'message'   => 'Your private learning workspace will appear here as the approved course features are built.',
        ]);
    }
}
