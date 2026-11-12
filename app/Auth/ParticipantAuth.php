<?php

declare(strict_types=1);

namespace App\Auth;

/**
 * Autentikasi peserta: sesi PHP + cookie remember 30 hari.
 * PRD: 2.2.2, 4.4
 *
 * TODO:
 *   - static login(int $participantId): void — session_regenerate_id, buat token random_bytes(32), simpan sha256 di participant_sessions, set cookie {session_id}:{token}
 *   - static id(): ?int — dari sesi; jika kosong, pulihkan dari cookie (cek expires_at, hash_equals)
 *   - static user(): ?array — data peserta + nama departemen
 *   - static logout(): void — hapus baris participant_sessions terkait dan cookie
 *   - Cookie: HttpOnly, SameSite=Lax, Secure jika HTTPS
 */
final class ParticipantAuth
{
    /**
     * Versi sementara (Hari 1) agar middleware Router bisa jalan: hanya membaca sesi PHP.
     * TODO (TASKS Hari 2): pulihkan sesi dari cookie remember.
     */
    public static function id(): ?int
    {
        $id = $_SESSION['participant_id'] ?? null;
        return is_int($id) ? $id : null;
    }
}
