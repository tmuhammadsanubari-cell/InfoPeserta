<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Token sesi peserta (cookie remember).
 * PRD: 4.2 (participant_sessions)
 *
 *   - create(int $participantId, string $tokenHash, ?string $ua, string $expiresAt): int
 *   - findValid(int $id): ?array, delete(int $id), deleteExpired()
 */
final class ParticipantSession
{
    public static function create(int $participantId, string $tokenHash, ?string $ua, string $expiresAt): int
    {
        Database::execute(
            'INSERT INTO participant_sessions (participant_id, token_hash, user_agent, expires_at) VALUES (?, ?, ?, ?)',
            [$participantId, $tokenHash, $ua !== null ? mb_substr($ua, 0, 255) : null, $expiresAt]
        );
        return Database::lastInsertId();
    }

    /** Baris sesi yang belum kedaluwarsa, atau null. */
    public static function findValid(int $id): ?array
    {
        return Database::fetch(
            'SELECT id, participant_id, token_hash, expires_at FROM participant_sessions WHERE id = ? AND expires_at > NOW()',
            [$id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM participant_sessions WHERE id = ?', [$id]);
    }

    public static function deleteExpired(): void
    {
        Database::execute('DELETE FROM participant_sessions WHERE expires_at <= NOW()');
    }
}
