<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Keeps a shared-IP burst from blocking evaluation users too quickly. */
final class AuthPageRateFilter implements FilterInterface
{
    private const REQUESTS_PER_MINUTE = 60;

    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest) {
            return null;
        }

        $throttler = service('throttler');
        $key = 'duno-auth-page-' . hash('sha256', $request->getIPAddress());

        if (! $throttler->check($key, self::REQUESTS_PER_MINUTE, MINUTE, 1)) {
            return service('response')
                ->setStatusCode(429)
                ->setBody(lang('Auth.throttled', [$throttler->getTokenTime()]));
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
