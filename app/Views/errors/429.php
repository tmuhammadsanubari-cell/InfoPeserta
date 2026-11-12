<?php
/**
 * View: Terlalu banyak percobaan
 * PRD: 4.5 (TOO_MANY_ATTEMPTS)
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - 'Terlalu banyak percobaan. Coba lagi dalam 15 menit.'
 */
$minutes = max(1, (int) config('LOGIN_LOCK_MINUTES', 15));
?>
<main class="container error-page">
    <p class="error-code">429</p>
    <h1>Login ditolak sementara</h1>
    <p class="muted">Terlalu banyak percobaan. Coba lagi dalam <?= e($minutes) ?> menit.</p>
    <a class="btn btn-primary" href="<?= e(url('/admin/login')) ?>"><?= icon('arrow-left', 'icon icon-sm') ?> Kembali ke halaman login</a>
</main>
