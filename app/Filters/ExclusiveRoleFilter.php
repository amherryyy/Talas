<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Enforces the MVP's mutually exclusive student and admin route boundaries.
 */
class ExclusiveRoleFilter implements FilterInterface
{
    /**
     * @param list<string>|null $arguments
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $role = $arguments[0] ?? null;
        $user = auth()->user();

        $allowed = match ($role) {
            'student' => $user !== null && $user->inGroup('student') && ! $user->inGroup('admin'),
            'admin'   => $user !== null && $user->inGroup('admin'),
            default   => false,
        };

        if (! $allowed) {
            return service('response')->setStatusCode(403)->setBody('Forbidden');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No post-processing is required.
    }
}
