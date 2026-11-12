<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\View;
use App\Models\Participant;

/**
 * Dashboard ringkasan admin.
 * PRD: 2.2.11
 *
 *   - index(): total peserta, pendaftar 24 jam, jumlah per departemen (GROUP BY)
 */
final class DashboardController
{
    public function index(): void
    {
        View::render('admin/dashboard/index', [
            'title'        => 'Dashboard',
            'total'        => Participant::countAll(),
            'last24h'      => Participant::countSince(date('Y-m-d H:i:s', time() - 86400)),
            'byDepartment' => Participant::countByDepartment(),
            'regOpen'      => setting('registration_open', '1') === '1',
        ], 'admin');
    }
}
