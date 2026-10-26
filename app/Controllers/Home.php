<?php

namespace App\Controllers;

use App\Content\StudySuggestions;

class Home extends BaseController
{
    public function index(): string
    {
        $isLoggedIn = auth()->loggedIn();
        $role = 'guest';
        $workspaceUrl = null;
        $workspaceLabel = null;

        if ($isLoggedIn) {
            $user = auth()->user();

            if ($user !== null && $user->inGroup('admin')) {
                $role = 'admin';
                $workspaceUrl = site_url('admin');
                $workspaceLabel = 'Open admin dashboard';
            } elseif ($user !== null && $user->inGroup('student') && ! $user->inGroup('admin')) {
                $role = 'student';
                $workspaceUrl = site_url('student');
                $workspaceLabel = 'Open my workspace';
            }
        }

        return view('home', [
            'isLoggedIn'    => $isLoggedIn,
            'appTourUserId' => $role === 'student' ? (string) auth()->id() : '',
            'role'          => $role,
            'workspaceUrl'  => $workspaceUrl,
            'workspaceLabel' => $workspaceLabel,
            'suggestions' => StudySuggestions::ITEMS,
            'suggestionSourceUrl' => StudySuggestions::SOURCE_URL,
            'suggestionSourceName' => StudySuggestions::SOURCE_NAME,
        ]);
    }
}
