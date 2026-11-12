<?php
/**
 * View: admin/account/edit.php — form ganti password
 * PRD: 2.2.4
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $admin
 */
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Ganti Password</h1>
        <p class="muted">Akun: <strong><?= e($admin['username'] ?? '') ?></strong>. Ganti password bawaan segera setelah website dipasang.</p>
    </div>
</div>

<form class="card form admin-form admin-form-narrow" method="post" action="<?= e(url('/admin/akun')) ?>" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label class="label" for="current_password">Password lama</label>
        <input class="input<?= invalid('current_password') ?>" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
        <?= field_error('current_password') ?>
    </div>

    <div class="form-group">
        <label class="label" for="new_password">Password baru</label>
        <input class="input<?= invalid('new_password') ?>" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="8" maxlength="72" required>
        <p class="helper">Minimal 8 karakter.</p>
        <?= field_error('new_password') ?>
    </div>

    <div class="form-group">
        <label class="label" for="new_password_confirmation">Ulangi password baru</label>
        <input class="input<?= invalid('new_password_confirmation') ?>" type="password" id="new_password_confirmation" name="new_password_confirmation" autocomplete="new-password" required>
        <?= field_error('new_password_confirmation') ?>
    </div>

    <div class="form-actions">
        <button class="btn btn-primary" type="submit"><?= icon('key', 'icon icon-sm') ?> Simpan password</button>
    </div>
</form>
