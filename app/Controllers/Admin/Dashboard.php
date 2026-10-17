<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Services\AdminDashboardSummaryService;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('admin/dashboard', [
            'pageTitle' => 'Duno admin dashboard',
            'summary' => (new AdminDashboardSummaryService())->summarize(),
        ]);
    }
}
