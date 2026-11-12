<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\View;

/**
 * Dashboard ringkasan admin.
 * PRD: 2.2.11
 *
 * TODO (TASKS Hari 4):
 *   - index(): total peserta, pendaftar 24 jam, jumlah per departemen (GROUP BY)
 *
 * Versi Hari 3: hanya tautan cepat, agar login admin punya halaman tujuan.
 */
final class DashboardController
{
    public function index(): void
    {
        View::render('admin/dashboard/index', ['title' => 'Dashboard'], 'admin');
    }
}
