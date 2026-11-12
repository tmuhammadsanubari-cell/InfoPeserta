<?php
/**
 * View: admin/schedule/item-form.php — form kegiatan (tambah/ubah)
 * PRD: 2.2.5
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $title, $days, $item (id null = tambah)
 */
$isEdit = $item['id'] !== null;
$action = $isEdit ? '/admin/jadwal/kegiatan/' . $item['id'] : '/admin/jadwal/kegiatan';
$selectedDay = old('day_id', (string) $item['day_id']);
?>
<div class="admin-page-head">
    <div>
        <p class="breadcrumb"><a href="<?= e(url('/admin/jadwal')) ?>">Jadwal &amp; Itinerary</a> / <?= e($title) ?></p>
        <h1 class="admin-title"><?= e($title) ?></h1>
    </div>
</div>

<form class="card form admin-form" method="post" action="<?= e(url($action)) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="label" for="day_id">Hari <span class="req">Wajib</span></label>
        <select class="input select<?= invalid('day_id') ?>" id="day_id" name="day_id" required>
            <?php foreach ($days as $d): ?>
                <option value="<?= e($d['id']) ?>"<?= (string) $d['id'] === $selectedDay ? ' selected' : '' ?>>
                    Hari <?= e($d['day_number']) ?> – <?= e($d['title']) ?> (<?= e(format_date_short_id($d['date'])) ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <?= field_error('day_id') ?>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="label" for="start_time">Jam mulai <span class="req">Wajib</span></label>
            <input class="input<?= invalid('start_time') ?>" type="time" id="start_time" name="start_time"
                   value="<?= e(substr(old('start_time', (string) $item['start_time']), 0, 5)) ?>" required>
            <?= field_error('start_time') ?>
        </div>
        <div class="form-group">
            <label class="label" for="end_time">Jam selesai <span class="opt">Opsional</span></label>
            <input class="input<?= invalid('end_time') ?>" type="time" id="end_time" name="end_time"
                   value="<?= e(substr(old('end_time', (string) ($item['end_time'] ?? '')), 0, 5)) ?>">
            <?= field_error('end_time') ?>
        </div>
    </div>

    <div class="form-group">
        <label class="label" for="activity">Nama kegiatan <span class="req">Wajib</span></label>
        <input class="input<?= invalid('activity') ?>" type="text" id="activity" name="activity" maxlength="150"
               value="<?= e(old('activity', (string) $item['activity'])) ?>" placeholder="Contoh: Sarapan dan check-out" required>
        <?= field_error('activity') ?>
    </div>

    <div class="form-group">
        <label class="label" for="location">Lokasi <span class="opt">Opsional</span></label>
        <input class="input<?= invalid('location') ?>" type="text" id="location" name="location" maxlength="200"
               value="<?= e(old('location', (string) ($item['location'] ?? ''))) ?>" placeholder="Contoh: Jatim Park 2, Batu">
        <p class="helper">Jika diisi, peserta melihat tautan "Buka di Maps".</p>
        <?= field_error('location') ?>
    </div>

    <div class="form-group">
        <label class="label" for="notes">Catatan <span class="opt">Opsional</span></label>
        <textarea class="input textarea<?= invalid('notes') ?>" id="notes" name="notes" rows="4" maxlength="2000"
                  placeholder="Contoh: Bus 1 dan Bus 2 berangkat bersamaan."><?= e(old('notes', (string) ($item['notes'] ?? ''))) ?></textarea>
        <?= field_error('notes') ?>
    </div>

    <div class="form-group form-group-narrow">
        <label class="label" for="sort_order">Urutan <span class="opt">Opsional</span></label>
        <input class="input<?= invalid('sort_order') ?>" type="number" id="sort_order" name="sort_order" min="0" max="9999"
               inputmode="numeric" value="<?= e(old('sort_order', (string) $item['sort_order'])) ?>">
        <p class="helper">Hanya dipakai jika ada dua kegiatan dengan jam mulai yang sama.</p>
        <?= field_error('sort_order') ?>
    </div>

    <div class="form-actions">
        <a class="btn btn-secondary" href="<?= e(url('/admin/jadwal')) ?>">Batal</a>
        <button class="btn btn-primary" type="submit"><?= $isEdit ? 'Simpan perubahan' : 'Tambah kegiatan' ?></button>
    </div>
</form>
