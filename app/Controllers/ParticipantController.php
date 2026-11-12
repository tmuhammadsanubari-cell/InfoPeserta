<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\ParticipantAuth;
use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\Department;
use App\Models\Participant;
use PDOException;

/**
 * Registrasi, masuk, dan keluar peserta.
 * PRD: 2.2.1, 2.2.2
 *
 *   - showRegister(): form registrasi; jika registration_open = 0 tampilkan pesan ditutup
 *   - register(): validasi, normalize_phone, cek duplikat (→ /masuk?phone=), insert, ParticipantAuth::login, redirect /info
 *   - showLogin(): form masuk, prefill ?phone=
 *   - login(): normalize_phone, cari peserta, tidak ada → pesan + link daftar, ada → login + redirect /info
 *   - logout(): ParticipantAuth::logout, redirect /
 */
final class ParticipantController
{
    public const MSG_PHONE_INVALID = 'Nomor HP tidak valid. Contoh: 081234567890';
    public const MSG_PHONE_EXISTS = 'Nomor ini sudah terdaftar. Silakan masuk.';
    public const MSG_PHONE_NOT_FOUND = 'Nomor belum terdaftar.';

    public function showRegister(): void
    {
        $open = self::registrationOpen();

        View::render('participant/register', [
            'title'       => 'Daftar Peserta',
            'open'        => $open,
            'departments' => $open ? Department::active() : [],
        ]);
    }

    public function register(): void
    {
        if (!self::registrationOpen()) {
            redirect('/daftar');
        }

        $input = [
            'full_name'     => preg_replace('/\s+/u', ' ', trim((string) ($_POST['full_name'] ?? ''))) ?? '',
            'department_id' => trim((string) ($_POST['department_id'] ?? '')),
            'phone'         => trim((string) ($_POST['phone'] ?? '')),
        ];

        $errors = Validator::make($input, [
            'full_name'     => 'required|min:3|max:100',
            'department_id' => 'required',
            'phone'         => 'required',
        ], [
            'full_name'     => 'Nama lengkap',
            'department_id' => 'Departemen',
            'phone'         => 'Nomor HP',
        ]);

        if (!isset($errors['department_id'])) {
            $department = ctype_digit($input['department_id']) ? Department::find((int) $input['department_id']) : null;
            if ($department === null || (int) $department['is_active'] !== 1) {
                $errors['department_id'] = 'Pilih departemen dari daftar.';
            }
        }

        $phone = normalize_phone($input['phone']);
        if (!isset($errors['phone']) && $phone === null) {
            $errors['phone'] = self::MSG_PHONE_INVALID;
        }

        if ($errors !== []) {
            Flash::withErrors($errors, $input);
            redirect('/daftar');
        }

        if (Participant::findByPhone($phone) !== null) {
            self::redirectPhoneExists($phone);
        }

        try {
            $id = Participant::create([
                'full_name'     => $input['full_name'],
                'department_id' => (int) $input['department_id'],
                'phone'         => $phone,
            ]);
        } catch (PDOException $e) {
            // Pengaman kedua: dua pendaftaran nomor yang sama hampir bersamaan (PRD 2.2.1).
            if (($e->errorInfo[1] ?? null) === 1062) {
                self::redirectPhoneExists($phone);
            }
            throw $e;
        }

        ParticipantAuth::login($id);
        redirect('/info');
    }

    public function showLogin(): void
    {
        $prefill = old('phone');
        if ($prefill === '' && isset($_GET['phone']) && is_string($_GET['phone'])) {
            $prefill = mb_substr(trim($_GET['phone']), 0, 20);
        }

        View::render('participant/login', [
            'title'    => 'Masuk',
            'phone'    => $prefill,
            'notFound' => error('phone') === self::MSG_PHONE_NOT_FOUND,
        ]);
    }

    public function login(): void
    {
        $raw = trim((string) ($_POST['phone'] ?? ''));
        $phone = normalize_phone($raw);

        if ($raw === '') {
            Flash::withErrors(['phone' => 'Nomor HP wajib diisi.'], ['phone' => $raw]);
            redirect('/masuk');
        }
        if ($phone === null) {
            Flash::withErrors(['phone' => self::MSG_PHONE_INVALID], ['phone' => $raw]);
            redirect('/masuk');
        }

        $participant = Participant::findByPhone($phone);
        if ($participant === null) {
            Flash::withErrors(['phone' => self::MSG_PHONE_NOT_FOUND], ['phone' => $raw]);
            redirect('/masuk');
        }

        ParticipantAuth::login((int) $participant['id']);
        redirect('/info');
    }

    public function logout(): void
    {
        ParticipantAuth::logout();
        redirect('/');
    }

    private static function registrationOpen(): bool
    {
        return setting('registration_open', '1') === '1';
    }

    private static function redirectPhoneExists(string $phone): never
    {
        Flash::set('info', self::MSG_PHONE_EXISTS);
        redirect('/masuk?phone=' . rawurlencode($phone));
    }
}
