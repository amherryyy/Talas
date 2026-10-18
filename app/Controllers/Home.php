<?php

namespace App\Controllers;

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
            'role'          => $role,
            'workspaceUrl'  => $workspaceUrl,
            'workspaceLabel' => $workspaceLabel,
        ]);
    }
}
