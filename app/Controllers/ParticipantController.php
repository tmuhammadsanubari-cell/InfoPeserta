<?php

declare(strict_types=1);

namespace App\Controllers;

/**
 * Registrasi, masuk, dan keluar peserta.
 * PRD: 2.2.1, 2.2.2
 *
 * TODO:
 *   - showRegister(): form registrasi; jika registration_open = 0 tampilkan pesan ditutup
 *   - register(): validasi, normalize_phone, cek duplikat (→ /masuk?phone=), insert, ParticipantAuth::login, redirect /info
 *   - showLogin(): form masuk, prefill ?phone=
 *   - login(): normalize_phone, cari peserta, tidak ada → pesan + link daftar, ada → login + redirect /info
 *   - logout(): ParticipantAuth::logout, redirect /
 */
final class ParticipantController
{
}
