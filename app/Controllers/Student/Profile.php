<?php

declare(strict_types=1);

namespace App\Controllers\Student;

use App\Controllers\BaseController;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Shield\Models\UserModel;
use RuntimeException;

final class Profile extends BaseController
{
    public function show(): string
    {
        $user = $this->currentUser();

        return view('student/profile', [
            'username' => (string) $user->username,
            'email' => (string) ($user->email ?? ''),
            'academicProgramTitle' => (string) (db_connect()->table('users')->select('academic_program_title')->where('id', (int) $user->id)->get()->getRow('academic_program_title') ?? ''),
            'errors' => session()->getFlashdata('errors') ?? [],
            'deleteErrors' => session()->getFlashdata('delete_errors') ?? [],
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

    public function deleteAccount(): RedirectResponse
    {
        $user = $this->currentUser();
        $userId = (int) $user->id;
        $confirmation = $this->request->getPost('delete_confirmation');
        $password = $this->request->getPost('current_password');
        $errors = [];

        if (! is_string($confirmation) || $confirmation !== 'DELETE') {
            $errors['delete_confirmation'] = lang('App.accountDeleteConfirmationError');
        }

        $identity = $user->getEmailIdentity();
        if (! is_string($password) || $password === '' || $identity === null || ! is_string($identity->secret2)
            || ! service('passwords')->verify($password, $identity->secret2)) {
            $errors['current_password'] = lang('App.accountDeletePasswordError');
        }

        if ($errors !== []) {
            return redirect()->to(site_url('student/profile'))->with('delete_errors', $errors);
        }

        $db = db_connect();
        $db->transBegin();

        try {
            // Remove dependent records in order so restrictive history references
            // are cleared before their topics and subjects are deleted.
            foreach ([
                'study_plan_items',
                'study_sessions',
                'topics',
                'study_plans',
                'subjects',
                'check_ins',
                'student_streak_days',
                'registration_verification_limits',
            ] as $table) {
                $db->table($table)->where('user_id', $userId)->delete();
            }

            if (! model(UserModel::class)->delete($userId, true)) {
                throw new RuntimeException('The account record could not be deleted.');
            }

            if ($db->transStatus() === false) {
                throw new RuntimeException('The account deletion transaction failed.');
            }

            $db->transCommit();
        } catch (DatabaseException|RuntimeException) {
            $db->transRollback();

            return redirect()->to(site_url('student/profile'))
                ->with('delete_errors', ['account' => lang('App.accountDeleteFailed')]);
        }

        auth()->logout();

        return redirect()->to(site_url('/'))->with('success', lang('App.accountDeleted'));
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
