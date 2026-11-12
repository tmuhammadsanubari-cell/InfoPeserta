<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Auth\AdminAuth;
use App\Core\Flash;
use App\Core\View;

/**
 * Login/logout admin.
 * PRD: 2.2.4
 *
 *   - showLogin(), login() (429 jika terkunci), logout()
 */
final class AuthController
{
    public function showLogin(): void
    {
        if (AdminAuth::check()) {
            redirect('/admin');
        }

        View::render('admin/auth/login', [
            'title'     => 'Login Admin',
            'bodyClass' => 'page page-auth',
        ]);
    }

    public function login(): void
    {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        $result = $username === '' || $password === ''
            ? AdminAuth::INVALID
            : AdminAuth::attempt($username, $password, client_ip());

        if ($result === AdminAuth::LOCKED) {
            View::error(429, ['title' => 'Terlalu banyak percobaan']);
            return;
        }

        if ($result !== true) {
            Flash::set('error', $username === '' || $password === ''
                ? 'Username dan password wajib diisi.'
                : 'Username atau password salah.');
            Flash::withErrors([], ['username' => $username]);
            redirect('/admin/login');
        }

        redirect('/admin');
    }

    public function logout(): void
    {
        AdminAuth::logout();
        Flash::set('success', 'Anda sudah keluar dari panel admin.');
        redirect('/admin/login');
    }
}
