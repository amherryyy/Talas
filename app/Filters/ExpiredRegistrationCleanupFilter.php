<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Shield\Authentication\Authenticators\Session;

/** Removes old, inactive signups when a visitor opens registration. */
class ExpiredRegistrationCleanupFilter implements FilterInterface
{
    private const REGISTRATION_RETENTION_HOURS = 48;
    private const MAX_USERS_PER_REQUEST = 100;

    public function before(RequestInterface $request, $arguments = null)
    {
        $db = db_connect(config('Auth')->DBGroup);
        $userTable = config('Auth')->tables['users'];
        $identityTable = config('Auth')->tables['identities'];
        $cutoff = Time::now()->subHours(self::REGISTRATION_RETENTION_HOURS)->toDateTimeString();

        $expired = $db->table($userTable . ' u')
            ->select('u.id')
            ->join($identityTable . ' i', 'i.user_id = u.id')
            ->where('u.active', 0)
            ->where('u.created_at <=', $cutoff)
            ->where('i.type', Session::ID_TYPE_EMAIL_ACTIVATE)
            ->orderBy('u.created_at', 'ASC')
            ->limit(self::MAX_USERS_PER_REQUEST)
            ->get()->getResultArray();

        $userIds = array_map(static fn (array $row): int => (int) $row['id'], $expired);
        if ($userIds !== []) {
            $db->table($userTable)->whereIn('id', $userIds)->delete();
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
    }
}
