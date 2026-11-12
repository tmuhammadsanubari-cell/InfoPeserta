<?php

declare(strict_types=1);

namespace App\Auth;

use App\Models\Participant;
use App\Models\ParticipantSession;

/**
 * Autentikasi peserta: sesi PHP + cookie remember 30 hari.
 * PRD: 2.2.2, 4.4
 *
 *   - static login(int $participantId): void — session_regenerate_id, buat token random_bytes(32), simpan sha256 di participant_sessions, set cookie {session_id}:{token}
 *   - static id(): ?int — dari sesi; jika kosong, pulihkan dari cookie (cek expires_at, hash_equals)
 *   - static user(): ?array — data peserta + nama departemen
 *   - static logout(): void — hapus baris participant_sessions terkait dan cookie
 *   - Cookie: HttpOnly, SameSite=Lax, Secure jika HTTPS
 *
 * Key sesi peserta ('participant_*') terpisah dari key sesi admin.
 */
final class ParticipantAuth
{
    private const KEY_ID = 'participant_id';
    private const KEY_SESSION_ROW = 'participant_session_row';

    /** Hasil user() per request; false = belum dihitung. */
    private static array|null|false $user = false;

    public static function login(int $participantId): void
    {
        session_regenerate_id(true);

        $token = bin2hex(random_bytes(32));
        $days = max(1, (int) config('REMEMBER_DAYS', 30));
        $expiresTs = time() + $days * 86400;

        ParticipantSession::deleteExpired();
        $rowId = ParticipantSession::create(
            $participantId,
            hash('sha256', $token),
            isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : null,
            date('Y-m-d H:i:s', $expiresTs)
        );

        $_SESSION[self::KEY_ID] = $participantId;
        $_SESSION[self::KEY_SESSION_ROW] = $rowId;
        Participant::touchLogin($participantId);

        self::setCookie($rowId . ':' . $token, $expiresTs);
        self::$user = false;
    }

    public static function id(): ?int
    {
        $user = self::user();
        return $user !== null ? (int) $user['id'] : null;
    }

    /** Peserta yang sedang masuk (dengan department_name), atau null. */
    public static function user(): ?array
    {
        if (self::$user !== false) {
            return self::$user;
        }

        $user = null;
        $id = $_SESSION[self::KEY_ID] ?? null;

        if (is_int($id)) {
            $user = Participant::find($id);
            if ($user === null) {
                // Peserta sudah dihapus admin: bersihkan sesi.
                self::forget();
            }
        } else {
            $user = self::restoreFromCookie();
        }

        return self::$user = $user;
    }

    /** Wajib masuk; jika tidak, arahkan ke halaman awal. */
    public static function requireParticipant(): array
    {
        $user = self::user();
        if ($user === null) {
            redirect('/');
        }
        return $user;
    }

    public static function logout(): void
    {
        $rowId = $_SESSION[self::KEY_SESSION_ROW] ?? null;
        if (is_int($rowId)) {
            ParticipantSession::delete($rowId);
        }

        // Cookie bisa merujuk baris lain (misalnya sesi PHP dibuat ulang): hapus juga jika tokennya cocok.
        $row = self::validCookieRow();
        if ($row !== null) {
            ParticipantSession::delete((int) $row['id']);
        }

        self::forget();
        session_regenerate_id(true);
    }

    /** Pulihkan sesi PHP dari cookie remember yang valid. */
    private static function restoreFromCookie(): ?array
    {
        $row = self::validCookieRow();
        if ($row === null) {
            if (isset($_COOKIE[self::cookieName()])) {
                self::clearCookie();
            }
            return null;
        }

        $user = Participant::find((int) $row['participant_id']);
        if ($user === null) {
            self::clearCookie();
            return null;
        }

        session_regenerate_id(true);
        $_SESSION[self::KEY_ID] = (int) $user['id'];
        $_SESSION[self::KEY_SESSION_ROW] = (int) $row['id'];

        return $user;
    }

    /** Baris participant_sessions dari cookie jika format, masa berlaku, dan hash token cocok. */
    private static function validCookieRow(): ?array
    {
        $raw = $_COOKIE[self::cookieName()] ?? null;
        if (!is_string($raw) || !preg_match('/^(\d{1,10}):([a-f0-9]{64})$/', $raw, $m)) {
            return null;
        }

        $row = ParticipantSession::findValid((int) $m[1]);
        if ($row === null || !hash_equals((string) $row['token_hash'], hash('sha256', $m[2]))) {
            return null;
        }

        return $row;
    }

    private static function forget(): void
    {
        unset($_SESSION[self::KEY_ID], $_SESSION[self::KEY_SESSION_ROW]);
        self::clearCookie();
        self::$user = null;
    }

    private static function cookieName(): string
    {
        return (string) config('REMEMBER_COOKIE', 'pip_remember');
    }

    private static function setCookie(string $value, int $expires): void
    {
        setcookie(self::cookieName(), $value, [
            'expires'  => $expires,
            'path'     => base_path() . '/',
            'secure'   => is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        $_COOKIE[self::cookieName()] = $value;
    }

    private static function clearCookie(): void
    {
        setcookie(self::cookieName(), '', [
            'expires'  => time() - 3600,
            'path'     => base_path() . '/',
            'secure'   => is_https(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
        unset($_COOKIE[self::cookieName()]);
    }
}
