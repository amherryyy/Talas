<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetCodeModel extends Model
{
    public const MAX_FAILED_ATTEMPTS = 5;

    protected $table = 'password_reset_codes';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['user_id', 'code_hash', 'failed_attempts', 'expires_at', 'created_at'];
    protected $useTimestamps = false;

    /** @return array<string, mixed>|null */
    public function findActiveForUser(int $userId, string $now): ?array
    {
        $row = $this->where('user_id', $userId)
            ->where('expires_at >', $now)
            ->where('failed_attempts <', self::MAX_FAILED_ATTEMPTS)
            ->first();

        return is_array($row) ? $row : null;
    }

    public function deleteForUser(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete();
    }

    public function deleteById(int $id): bool
    {
        return $this->where('id', $id)->delete();
    }

    public function deleteExpired(string $now): bool
    {
        return $this->where('expires_at <=', $now)->delete();
    }

    public function incrementFailedAttempts(int $id, string $now): int
    {
        $this->db->table($this->table)
            ->set('failed_attempts', 'failed_attempts + 1', false)
            ->where('id', $id)
            ->where('expires_at >', $now)
            ->where('failed_attempts <', self::MAX_FAILED_ATTEMPTS)
            ->update();

        $row = $this->find($id);
        if (! is_array($row)) {
            return self::MAX_FAILED_ATTEMPTS;
        }

        $attempts = (int) $row['failed_attempts'];
        if ($attempts >= self::MAX_FAILED_ATTEMPTS) {
            $this->deleteById($id);
        }

        return $attempts;
    }

    /** Atomically removes a still-valid code after it has been checked. */
    public function consumeActive(int $id, string $now): bool
    {
        $this->db->table($this->table)
            ->where('id', $id)
            ->where('expires_at >', $now)
            ->where('failed_attempts <', self::MAX_FAILED_ATTEMPTS)
            ->delete();

        return $this->db->affectedRows() === 1;
    }
}
