<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('role_landing', [
            'pageTitle' => 'Admin workspace',
            'heading'   => 'Duno administration',
            'message'   => 'The approved aggregate admin view will be built here. No student records or check-in details are displayed.',
        ]);
    }
}
