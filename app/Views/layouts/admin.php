<?php
/**
 * View: Layout admin
 * PRD: 2.2.4–2.2.11, docs/UI-GUIDE.md
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Sidebar: Dashboard, Jadwal & Itinerary, Pengumuman, Info Penting, Data Peserta, Departemen, Pengaturan, Ganti Password, Keluar (form POST + CSRF)
 *   - Tombol hamburger di HP (tanpa JS menu tampil di atas konten)
 *   - Cetak flash dan $content
 *
 * Variabel: $content (wajib), $title (opsional)
 */

use App\Auth\AdminAuth;
use App\Core\Flash;

$eventName = setting('event_name', (string) config('APP_NAME'));
$pageTitle = (isset($title) && $title !== '' ? $title . ' · ' : '') . 'Admin · ' . $eventName;

$primary = setting('primary_color', '#0F766E');
if (!preg_match('/^#[0-9a-f]{6}$/i', (string) $primary)) {
    $primary = '#0F766E';
}

$menu = [
    ['/admin',            'Dashboard',          'grid'],
    ['/admin/jadwal',     'Jadwal & Itinerary', 'calendar'],
    ['/admin/pengumuman', 'Pengumuman',         'megaphone'],
    ['/admin/info',       'Info Penting',       'info'],
    ['/admin/peserta',    'Data Peserta',       'users'],
    ['/admin/departemen', 'Departemen',         'briefcase'],
    ['/admin/pengaturan', 'Pengaturan',         'sliders'],
    ['/admin/akun',       'Ganti Password',     'key'],
];
$path = current_path();
$admin = AdminAuth::user();
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="<?= e($primary) ?>">
    <title><?= e($pageTitle) ?></title>
    <script>document.documentElement.className += ' js';</script>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <style>:root{--color-primary:<?= e($primary) ?>}</style>
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</head>
<body class="admin">
<div class="admin-shell">
    <aside class="admin-sidebar" id="admin-nav">
        <div class="admin-brand">
            <span class="admin-brand-logo"><?= icon('shield-check') ?></span>
            <span class="admin-brand-text">
                <strong>Panel Admin</strong>
                <small><?= e($eventName) ?></small>
            </span>
            <button class="icon-btn admin-nav-close" type="button" data-nav-toggle aria-controls="admin-nav" aria-label="Tutup menu"><?= icon('x') ?></button>
        </div>

        <nav aria-label="Menu admin">
            <ul class="admin-menu">
                <?php foreach ($menu as [$href, $label, $ico]):
                    $active = $href === '/admin' ? $path === '/admin' : ($path === $href || str_starts_with($path, $href . '/')); ?>
                    <li>
                        <a class="admin-menu-link<?= $active ? ' is-active' : '' ?>" href="<?= e(url($href)) ?>"<?= $active ? ' aria-current="page"' : '' ?>>
                            <?= icon($ico) ?><span><?= e($label) ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>

        <form class="admin-logout" method="post" action="<?= e(url('/admin/logout')) ?>">
            <?= csrf_field() ?>
            <button class="admin-menu-link admin-menu-danger" type="submit"><?= icon('log-out') ?><span>Keluar</span></button>
        </form>
    </aside>
    <div class="admin-backdrop" data-nav-toggle aria-hidden="true"></div>

    <div class="admin-main">
        <header class="admin-topbar">
            <button class="icon-btn hamburger" type="button" data-nav-toggle aria-controls="admin-nav" aria-expanded="false" aria-label="Buka menu"><?= icon('menu') ?></button>
            <span class="admin-topbar-title"><?= e($eventName) ?></span>
            <a class="btn btn-secondary btn-sm topbar-preview" href="<?= e(url('/')) ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon icon-sm') ?><span>Halaman peserta</span></a>
            <?php if ($admin !== null): ?><span class="admin-user"><?= e($admin['username']) ?></span><?php endif; ?>
        </header>

        <?php $messages = Flash::get(); ?>
        <?php if ($messages !== []): ?>
            <div class="admin-flash">
                <?php foreach ($messages as $msg): ?>
                    <div class="alert alert-<?= e($msg['type']) ?>" role="<?= $msg['type'] === 'error' ? 'alert' : 'status' ?>"><?= e($msg['message']) ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <main class="admin-content">
            <?= $content ?>
        </main>
    </div>
</div>
</body>
</html>
