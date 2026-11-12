<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth\AdminAuth;
use App\Core\Flash;
use App\Core\View;
use App\Models\Admin;

/**
 * Ganti password admin.
 * PRD: 2.2.4 (ganti password)
 *
 *   - edit(), update(): password lama benar, baru minimal 8 karakter, konfirmasi cocok
 */
final class AccountController
{
    public function edit(): void
    {
        View::render('admin/account/edit', [
            'title' => 'Ganti Password',
            'admin' => AdminAuth::user(),
        ], 'admin');
    }

    public function update(): void
    {
        $admin = AdminAuth::user();
        if ($admin === null) {
            redirect('/admin/login');
        }

        $current = (string) ($_POST['current_password'] ?? '');
        $new = (string) ($_POST['new_password'] ?? '');
        $confirm = (string) ($_POST['new_password_confirmation'] ?? '');

        $errors = [];
        if ($current === '') {
            $errors['current_password'] = 'Password lama wajib diisi.';
        } elseif (!password_verify($current, (string) $admin['password_hash'])) {
            $errors['current_password'] = 'Password lama salah.';
        }

        if ($new === '') {
            $errors['new_password'] = 'Password baru wajib diisi.';
        } elseif (mb_strlen($new) < 8) {
            $errors['new_password'] = 'Password baru minimal 8 karakter.';
        } elseif (strlen($new) > 72) {
            $errors['new_password'] = 'Password baru maksimal 72 karakter.'; // batas bcrypt
        } elseif ($current !== '' && hash_equals($current, $new)) {
            $errors['new_password'] = 'Password baru harus berbeda dari password lama.';
        }

        if (!isset($errors['new_password']) && !hash_equals($new, $confirm)) {
            $errors['new_password_confirmation'] = 'Konfirmasi password tidak cocok.';
        }

        if ($errors !== []) {
            Flash::withErrors($errors);
            redirect('/admin/akun');
        }

        Admin::updatePassword((int) $admin['id'], password_hash($new, PASSWORD_DEFAULT));
        session_regenerate_id(true);

        Flash::set('success', 'Password berhasil diganti. Gunakan password baru saat login berikutnya.');
        redirect('/admin/akun');
    }
}
