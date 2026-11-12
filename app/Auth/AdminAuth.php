<?php

declare(strict_types=1);

namespace App\Auth;

use App\Core\Flash;
use App\Models\Admin;
use App\Models\LoginAttempt;

/**
 * Autentikasi admin dengan rate limit dan timeout.
 * PRD: 2.2.4, 4.4
 *
 *   - static attempt(string $username, string $password, string $ip): bool|string — cek login_attempts (LOGIN_MAX_ATTEMPTS / LOGIN_LOCK_MINUTES), password_verify
 *   - static check(): bool — sesi admin ada dan belum idle > ADMIN_IDLE_MINUTES; perbarui waktu aktivitas
 *   - static id(): ?int dan static logout(): void
 *   - Key sesi admin terpisah dari sesi peserta
 *   - Hapus baris login_attempts lebih dari 1 hari secara oportunistik
 */
final class AdminAuth
{
    public const LOCKED = 'locked';
    public const INVALID = 'invalid';

    private const KEY_ID = 'admin_id';
    private const KEY_ACTIVITY = 'admin_last_activity';

    /** Hash pengganti agar waktu respons sama saat username tidak ada. */
    private const DUMMY_HASH = '$2y$10$97wx8F0/fzqht7sSqoATm.Y.Mq4txvqk8YEkA1MqJECTd5PQcdoDm';

    /**
     * @return true|string true jika berhasil, AdminAuth::LOCKED jika terkunci, AdminAuth::INVALID jika salah
     */
    public static function attempt(string $username, string $password, string $ip): bool|string
    {
        $max = max(1, (int) config('LOGIN_MAX_ATTEMPTS', 5));
        $lockMinutes = max(1, (int) config('LOGIN_LOCK_MINUTES', 15));

        LoginAttempt::prune();

        if (LoginAttempt::countRecent($ip, $lockMinutes) >= $max) {
            return self::LOCKED;
        }

        $admin = $username !== '' ? Admin::findByUsername($username) : null;
        $valid = password_verify($password, $admin['password_hash'] ?? self::DUMMY_HASH) && $admin !== null;

        if (!$valid) {
            LoginAttempt::record($ip, $username !== '' ? $username : null);
            return self::INVALID;
        }

        $id = (int) $admin['id'];
        if (password_needs_rehash($admin['password_hash'], PASSWORD_DEFAULT)) {
            Admin::updatePassword($id, password_hash($password, PASSWORD_DEFAULT));
        }

        session_regenerate_id(true);
        $_SESSION[self::KEY_ID] = $id;
        $_SESSION[self::KEY_ACTIVITY] = time();

        Admin::touchLogin($id);
        LoginAttempt::clear($ip);

        return true;
    }

    /** Sesi admin aktif dan belum idle melewati ADMIN_IDLE_MINUTES. Memperbarui waktu aktivitas. */
    public static function check(): bool
    {
        $id = $_SESSION[self::KEY_ID] ?? null;
        if (!is_int($id)) {
            return false;
        }

        $idle = max(1, (int) config('ADMIN_IDLE_MINUTES', 120)) * 60;
        $last = (int) ($_SESSION[self::KEY_ACTIVITY] ?? 0);
        if (time() - $last > $idle) {
            self::logout();
            Flash::set('info', 'Sesi admin berakhir karena tidak ada aktivitas. Silakan masuk lagi.');
            return false;
        }

        $_SESSION[self::KEY_ACTIVITY] = time();
        return true;
    }

    public static function id(): ?int
    {
        $id = $_SESSION[self::KEY_ID] ?? null;
        return is_int($id) ? $id : null;
    }

    /** Data admin yang sedang masuk, atau null. */
    public static function user(): ?array
    {
        static $user = false;
        if ($user === false) {
            $id = self::id();
            $user = $id !== null ? Admin::find($id) : null;
        }
        return $user;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::KEY_ID], $_SESSION[self::KEY_ACTIVITY]);
        session_regenerate_id(true);
    }
}
