<?php
/**
 * View: admin/dashboard/index.php — kartu statistik + tabel per departemen + tautan cepat
 * PRD: 2.2.11
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $total, $last24h, $byDepartment (id, name, is_active, total), $regOpen
 */
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Dashboard</h1>
        <p class="muted">Ringkasan pendaftaran <?= e(setting('event_name', '')) ?>.</p>
    </div>
</div>

<div class="stat-grid">
    <div class="card stat">
        <span class="stat-label">Total peserta</span>
        <span class="stat-value"><?= e($total) ?></span>
        <span class="feature-icon stat-icon"><?= icon('users') ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Daftar 24 jam terakhir</span>
        <span class="stat-value"><?= e($last24h) ?></span>
        <span class="feature-icon stat-icon"><?= icon('clock') ?></span>
    </div>
    <div class="card stat">
        <span class="stat-label">Registrasi</span>
        <span class="stat-value stat-value-sm"><?= $regOpen ? 'Dibuka' : 'Ditutup' ?></span>
        <a class="link-strong" href="<?= e(url('/admin/pengaturan')) ?>">Ubah di pengaturan</a>
    </div>
</div>

<section class="card table-card">
    <div class="table-card-head">
        <h2 class="card-title">Peserta per departemen</h2>
        <a class="btn btn-secondary btn-sm" href="<?= e(url('/admin/peserta/ekspor')) ?>"><?= icon('arrow-down', 'icon icon-sm') ?> Unduh CSV</a>
    </div>
    <?php if ($byDepartment === []): ?>
        <p class="empty">Belum ada departemen.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Departemen</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Proporsi</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($byDepartment as $d): $count = (int) $d['total']; $pct = $total > 0 ? $count / $total * 100 : 0; ?>
                <tr>
                    <td data-label="Departemen" class="cell-strong">
                        <a href="<?= e(url('/admin/peserta?department_id=' . $d['id'])) ?>"><?= e($d['name']) ?></a>
                        <?php if ((int) $d['is_active'] !== 1): ?><span class="chip">nonaktif</span><?php endif; ?>
                    </td>
                    <td data-label="Jumlah"><?= $count ?> peserta</td>
                    <td data-label="Proporsi">
                        <span class="bar" aria-hidden="true"><span class="bar-fill" style="width:<?= number_format($pct, 1, '.', '') ?>%"></span></span>
                        <span class="bar-text"><?= number_format($pct, 1, ',', '.') ?>%</span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td class="cell-strong">Total</td>
                    <td class="cell-strong" colspan="2"><?= e($total) ?> peserta</td>
                </tr>
            </tfoot>
        </table>
    <?php endif; ?>
</section>

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
