<?php
/**
 * View: Halaman tidak ditemukan
 * PRD: 4.5
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Pesan ramah + tombol kembali ke beranda
 */
?>
<main class="container error-page">
    <p class="error-code">404</p>
    <h1>Halaman tidak ditemukan</h1>
    <p class="muted">Alamat yang Anda buka tidak ada atau sudah dipindahkan.</p>
    <?php $inAdmin = str_starts_with(current_path(), '/admin'); ?>
    <a class="btn btn-primary" href="<?= e(url($inAdmin ? '/admin' : '/')) ?>"><?= icon('arrow-left', 'icon icon-sm') ?> <?= $inAdmin ? 'Kembali ke dashboard' : 'Kembali ke beranda' ?></a>
</main>
