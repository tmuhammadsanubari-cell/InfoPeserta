<?php
/**
 * View: admin/schedule/index.php — kartu per hari + tabel kegiatan
 * PRD: 2.2.5
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $days (dengan 'items'), $todayStr
 */
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Jadwal &amp; Itinerary</h1>
        <p class="muted">Kelola hari, rute perjalanan, dan kegiatan. Perubahan langsung terlihat di halaman peserta.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-secondary" href="<?= e(url('/info')) ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon icon-sm') ?> Lihat halaman peserta</a>
        <a class="btn btn-primary" href="<?= e(url('/admin/jadwal/hari/tambah')) ?>"><?= icon('plus', 'icon icon-sm') ?> Tambah Hari</a>
    </div>
</div>

<?php if ($days === []): ?>
    <div class="card empty">
        <p>Belum ada jadwal. Peserta akan melihat pesan "Jadwal akan segera diumumkan."</p>
        <p><a class="btn btn-primary" href="<?= e(url('/admin/jadwal/hari/tambah')) ?>"><?= icon('plus', 'icon icon-sm') ?> Tambah hari pertama</a></p>
    </div>
<?php endif; ?>

<div class="stack">
<?php foreach ($days as $day): $count = count($day['items']); ?>
    <section class="card admin-day" id="hari-<?= e($day['id']) ?>">
        <div class="admin-day-head">
            <div class="admin-day-info">
                <p class="admin-day-meta">
                    <span class="badge badge-primary">Hari <?= e($day['day_number']) ?></span>
                    <span><?= e(format_date_id($day['date'])) ?></span>
                    <?php if ($day['date'] === $todayStr): ?><span class="badge badge-soft">Hari ini</span><?php endif; ?>
                </p>
                <h2 class="admin-day-title"><?= e($day['title']) ?></h2>
                <?php if (!empty($day['route'])): ?>
                    <p class="route route-inline"><?= icon('navigation', 'icon icon-sm') ?><span><?= e($day['route']) ?></span></p>
                <?php endif; ?>
            </div>
            <div class="admin-row-actions">
                <a class="btn btn-secondary btn-sm" href="<?= e(url('/admin/jadwal/hari/' . $day['id'] . '/edit')) ?>"><?= icon('edit', 'icon icon-sm') ?> Ubah hari</a>
                <form method="post" action="<?= e(url('/admin/jadwal/hari/' . $day['id'] . '/hapus')) ?>">
                    <?= csrf_field() ?>
                    <button class="btn btn-danger-soft btn-sm" type="submit"
                            data-confirm="Hapus Hari <?= e($day['day_number']) ?> beserta <?= $count ?> kegiatan di dalamnya? Tindakan ini tidak bisa dibatalkan.">
                        <?= icon('trash', 'icon icon-sm') ?> Hapus hari
                    </button>
                </form>
            </div>
        </div>

        <?php if ($count === 0): ?>
            <p class="muted admin-day-empty">Belum ada kegiatan di hari ini.</p>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Jam</th>
                        <th scope="col">Kegiatan</th>
                        <th scope="col">Lokasi</th>
                        <th scope="col">Catatan</th>
                        <th scope="col" class="col-actions"><span class="sr-only">Aksi</span></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($day['items'] as $item): ?>
                    <tr>
                        <td data-label="Jam" class="cell-time">
                            <?= e(format_time_id($item['start_time'])) ?><?= $item['end_time'] ? ' – ' . e(format_time_id($item['end_time'])) : '' ?>
                        </td>
                        <td data-label="Kegiatan" class="cell-strong"><?= e($item['activity']) ?></td>
                        <td data-label="Lokasi"><?= $item['location'] ? e($item['location']) : '<span class="muted">–</span>' ?></td>
                        <td data-label="Catatan" class="cell-notes"><?= $item['notes'] ? format_text($item['notes']) : '<span class="muted">–</span>' ?></td>
                        <td class="col-actions">
                            <div class="admin-row-actions">
                                <a class="icon-btn" href="<?= e(url('/admin/jadwal/kegiatan/' . $item['id'] . '/edit')) ?>" aria-label="Ubah <?= e($item['activity']) ?>" title="Ubah"><?= icon('edit', 'icon icon-sm') ?></a>
                                <form method="post" action="<?= e(url('/admin/jadwal/kegiatan/' . $item['id'] . '/hapus')) ?>">
                                    <?= csrf_field() ?>
                                    <button class="icon-btn icon-btn-danger" type="submit" aria-label="Hapus <?= e($item['activity']) ?>" title="Hapus"
                                            data-confirm="Hapus kegiatan &quot;<?= e($item['activity']) ?>&quot;?"><?= icon('trash', 'icon icon-sm') ?></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <div class="admin-day-foot">
            <a class="btn btn-secondary btn-sm" href="<?= e(url('/admin/jadwal/kegiatan/tambah?day_id=' . $day['id'])) ?>"><?= icon('plus', 'icon icon-sm') ?> Tambah kegiatan Hari <?= e($day['day_number']) ?></a>
            <span class="muted"><?= $count ?> kegiatan</span>
        </div>
    </section>
<?php endforeach; ?>
</div>
