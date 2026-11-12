<?php
/**
 * View: admin/dashboard/index.php — kartu statistik + tabel per departemen + tautan cepat
 * PRD: 2.2.11
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * TODO (TASKS Hari 4):
  - kartu statistik + tabel per departemen
 */
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Dashboard</h1>
        <p class="muted">Ringkasan dan tautan cepat panel panitia.</p>
    </div>
</div>

<h2 class="section-label">Tautan cepat</h2>
<div class="quick-links">
    <a class="card quick-link" href="<?= e(url('/admin/jadwal')) ?>">
        <span class="feature-icon"><?= icon('calendar') ?></span>
        <span><strong>Kelola jadwal</strong><small>Hari, rute, dan kegiatan</small></span>
    </a>
    <a class="card quick-link" href="<?= e(url('/admin/pengumuman/tambah')) ?>">
        <span class="feature-icon"><?= icon('megaphone') ?></span>
        <span><strong>Buat pengumuman</strong><small>Kabar terbaru untuk peserta</small></span>
    </a>
    <a class="card quick-link" href="<?= e(url('/admin/peserta')) ?>">
        <span class="feature-icon"><?= icon('users') ?></span>
        <span><strong>Data peserta</strong><small>Cari, filter, dan unduh CSV</small></span>
    </a>
</div>
