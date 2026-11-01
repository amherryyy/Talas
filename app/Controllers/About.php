<?php

namespace App\Controllers;

class About extends BaseController
{
    public function index(): string
    {
        $workspaceUrl = site_url('register');
        $workspaceAction = lang('App.createAccount');

        if (auth()->loggedIn()) {
            $user = auth()->user();
            if ($user !== null && $user->inGroup('admin')) {
                $workspaceUrl = site_url('admin');
                $workspaceAction = 'Open admin dashboard';
            } elseif ($user !== null && $user->inGroup('student')) {
                $workspaceUrl = site_url('student');
                $workspaceAction = 'Open my study space';
            }
        }

        return view('about', [
            'workspaceUrl' => $workspaceUrl,
            'workspaceAction' => $workspaceAction,
            'footerGroups' => [
                ['id' => 'about-footer-start', 'title' => 'Start here', 'links' => [
                    ['label' => lang('App.home'), 'href' => site_url('/')],
                    ['label' => lang('App.about'), 'href' => site_url('about#how-to-use')],
                ]],
                ['id' => 'about-footer-account', 'title' => 'Your account', 'links' => [
                    ['label' => lang('App.signIn'), 'href' => site_url('login')],
                    ['label' => lang('App.createAccount'), 'href' => site_url('register')],
                ]],
            ],
        ]);
    }
}
