<?php
/**
 * View: admin/info/index.php — daftar
 * PRD: 2.2.6
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $sections (urut sort_order)
 */
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Info Penting</h1>
        <p class="muted">Bagian seperti titik kumpul, kontak panitia, dan barang bawaan. Tampil berurutan di tab Info peserta.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-primary" href="<?= e(url('/admin/info/tambah')) ?>"><?= icon('plus', 'icon icon-sm') ?> Tambah Bagian</a>
    </div>
</div>

<?php if ($sections === []): ?>
    <div class="card empty">Belum ada info penting.</div>
<?php else: ?>
    <div class="stack-sm">
        <?php foreach ($sections as $s): ?>
            <article class="card admin-list-item">
                <span class="order-badge" title="Urutan"><?= e($s['sort_order']) ?></span>
                <div class="admin-list-main">
                    <h2 class="admin-list-title"><?= e($s['title']) ?></h2>
                    <p class="admin-list-excerpt"><?= e(mb_strimwidth(preg_replace('/\s+/u', ' ', (string) $s['body']) ?? '', 0, 160, '…')) ?></p>
                </div>
                <div class="admin-row-actions">
                    <a class="btn btn-secondary btn-sm" href="<?= e(url('/admin/info/' . $s['id'] . '/edit')) ?>"><?= icon('edit', 'icon icon-sm') ?> Ubah</a>
                    <form method="post" action="<?= e(url('/admin/info/' . $s['id'] . '/hapus')) ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-danger-soft btn-sm" type="submit" data-confirm="Hapus bagian &quot;<?= e($s['title']) ?>&quot;?"><?= icon('trash', 'icon icon-sm') ?> Hapus</button>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
