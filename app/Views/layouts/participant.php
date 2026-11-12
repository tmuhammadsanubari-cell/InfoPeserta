<?php
/**
 * View: Layout peserta
 * PRD: 2.2.3, docs/UI-GUIDE.md
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - <!doctype html>, viewport, <title> = nama kegiatan
 *   - Inline style :root { --color-primary: setting('primary_color') }
 *   - Link /assets/css/app.css dan /assets/js/app.js (defer)
 *   - Cetak $content dan pesan flash
 *
 * Variabel: $content (wajib), $title (opsional, judul halaman), $bodyClass (opsional).
 */

use App\Core\Flash;

$eventName = setting('event_name', (string) config('APP_NAME'));
$pageTitle = isset($title) && $title !== '' ? $title . ' · ' . $eventName : $eventName;

// Hanya warna hex yang valid yang dicetak ke CSS.
$primary = setting('primary_color', '#0F766E');
if (!preg_match('/^#[0-9a-f]{6}$/i', (string) $primary)) {
    $primary = '#0F766E';
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="<?= e($primary) ?>">
    <title><?= e($pageTitle) ?></title>
    <script>document.documentElement.className += ' js';</script>
    <link rel="stylesheet" href="<?= e(asset('assets/css/app.css')) ?>">
    <style>:root{--color-primary:<?= e($primary) ?>}</style>
    <script src="<?= e(asset('assets/js/app.js')) ?>" defer></script>
</head>
<body class="<?= e($bodyClass ?? 'page') ?>">
<?php $messages = Flash::get(); ?>
<?php if ($messages !== []): ?>
    <div class="flash-stack">
        <?php foreach ($messages as $msg): ?>
            <div class="alert alert-<?= e($msg['type']) ?>" role="<?= $msg['type'] === 'error' ? 'alert' : 'status' ?>">
                <?= e($msg['message']) ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?= $content ?>
</body>
</html>
