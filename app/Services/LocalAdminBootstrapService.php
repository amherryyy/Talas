<?php

namespace App\Services;

use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;
use Config\Auth;
use RuntimeException;

class LocalAdminBootstrapService
{
    public static function isAllowedEnvironment(string $environment, string $databaseHost): bool
    {
        return $environment === 'development'
            && in_array($databaseHost, ['localhost', '127.0.0.1', '::1'], true);
    }

    public function adminExists(): bool
    {
        $groupsUsersTable = config(Auth::class)->tables['groups_users'];

        return db_connect()->table($groupsUsersTable)
            ->where('group', 'admin')
            ->countAllResults() > 0;
    }

    /**
     * Create a single admin with a generated one-time password.
     *
     * @return array{user_id: int, password: string}|null Null when an admin already exists.
     */
    public function create(string $username, string $email): ?array
    {
        if ($this->adminExists()) {
            return null;
        }

        $db = db_connect();
        // 18 random bytes produce a manageable 24-character password, above
        // Shield's configured 8-character minimum and below bcrypt's 72-byte cap.
        $password = rtrim(strtr(base64_encode(random_bytes(18)), '+/', '-_'), '=');
        $db->transBegin();

        try {
            $users = new UserModel();
            $userId = $users->insert(new User([
                'username' => $username,
                'email' => $email,
                'password' => $password,
            ]), true);

            if (! is_numeric($userId) || (int) $userId < 1) {
                throw new RuntimeException('The admin account could not be created.');
            }

            $user = $users->findById((int) $userId);
            if ($user === null) {
                throw new RuntimeException('The new admin account could not be loaded.');
            }

            $user->syncGroups('admin');
            if (! $user->inGroup('admin') || $user->inGroup('student')) {
                throw new RuntimeException('The new account did not receive the expected admin-only role.');
            }

            $db->transCommit();

            return ['user_id' => (int) $userId, 'password' => $password];
        } catch (\Throwable $exception) {
            $db->transRollback();
            throw $exception;
        }
    }
}
