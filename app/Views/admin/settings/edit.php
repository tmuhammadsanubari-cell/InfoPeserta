<?php
/**
 * View: admin/settings/edit.php — form pengaturan acara
 * PRD: 2.2.9
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $settings (key => value)
 */
use App\Controllers\Admin\SettingController;
use App\Core\Flash;

$val = static fn (string $key): string => old($key, (string) ($settings[$key] ?? ''));
$color = strtoupper($val('primary_color') ?: '#0F766E');
$regOpen = Flash::old() !== [] ? old('registration_open') === '1' : ($settings['registration_open'] ?? '1') === '1';
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Pengaturan Acara</h1>
        <p class="muted">Identitas kegiatan, kontak panitia, warna tampilan, dan status pendaftaran.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-secondary" href="<?= e(url('/')) ?>" target="_blank" rel="noopener"><?= icon('eye', 'icon icon-sm') ?> Pratinjau</a>
    </div>
</div>

<form class="card form admin-form" method="post" action="<?= e(url('/admin/pengaturan')) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="label" for="event_name">Nama kegiatan <span class="req">Wajib</span></label>
        <input class="input<?= invalid('event_name') ?>" type="text" id="event_name" name="event_name" maxlength="100" value="<?= e($val('event_name')) ?>" required>
        <p class="helper">Tampil di judul tab browser dan header halaman.</p>
        <?= field_error('event_name') ?>
    </div>

    <div class="form-group">
        <label class="label" for="event_subtitle">Subjudul <span class="opt">Opsional</span></label>
        <input class="input<?= invalid('event_subtitle') ?>" type="text" id="event_subtitle" name="event_subtitle" maxlength="150" value="<?= e($val('event_subtitle')) ?>" placeholder="Contoh: Malang – Batu">
        <?= field_error('event_subtitle') ?>
    </div>

    <div class="form-row">
        <div class="form-group">
            <label class="label" for="event_start_date">Tanggal mulai <span class="req">Wajib</span></label>
            <input class="input<?= invalid('event_start_date') ?>" type="date" id="event_start_date" name="event_start_date" value="<?= e($val('event_start_date')) ?>" required>
            <?= field_error('event_start_date') ?>
        </div>
        <div class="form-group">
            <label class="label" for="event_end_date">Tanggal selesai <span class="req">Wajib</span></label>
            <input class="input<?= invalid('event_end_date') ?>" type="date" id="event_end_date" name="event_end_date" value="<?= e($val('event_end_date')) ?>" required>
            <?= field_error('event_end_date') ?>
        </div>
    </div>

    <div class="form-group">
        <label class="label" for="committee_whatsapp">Nomor WhatsApp panitia <span class="opt">Opsional</span></label>
        <input class="input<?= invalid('committee_whatsapp') ?>" type="tel" id="committee_whatsapp" name="committee_whatsapp" inputmode="tel" maxlength="20"
               value="<?= e($val('committee_whatsapp')) ?>" placeholder="Contoh: 081234567890">
        <p class="helper">Jika diisi, tombol WhatsApp muncul di halaman informasi peserta. Kosongkan untuk menyembunyikan.</p>
        <?= field_error('committee_whatsapp') ?>
    </div>

    <fieldset class="form-group color-field">
        <legend class="label">Warna utama <code class="color-code" data-color-code><?= e($color) ?></code></legend>
        <div class="color-row">
            <input class="color-input<?= invalid('primary_color') ?>" type="color" id="primary_color" name="primary_color" value="<?= e(strtolower($color)) ?>" aria-label="Pilih warna utama" data-color-input>
            <div class="swatches" role="group" aria-label="Pilihan warna cepat">
                <?php foreach (SettingController::PRESET_COLORS as $hex => $name): ?>
                    <button class="swatch<?= $hex === $color ? ' is-active' : '' ?>" type="button" style="background:<?= e($hex) ?>"
                            data-color-preset="<?= e(strtolower($hex)) ?>" title="<?= e($name) ?>" aria-label="Warna <?= e($name) ?>"></button>
                <?php endforeach; ?>
            </div>
        </div>
        <p class="helper">Dipakai untuk header, tombol, dan tab aktif. Pilih warna yang cukup gelap agar teks putih terbaca.</p>
        <?= field_error('primary_color') ?>
    </fieldset>

    <label class="checkbox">
        <input type="checkbox" name="registration_open" value="1"<?= $regOpen ? ' checked' : '' ?>>
        <span><strong>Registrasi dibuka</strong><br><small class="muted">Jika tidak dicentang, form pendaftaran ditutup. Peserta terdaftar tetap bisa masuk.</small></span>
    </label>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit">Simpan pengaturan</button>
    </div>
</form>
