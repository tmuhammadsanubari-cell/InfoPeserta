<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Token sesi peserta (cookie remember).
 * PRD: 4.2 (participant_sessions)
 *
 * TODO:
 *   - create(int $participantId, string $tokenHash, ?string $ua, string $expiresAt): int
 *   - findValid(int $id): ?array, delete(int $id), deleteExpired()
 */
final class ParticipantSession
{
}
