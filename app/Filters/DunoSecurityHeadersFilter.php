<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Adds shared browser security headers, including HSTS on production HTTPS responses. */
final class DunoSecurityHeadersFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = service('response');
        (new SecureHeaders())->after($request, $response);

        if (ENVIRONMENT === 'production' && method_exists($request, 'isSecure') && $request->isSecure()) {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $response = (new SecureHeaders())->after($request, $response);

        if (ENVIRONMENT === 'production' && method_exists($request, 'isSecure') && $request->isSecure()) {
            $response->setHeader('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
