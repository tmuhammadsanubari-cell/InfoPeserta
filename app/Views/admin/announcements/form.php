<?php
/**
 * View: admin/announcements/form.php — tambah/ubah
 * PRD: 2.2.6
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $title, $announcement (id null = tambah)
 */
use App\Core\Flash;

$isEdit = $announcement['id'] !== null;
$action = $isEdit ? '/admin/pengumuman/' . $announcement['id'] : '/admin/pengumuman';
// Checkbox tidak terkirim jika kosong: setelah gagal validasi, pakai nilai yang dikirim.
$pinned = Flash::old() !== [] ? old('is_pinned') === '1' : (int) $announcement['is_pinned'] === 1;
?>
<div class="admin-page-head">
    <div>
        <p class="breadcrumb"><a href="<?= e(url('/admin/pengumuman')) ?>">Pengumuman</a> / <?= e($title) ?></p>
        <h1 class="admin-title"><?= e($title) ?></h1>
    </div>
</div>

<form class="card form admin-form" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="label" for="title">Judul <span class="req">Wajib</span></label>
        <input class="input<?= invalid('title') ?>" type="text" id="title" name="title" maxlength="150"
               value="<?= e(old('title', (string) $announcement['title'])) ?>" placeholder="Contoh: Perubahan titik kumpul hari ke-2" required>
        <p class="helper">Maksimal 150 karakter.</p>
        <?= field_error('title') ?>
    </div>

    <div class="form-group">
        <label class="label" for="body">Isi pengumuman <span class="req">Wajib</span></label>
        <textarea class="input textarea<?= invalid('body') ?>" id="body" name="body" rows="7" maxlength="10000" required><?= e(old('body', (string) $announcement['body'])) ?></textarea>
        <p class="helper">Teks biasa. Baris baru dipertahankan dan alamat web otomatis menjadi tautan.</p>
        <?= field_error('body') ?>
    </div>

    <label class="checkbox">
        <input type="checkbox" name="is_pinned" value="1"<?= $pinned ? ' checked' : '' ?>>
        <span><strong>Sematkan</strong> agar selalu tampil paling atas</span>
    </label>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= e(url('/admin/pengumuman')) ?>">Batal</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Terbitkan' ?></button>
    </div>
</form>
