<?php

namespace App\Models;

use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Model;
use CodeIgniter\I18n\Time;

class RegistrationVerificationLimitModel extends Model
{
    public const MAX_FAILED_ATTEMPTS = 5;
    public const MAX_DAILY_SENDS = 5;

    protected $table = 'registration_verification_limits';
    protected $primaryKey = 'user_id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id', 'failed_attempts', 'last_sent_at', 'sends_today', 'send_day', 'created_at', 'updated_at',
    ];
    protected $useTimestamps = false;

    /** @return array<string, mixed>|null */
    public function findForUser(int $userId): ?array
    {
        if ($userId < 1) {
            return null;
        }

        $row = $this->where('user_id', $userId)->first();

        return is_array($row) ? $row : null;
    }

    public function ensureForUser(int $userId): bool
    {
        if ($userId < 1) {
            return false;
        }
        if ($this->findForUser($userId) !== null) {
            return true;
        }

        try {
            $this->insert([
                'user_id' => $userId,
                'failed_attempts' => 0,
                'sends_today' => 0,
                'created_at' => Time::now()->toDateTimeString(),
                'updated_at' => Time::now()->toDateTimeString(),
            ]);
        } catch (DatabaseException) {
            // A simultaneous registration flow may have created the unique row.
        }

        return $this->findForUser($userId) !== null;
    }

    /**
     * Reserves an email send atomically. Initial mail counts toward the daily cap.
     *
     * @return 'sent'|'cooldown'|'daily_limit'|'missing'
     */
    public function reserveEmailSend(int $userId, Time $now): string
    {
        $row = $this->findForUser($userId);
        if ($row === null) {
            return 'missing';
        }

        $today = $now->toDateString();
        $nowString = $now->toDateTimeString();
        $cooldownEnds = $now->subMinutes(1)->toDateTimeString();
        $db = $this->db;

        $builder = $db->table($this->table)
            ->set('sends_today', 'CASE WHEN send_day = ' . $db->escape($today) . ' THEN sends_today + 1 ELSE 1 END', false)
            ->set('send_day', $today)
            ->set('last_sent_at', $nowString)
            ->set('updated_at', $nowString)
            ->where('user_id', $userId)
            ->groupStart()
                ->where('last_sent_at', null)
                ->orWhere('last_sent_at <=', $cooldownEnds)
            ->groupEnd()
            ->groupStart()
                ->where('send_day !=', $today)
                ->orWhere('send_day', null)
                ->orWhere('sends_today <', self::MAX_DAILY_SENDS)
            ->groupEnd();

        if ($builder->update() && $db->affectedRows() > 0) {
            return 'sent';
        }

        $row = $this->findForUser($userId);
        if ($row === null) {
            return 'missing';
        }
        if ($row['send_day'] === $today && (int) $row['sends_today'] >= self::MAX_DAILY_SENDS) {
            return 'daily_limit';
        }

        return 'cooldown';
    }

    public function incrementFailedAttempts(int $userId): int
    {
        if ($userId < 1) {
            return 5;
        }

        $this->db->table($this->table)
            ->set('failed_attempts', 'failed_attempts + 1', false)
            ->set('updated_at', Time::now()->toDateTimeString())
            ->where('user_id', $userId)
            ->where('failed_attempts <', self::MAX_FAILED_ATTEMPTS)
            ->update();

        return (int) ($this->findForUser($userId)['failed_attempts'] ?? 5);
    }

    public function resetFailedAttempts(int $userId): bool
    {
        return $this->where('user_id', $userId)->update(null, [
            'failed_attempts' => 0,
            'updated_at' => Time::now()->toDateTimeString(),
        ]);
    }

    public function deleteForUser(int $userId): bool
    {
        return $this->where('user_id', $userId)->delete();
    }
}
