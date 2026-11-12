<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Catatan percobaan login admin.
 * PRD: 4.2 (login_attempts), NFR-005
 *
 *   - record(string $ip, ?string $username), countRecent(string $ip, int $minutes): int, clear(string $ip), prune()
 */
final class LoginAttempt
{
    /** Catat satu percobaan gagal. */
    public static function record(string $ip, ?string $username): void
    {
        Database::execute(
            'INSERT INTO login_attempts (ip_address, username) VALUES (?, ?)',
            [mb_substr($ip, 0, 45), $username !== null ? mb_substr($username, 0, 50) : null]
        );
    }

    /** Jumlah percobaan gagal dari IP ini dalam $minutes menit terakhir. */
    public static function countRecent(string $ip, int $minutes): int
    {
        return (int) Database::fetchValue(
            'SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempted_at > NOW() - INTERVAL ? MINUTE',
            [$ip, $minutes]
        );
    }

    /** Hapus catatan IP ini setelah login berhasil. */
    public static function clear(string $ip): void
    {
        Database::execute('DELETE FROM login_attempts WHERE ip_address = ?', [$ip]);
    }

    /** Hapus catatan lebih dari 1 hari (dipanggil oportunistik saat login, PRD 2.2.4). */
    public static function prune(): void
    {
        Database::execute('DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 1 DAY');
    }
}
