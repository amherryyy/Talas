<?php

namespace App\Controllers;

use CodeIgniter\Shield\Authentication\Authenticators\Session;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\Shield\Models\UserModel;

class RegistrationRecovery extends BaseController
{
    public function resend()
    {
        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();
        $action = $authenticator->getAction();

        if (! $action instanceof \App\Authentication\Actions\RegistrationEmailActivator) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }

        return $action->resendCode();
    }

    public function cancel()
    {
        /** @var Session $authenticator */
        $authenticator = auth('session')->getAuthenticator();
        $user = $authenticator->getPendingUser();

        if ($user === null || $user->isActivated()) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }

        $identities = model(UserIdentityModel::class);
        if ($identities->getIdentityByType($user, Session::ID_TYPE_EMAIL_ACTIVATE) === null) {
            return $this->response->setStatusCode(404)->setBody('Not Found');
        }

        $db = db_connect(config('Auth')->DBGroup);
        $db->transBegin();
        try {
            model(\App\Models\RegistrationVerificationLimitModel::class)->deleteForUser((int) $user->id);
            if (! model(UserModel::class)->delete((int) $user->id, true)) {
                $db->transRollback();

                return redirect()->to(url_to('register'))->with('error', lang('Registration.restartFailed'));
            }
            $db->transCommit();
            $authenticator->logout();
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }

        return redirect()->to(url_to('register'));
    }
}
