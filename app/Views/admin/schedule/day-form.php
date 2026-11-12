<?php
/**
 * View: admin/schedule/day-form.php — form hari (tambah/ubah)
 * PRD: 2.2.5
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $title, $day (id null = tambah)
 */
$isEdit = $day['id'] !== null;
$action = $isEdit ? '/admin/jadwal/hari/' . $day['id'] : '/admin/jadwal/hari';
?>
<div class="admin-page-head">
    <div>
        <p class="breadcrumb"><a href="<?= e(url('/admin/jadwal')) ?>">Jadwal &amp; Itinerary</a> / <?= e($title) ?></p>
        <h1 class="admin-title"><?= e($title) ?></h1>
    </div>
</div>

<form class="card form admin-form" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form-row">
        <div class="form-group">
            <label class="label" for="day_number">Nomor hari <span class="req">Wajib</span></label>
            <input class="input<?= invalid('day_number') ?>" type="number" id="day_number" name="day_number" min="1" max="255"
                   inputmode="numeric" value="<?= e(old('day_number', (string) $day['day_number'])) ?>" required>
            <?= field_error('day_number') ?>
        </div>
        <div class="form-group">
            <label class="label" for="date">Tanggal <span class="req">Wajib</span></label>
            <input class="input<?= invalid('date') ?>" type="date" id="date" name="date"
                   value="<?= e(old('date', (string) $day['date'])) ?>" required>
            <?= field_error('date') ?>
        </div>
    </div>

    <div class="form-group">
        <label class="label" for="title">Judul hari <span class="req">Wajib</span></label>
        <input class="input<?= invalid('title') ?>" type="text" id="title" name="title" maxlength="150"
               value="<?= e(old('title', (string) $day['title'])) ?>" placeholder="Contoh: Berangkat ke Malang" required>
        <?= field_error('title') ?>
    </div>

    <div class="form-group">
        <label class="label" for="route">Rute itinerary <span class="opt">Opsional</span></label>
        <input class="input<?= invalid('route') ?>" type="text" id="route" name="route" maxlength="255"
               value="<?= e(old('route', (string) ($day['route'] ?? ''))) ?>" placeholder="Contoh: Lamongan → Batu → Malang">
        <p class="helper">Pisahkan tempat dengan tanda → atau -.</p>
        <?= field_error('route') ?>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= e(url('/admin/jadwal')) ?>">Batal</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Tambah hari' ?></button>
    </div>
</form>
