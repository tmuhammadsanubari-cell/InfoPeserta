<?php
/**
 * View: admin/info/form.php — tambah/ubah
 * PRD: 2.2.6
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $title, $section (id null = tambah)
 */
$isEdit = $section['id'] !== null;
$action = $isEdit ? '/admin/info/' . $section['id'] : '/admin/info';
?>
<div class="admin-page-head">
    <div>
        <p class="breadcrumb"><a href="<?= e(url('/admin/info')) ?>">Info Penting</a> / <?= e($title) ?></p>
        <h1 class="admin-title"><?= e($title) ?></h1>
    </div>
</div>

<form class="card form admin-form" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="label" for="title">Judul <span class="req">Wajib</span></label>
        <input class="input<?= invalid('title') ?>" type="text" id="title" name="title" maxlength="100"
               value="<?= e(old('title', (string) $section['title'])) ?>" placeholder="Contoh: Titik Kumpul" required>
        <?= field_error('title') ?>
    </div>

    <div class="form-group">
        <label class="label" for="body">Isi <span class="req">Wajib</span></label>
        <textarea class="input textarea<?= invalid('body') ?>" id="body" name="body" rows="8" maxlength="10000" required><?= e(old('body', (string) $section['body'])) ?></textarea>
        <p class="helper">Teks biasa. Baris baru dipertahankan dan alamat web otomatis menjadi tautan.</p>
        <?= field_error('body') ?>
    </div>

    <div class="form-group form-group-narrow">
        <label class="label" for="sort_order">Urutan</label>
        <input class="input<?= invalid('sort_order') ?>" type="number" id="sort_order" name="sort_order" min="0" max="9999"
               inputmode="numeric" value="<?= e(old('sort_order', (string) $section['sort_order'])) ?>">
        <p class="helper">Angka kecil tampil lebih dulu.</p>
        <?= field_error('sort_order') ?>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= e(url('/admin/info')) ?>">Batal</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Tambah bagian' ?></button>
    </div>
</form>
