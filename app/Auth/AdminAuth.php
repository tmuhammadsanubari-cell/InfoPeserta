<?php

declare(strict_types=1);

namespace App\Auth;

/**
 * Autentikasi admin dengan rate limit dan timeout.
 * PRD: 2.2.4, 4.4
 *
 * TODO:
 *   - static attempt(string $username, string $password, string $ip): bool|string — cek login_attempts (LOGIN_MAX_ATTEMPTS / LOGIN_LOCK_MINUTES), password_verify
 *   - static check(): bool — sesi admin ada dan belum idle > ADMIN_IDLE_MINUTES; perbarui waktu aktivitas
 *   - static id(): ?int dan static logout(): void
 *   - Key sesi admin terpisah dari sesi peserta
 *   - Hapus baris login_attempts lebih dari 1 hari secara oportunistik
 */
final class AdminAuth
{
    /**
     * Versi sementara (Hari 1) agar middleware Router bisa jalan: hanya membaca sesi PHP.
     * TODO (TASKS Hari 3): timeout idle ADMIN_IDLE_MINUTES dan perbarui waktu aktivitas.
     */
    public static function check(): bool
    {
        return is_int($_SESSION['admin_id'] ?? null);
    }
}
