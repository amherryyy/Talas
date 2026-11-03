<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/** Adds endpoint-specific IP and identity limits to authentication submissions. */
final class AuthAbuseProtectionFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! $request instanceof IncomingRequest || strtoupper($request->getMethod()) !== 'POST') {
            return null;
        }

        $matchedRoute = service('router')->getMatchedRoute();
        $route = strtolower(trim((string) ($matchedRoute[0] ?? ''), '/'));
        $ip = $request->getIPAddress();
        $emailInput = $request->getPost('email');
        $email = is_string($emailInput) ? strtolower(trim($emailInput)) : '';
        $identifier = $email === '' ? 'empty' : hash_hmac(
            'sha256',
            $email,
            (string) config('Encryption')->key,
        );

        $limits = match (true) {
            $route === 'login' => [
                ['login-ip:' . $ip, 30, 300],
                ['login-identity:' . $identifier, 10, 900],
            ],
            $route === 'register' => [
                ['register-ip:' . $ip, 30, 3600],
                ['register-identity:' . $identifier, 3, 3600],
            ],
            $route === 'forgot-password' => [
                ['recovery-ip:' . $ip, 20, 3600],
                ['recovery-identity:' . $identifier, 3, 3600],
            ],
            $route === 'reset-password' => [
                ['reset-ip:' . $ip, 30, 3600],
                ['reset-identity:' . $identifier, 10, 3600],
            ],
            default => [],
        };

        $throttler = service('throttler');
        foreach ($limits as [$key, $capacity, $seconds]) {
            if (! $throttler->check('duno-auth-' . hash('sha256', $key), $capacity, $seconds, 1)) {
                return service('response')
                    ->setStatusCode(429)
                    ->setBody(lang('Auth.throttled', [$throttler->getTokenTime()]));
            }
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
