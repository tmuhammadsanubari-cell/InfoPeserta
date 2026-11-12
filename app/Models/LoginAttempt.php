<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Catatan percobaan login admin.
 * PRD: 4.2 (login_attempts), NFR-005
 *
 * TODO:
 *   - record(string $ip, ?string $username), countRecent(string $ip, int $minutes): int, clear(string $ip), prune()
 */
final class LoginAttempt
{
}
