<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Requires the registration agreement before Shield can create an account. */
class RegistrationConsentFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (strtoupper($request->getMethod()) !== 'POST') {
            return null;
        }

        if ($request->getPost('terms_privacy_consent') === '1') {
            return null;
        }

        return redirect()->back()->withInput()->with('errors', [
            'terms_privacy_consent' => lang('App.registrationConsentRequired'),
        ]);
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
