<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;

final class LogoutController extends BaseController
{
    public function logoutAction(): RedirectResponse
    {
        $url = config('Auth')->logoutRedirect();
        auth()->logout();

        return redirect()->to($url)->with('message', lang('App.loggedOutShort'));
    }
}
