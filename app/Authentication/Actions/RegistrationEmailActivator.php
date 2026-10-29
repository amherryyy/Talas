<?php

declare(strict_types=1);

namespace App\Authentication\Actions;

use CodeIgniter\I18n\Time;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Shield\Authentication\Actions\EmailActivator;
use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Exceptions\RuntimeException;
use CodeIgniter\Shield\Models\UserIdentityModel;
use Config\Email as EmailConfig;
use Config\Services;

/** Email activation codes expire after 15 minutes. */
class RegistrationEmailActivator extends EmailActivator
{
    private const CODE_LIFETIME_MINUTES = 15;

    public function createIdentity(User $user): string
    {
        $limits = model(\App\Models\RegistrationVerificationLimitModel::class);
        if (! $limits->ensureForUser((int) $user->id)) {
            throw new RuntimeException('Registration verification limits are unavailable. Apply the registration verification migration.');
        }

        $identityModel = model(UserIdentityModel::class);
        $identityModel->deleteIdentitiesByType($user, Session::ID_TYPE_EMAIL_ACTIVATE);
        $code = $identityModel->createCodeIdentity(
            $user,
            [
                'type' => Session::ID_TYPE_EMAIL_ACTIVATE,
                'name' => 'register',
                'extra' => lang('Auth.needVerification'),
            ],
            static fn (): string => (string) random_int(100000, 999999),
        );
        $identity = $identityModel->getIdentityByType($user, Session::ID_TYPE_EMAIL_ACTIVATE);

        if ($identity === null || ! $identityModel->setIdentityExpirationById(
            $identity->id,
            $user,
            Time::now()->addMinutes(self::CODE_LIFETIME_MINUTES),
        )) {
            throw new RuntimeException('Could not set the registration verification code expiration.');
        }

        return $code;
    }

    public function show(): string
    {
        $authenticator = auth('session')->getAuthenticator();
        $user = $authenticator->getPendingUser();
        if ($user === null || $user->email === null) {
            throw new RuntimeException('Cannot get the pending registration user.');
        }

        $limits = model(\App\Models\RegistrationVerificationLimitModel::class);
        $state = $limits->findForUser((int) $user->id);
        if ($state !== null && $state['last_sent_at'] !== null) {
            return $this->view(setting('Auth.views')['action_email_activate_show'], ['user' => $user]);
        }

        return $this->sendCode($user, false);
    }

    public function resendCode(): string
    {
        $user = auth('session')->getAuthenticator()->getPendingUser();
        if ($user === null || $user->email === null) {
            throw new RuntimeException('Cannot get the pending registration user.');
        }

        return $this->sendCode($user, true);
    }

    private function sendCode(User $user, bool $isResend): string
    {
        $limits = model(\App\Models\RegistrationVerificationLimitModel::class);
        $reservation = $limits->reserveEmailSend((int) $user->id, Time::now());
        if ($reservation !== 'sent') {
            $message = match ($reservation) {
                'cooldown' => lang('Registration.resendCooldown'),
                'daily_limit' => lang('Registration.dailyLimit'),
                default => lang('Registration.unavailable'),
            };
            session()->setFlashdata('error', $message);

            return $this->view(setting('Auth.views')['action_email_activate_show'], ['user' => $user]);
        }

        $identityModel = model(UserIdentityModel::class);
        $identity = $identityModel->getIdentityByType($user, Session::ID_TYPE_EMAIL_ACTIVATE);
        if ($isResend || $identity === null || $identity->expires === null
            || Time::parse((string) $identity->expires)->getTimestamp() <= Time::now()->getTimestamp()) {
            $code = $this->createIdentity($user);
            $limits->resetFailedAttempts((int) $user->id);
        } else {
            $code = (string) $identity->secret;
        }

        $emailConfig = config(EmailConfig::class);
        $email = Services::email($emailConfig, false)
            ->setMailType('html')
            ->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?: 'Duno');
        $email->setTo($user->email);
        $email->setSubject(lang('Registration.emailSubject'));
        $email->setMessage($this->view(
            setting('Auth.views')['action_email_activate_email'],
            ['code' => $code],
            ['debug' => false],
        ));

        if ($email->send(false) === false) {
            throw new RuntimeException('Registration verification email could not be sent. Check the configured mail transport.');
        }

        $email->clear();

        return $this->view(setting('Auth.views')['action_email_activate_show'], ['user' => $user]);
    }

    public function verify(IncomingRequest $request)
    {
        $authenticator = auth('session')->getAuthenticator();
        $user = $authenticator->getPendingUser();

        if ($user !== null) {
            $limits = model(\App\Models\RegistrationVerificationLimitModel::class);
            $state = $limits->findForUser((int) $user->id);
            if ($state === null) {
                throw new RuntimeException('Registration verification limits are unavailable. Apply the registration verification migration.');
            }

            if ((int) $state['failed_attempts'] >= \App\Models\RegistrationVerificationLimitModel::MAX_FAILED_ATTEMPTS) {
                session()->setFlashdata('error', lang('Registration.attemptsExceeded'));

                return $this->view(setting('Auth.views')['action_email_activate_show'], ['user' => $user]);
            }

            $identity = model(UserIdentityModel::class)->getIdentityByType(
                $user,
                Session::ID_TYPE_EMAIL_ACTIVATE,
            );
            $expiresAt = $identity?->expires;
            $expired = $expiresAt === null
                || Time::parse((string) $expiresAt)->getTimestamp() <= Time::now()->getTimestamp();

            if ($expired) {
                session()->setFlashdata('error', lang('Registration.codeExpired'));

                return $this->view(setting('Auth.views')['action_email_activate_show'], ['user' => $user]);
            }

            $postedToken = (string) $request->getPost('token');
            if (preg_match('/\A[0-9]{6}\z/', $postedToken) !== 1
                || ! hash_equals((string) $identity->secret, $postedToken)) {
                $attempts = $limits->incrementFailedAttempts((int) $user->id);
                $message = $attempts >= \App\Models\RegistrationVerificationLimitModel::MAX_FAILED_ATTEMPTS
                    ? lang('Registration.attemptsExceeded')
                    : lang('Auth.invalidActivateToken');
                session()->setFlashdata('error', $message);

                return $this->view(setting('Auth.views')['action_email_activate_show'], ['user' => $user]);
            }
        }

        $result = parent::verify($request);
        if ($user !== null && $authenticator->loggedIn()) {
            model(\App\Models\RegistrationVerificationLimitModel::class)->deleteForUser((int) $user->id);
        }

        return $result;
    }
}
