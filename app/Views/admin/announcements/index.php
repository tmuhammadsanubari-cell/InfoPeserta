<?php
/**
 * View: admin/announcements/index.php — daftar
 * PRD: 2.2.6
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $announcements (disematkan dulu, lalu terbaru)
 */
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Pengumuman</h1>
        <p class="muted">Pengumuman yang disematkan selalu tampil paling atas di halaman peserta.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-primary" href="<?= e(url('/admin/pengumuman/tambah')) ?>"><?= icon('plus', 'icon icon-sm') ?> Tambah Pengumuman</a>
    </div>
</div>

<?php if ($announcements === []): ?>
    <div class="card empty">Belum ada pengumuman.</div>
<?php else: ?>
    <div class="stack-sm">
        <?php foreach ($announcements as $a): $pinned = (int) $a['is_pinned'] === 1; ?>
            <article class="card admin-list-item<?= $pinned ? ' is-pinned' : '' ?>">
                <div class="admin-list-main">
                    <p class="admin-list-meta">
                        <?php if ($pinned): ?><span class="badge badge-primary"><?= icon('pin', 'icon icon-xs') ?> Disematkan</span><?php endif; ?>
                        <span class="muted"><?= icon('clock', 'icon icon-xs') ?> <?= e(format_datetime_id($a['published_at'])) ?></span>
                    </p>
                    <h2 class="admin-list-title"><?= e($a['title']) ?></h2>
                    <p class="admin-list-excerpt"><?= e(mb_strimwidth(preg_replace('/\s+/u', ' ', (string) $a['body']) ?? '', 0, 180, '…')) ?></p>
                </div>
                <div class="admin-row-actions">
                    <a class="btn btn-secondary btn-sm" href="<?= e(url('/admin/pengumuman/' . $a['id'] . '/edit')) ?>"><?= icon('edit', 'icon icon-sm') ?> Ubah</a>
                    <form method="post" action="<?= e(url('/admin/pengumuman/' . $a['id'] . '/hapus')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger-soft btn-sm" type="submit" data-confirm="Hapus pengumuman &quot;<?= e($a['title']) ?>&quot;?"><?= icon('trash', 'icon icon-sm') ?> Hapus</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
