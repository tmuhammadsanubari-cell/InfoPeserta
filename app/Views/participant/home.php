<?php
/**
 * View: Halaman awal
 * PRD: 4.3 (GET /)
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Nama kegiatan, subjudul, tanggal
 *   - Tombol 'Daftar Sekarang' dan 'Sudah daftar? Masuk'
 *
 * Variabel: $eventName, $eventSubtitle, $eventDates, $registrationOpen, $committeeWa
 */
?>
<main class="container stack">
    <section class="hero">
        <span class="hero-badge"><?= icon('shield-check', 'icon icon-sm') ?> Portal Resmi Peserta</span>
        <h1 class="hero-title"><?= e($eventName) ?></h1>
        <?php if ($eventSubtitle !== null || $eventDates !== ''): ?>
            <p class="hero-meta">
                <?php if ($eventSubtitle !== null): ?>
                    <span><?= icon('map-pin', 'icon icon-sm') ?><?= e($eventSubtitle) ?></span>
                <?php endif; ?>
                <?php if ($eventDates !== ''): ?>
                    <span><?= icon('calendar', 'icon icon-sm') ?><?= e($eventDates) ?></span>
                <?php endif; ?>
            </p>
        <?php endif; ?>
        <p class="hero-text">Pusat informasi jadwal dan agenda perjalanan.</p>
    </section>

    <section class="card" aria-labelledby="fitur-heading">
        <h2 class="card-title" id="fitur-heading">Yang bisa Anda lihat</h2>
        <ul class="feature-list">
            <li class="feature">
                <span class="feature-icon"><?= icon('route') ?></span>
                <span>
                    <strong>Jadwal &amp; Rute Harian</strong>
                    <small>Agenda tiap hari, jam, lokasi, dan rute perjalanan</small>
                </span>
            </li>
            <li class="feature">
                <span class="feature-icon"><?= icon('megaphone') ?></span>
                <span>
                    <strong>Pengumuman Terbaru</strong>
                    <small>Kabar perubahan jadwal di satu tempat</small>
                </span>
            </li>
            <li class="feature">
                <span class="feature-icon"><?= icon('info') ?></span>
                <span>
                    <strong>Info Penting</strong>
                    <small>Titik kumpul, kontak panitia, dan barang bawaan</small>
                </span>
            </li>
        </ul>
    </section>

    <section class="actions">
        <?php if ($registrationOpen): ?>
            <a class="btn btn-primary btn-block" href="<?= e(url('/daftar')) ?>">
                Daftar Sekarang <?= icon('arrow-right', 'icon icon-sm') ?>
            </a>
            <a class="btn btn-secondary btn-block" href="<?= e(url('/masuk')) ?>">
                <?= icon('log-in', 'icon icon-sm') ?> Sudah daftar? Masuk
            </a>
        <?php else: ?>
            <div class="alert alert-info">Registrasi sudah ditutup. Peserta yang sudah terdaftar tetap bisa masuk.</div>
            <a class="btn btn-primary btn-block" href="<?= e(url('/masuk')) ?>">
                <?= icon('log-in', 'icon icon-sm') ?> Masuk
            </a>
        <?php endif; ?>
    </section>

    <?php if ($committeeWa !== null && $committeeWa !== ''): ?>
        <section class="help-card">
            <span class="help-icon"><?= icon('phone', 'icon icon-sm') ?></span>
            <span class="help-text">
                <strong>Butuh bantuan?</strong>
                <small>Hubungi panitia lewat WhatsApp</small>
            </span>
            <a class="btn btn-light btn-sm" href="https://wa.me/<?= e($committeeWa) ?>" target="_blank" rel="noopener">Hubungi</a>
        </section>
    <?php endif; ?>

    <footer class="page-footer">Panitia <?= e($eventName) ?></footer>
</main>
