<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Models\UserModel;

final class Profile extends BaseController
{
    public function show(): string
    {
        $user = $this->currentUser();

        return view('student/profile', [
            'username' => (string) $user->username,
            'email' => (string) ($user->email ?? ''),
            'errors' => session()->getFlashdata('errors') ?? [],
            'pageTitle' => lang('App.profile'),
        ]);
    }

    public function update(): RedirectResponse
    {
        $user = $this->currentUser();
        $rawUsername = $this->request->getPost('username');
        $username = is_string($rawUsername) ? trim($rawUsername) : '';
        $usernameRules = config('Auth')->usernameValidationRules['rules'];
        $usernameRules[] = 'is_unique[users.username,id,' . (int) $user->id . ']';

        if (! $this->validateData(
            ['username' => $username],
            ['username' => ['label' => lang('App.username'), 'rules' => $usernameRules]],
        )) {
            return redirect()->to(site_url('student/profile'))
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $user->username = $username;

        try {
            if (! model(UserModel::class)->save($user)) {
                return redirect()->to(site_url('student/profile'))
                    ->withInput()
                    ->with('error', lang('App.profileSaveFailed'));
            }
        } catch (DatabaseException) {
            // The unique username index remains the final guard for concurrent updates.
            return redirect()->to(site_url('student/profile'))
                ->withInput()
                ->with('error', lang('App.profileSaveFailed'));
        }

        return redirect()->to(site_url('student/profile'))->with('success', lang('App.profileSaved'));
    }

    private function currentUser(): \CodeIgniter\Shield\Entities\User
    {
        $authenticatedUser = auth()->user();
        if ($authenticatedUser === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        $user = model(UserModel::class)
            ->withIdentities()
            ->findById((int) $authenticatedUser->id);

        if ($user === null) {
            throw PageNotFoundException::forPageNotFound();
        }

        return $user;
    }
}
