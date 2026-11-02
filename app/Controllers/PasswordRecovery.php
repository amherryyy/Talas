<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\PasswordResetCodeModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Passwords as ShieldPasswords;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use Config\Email as EmailConfig;
use Config\Services;

final class PasswordRecovery extends BaseController
{
    private const CODE_LIFETIME_MINUTES = 15;

    public function forgot()
    {
        return view('auth/forgot_password');
    }

    public function sendResetCode()
    {
        $emailInput = $this->request->getPost('email');
        $emailAddress = is_string($emailInput) ? strtolower(trim($emailInput)) : '';
        $user = null;

        if ($emailAddress !== '' && filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            /** @var UserModel $users */
            $users = model(UserModel::class);
            $candidate = $users->findByCredentials(['email' => $emailAddress]);
            if ($candidate instanceof User && $candidate->isActivated()) {
                $user = $candidate;
            }
        }

        if ($user instanceof User && $user->email !== null && $user->email !== '') {
            $this->createAndSendResetCode($user);
        }

        return redirect()->to(url_to('password.reset'))
            ->with('message', lang('Registration.resetRequestReceived'));
    }

    public function reset()
    {
        return view('auth/reset_password');
    }

    public function updatePassword()
    {
        $emailInput = $this->request->getPost('email');
        $codeInput = $this->request->getPost('code');
        $emailAddress = is_string($emailInput) ? strtolower(trim($emailInput)) : '';
        $code = is_string($codeInput) ? trim($codeInput) : '';
        if (! preg_match('/\A[0-9]{6}\z/', $code) || ! filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', lang('Registration.resetCodeInvalid'));
        }

        /** @var UserModel $users */
        $users = model(UserModel::class);
        $user = $users->findByCredentials(['email' => $emailAddress]);
        if (! $user instanceof User || ! $user->isActivated()) {
            return redirect()->back()->with('error', lang('Registration.resetCodeInvalid'));
        }

        $codes = model(PasswordResetCodeModel::class);
        $codeRow = $codes->findActiveForUser((int) $user->id, Time::now()->toDateTimeString());
        if ($codeRow === null || ! password_verify($code, (string) $codeRow['code_hash'])) {
            if ($codeRow !== null) {
                $codes->incrementFailedAttempts((int) $codeRow['id'], Time::now()->toDateTimeString());
            }

            return redirect()->back()->with('error', lang('Registration.resetCodeInvalid'));
        }

        $passwordInput = $this->request->getPost('password');
        $confirmationInput = $this->request->getPost('password_confirm');
        if (! is_string($passwordInput) || ! is_string($confirmationInput)) {
            return redirect()->back()->with('errors', ['password' => lang('Auth.errorPasswordEmpty')]);
        }
        $password = $passwordInput;
        $rules = [
            'password' => [
                'label' => 'Auth.password',
                'rules' => ['required', ShieldPasswords::getMaxLengthRule()],
                'errors' => ['max_byte' => 'Auth.errorPasswordTooLongBytes'],
            ],
            'password_confirm' => [
                'label' => 'Auth.passwordConfirm',
                'rules' => 'required|matches[password]',
            ],
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $passwordCheck = service('passwords')->check($password, $user);
        if (! $passwordCheck->isOK()) {
            return redirect()->back()->with('errors', [
                'password' => $passwordCheck->reason() ?? lang('Auth.errorPasswordLength', [config('Auth')->minimumPasswordLength]),
            ]);
        }

        $db = db_connect(config('Auth')->DBGroup);
        $db->transBegin();
        try {
            if (! $codes->consumeActive((int) $codeRow['id'], Time::now()->toDateTimeString())) {
                $db->transRollback();

                return redirect()->back()->with('error', lang('Registration.resetCodeInvalid'));
            }

            $user->setPassword($password);
            $users->save($user);
            $codes->deleteForUser((int) $user->id);

            if (! $db->transStatus()) {
                $db->transRollback();

                return redirect()->back()->with('error', lang('Registration.resetFailed'));
            }

            $db->transCommit();
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

        auth('session')->logout();

        return redirect()->to(url_to('login'))->with('message', lang('Registration.resetComplete'));
    }

    private function createAndSendResetCode(User $user): void
    {
        $now = Time::now();
        $codes = model(PasswordResetCodeModel::class);
        $codes->deleteExpired($now->toDateTimeString());
        $codes->deleteForUser((int) $user->id);

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $codes->insert([
            'user_id' => (int) $user->id,
            'code_hash' => password_hash($code, PASSWORD_DEFAULT),
            'failed_attempts' => 0,
            'expires_at' => $now->addMinutes(self::CODE_LIFETIME_MINUTES)->toDateTimeString(),
            'created_at' => $now->toDateTimeString(),
        ]);

        $emailConfig = config(EmailConfig::class);
        $email = Services::email($emailConfig, false);
        $email->setMailType('html');
        $email->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?: 'Duno');
        $email->setTo($user->email);
        $email->setSubject(lang('Registration.resetEmailSubject'));
        $email->setMessage(view('Email/password_reset', [
            'code' => $code,
            'expiresMinutes' => self::CODE_LIFETIME_MINUTES,
        ], ['debug' => false]));

        try {
            if (! $email->send(false)) {
                log_message('error', 'Duno password reset code email could not be sent. Check the configured mail transport.');
            }
        } catch (\Throwable $exception) {
            log_message('error', 'Duno password reset code email could not be sent. Check the configured mail transport.');
        }

        $email->clear();
    }
}
