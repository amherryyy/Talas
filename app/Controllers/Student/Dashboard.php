<?php

namespace App\Controllers\Student;

use App\Content\StudySuggestions;
use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('student/dashboard', [
            'pageTitle' => 'Duno workspace',
            'suggestions' => StudySuggestions::ITEMS,
            'suggestionSourceUrl' => StudySuggestions::SOURCE_URL,
            'suggestionSourceName' => StudySuggestions::SOURCE_NAME,
        ]);
    }
}
