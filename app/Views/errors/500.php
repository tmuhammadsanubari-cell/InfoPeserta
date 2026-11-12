<?php
/**
 * View: Error server
 * PRD: 4.5 (INTERNAL_ERROR)
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Pesan umum tanpa detail teknis
 */
?>
<main class="container error-page">
    <p class="error-code">500</p>
    <h1>Terjadi kesalahan</h1>
    <p class="muted">Maaf, ada masalah di server. Silakan coba lagi beberapa saat lagi.</p>
    <?php $inAdmin = str_starts_with(current_path(), '/admin'); ?>
    <a class="btn btn-primary" href="<?= e(url($inAdmin ? '/admin' : '/')) ?>"><?= icon('arrow-left', 'icon icon-sm') ?> <?= $inAdmin ? 'Kembali ke dashboard' : 'Kembali ke beranda' ?></a>
</main>
