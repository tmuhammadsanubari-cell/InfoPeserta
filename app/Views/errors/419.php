<?php
/**
 * View: Sesi form kedaluwarsa
 * PRD: 4.5 (CSRF_MISMATCH)
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - 'Halaman sudah terlalu lama dibuka. Silakan muat ulang dan coba lagi.'
 */
?>
<main class="container error-page">
    <p class="error-code">419</p>
    <h1>Sesi form kedaluwarsa</h1>
    <p class="muted">Halaman sudah terlalu lama dibuka. Silakan muat ulang dan coba lagi.</p>
    <?php $inAdmin = str_starts_with(current_path(), '/admin'); ?>
    <a class="btn btn-primary" href="<?= e(url($inAdmin ? '/admin' : '/')) ?>"><?= icon('refresh', 'icon icon-sm') ?> Muat ulang dari <?= $inAdmin ? 'dashboard' : 'beranda' ?></a>
</main>
