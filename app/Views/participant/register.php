<?php
/**
 * View: Form registrasi
 * PRD: 2.2.1
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Field nama lengkap, departemen (dropdown), nomor HP (type=tel)
 *   - Error per field + old input
 *   - Kondisi registrasi ditutup
 *
 * Variabel: $open (bool), $departments (array id, name)
 */
$eventLine = implode(' • ', array_filter([setting('event_name'), setting('event_subtitle')]));
$field = static function (string $name): array {
    $err = error($name);
    return [
        'err'   => $err,
        'class' => $err !== null ? ' is-invalid' : '',
        'aria'  => $err !== null ? ' aria-invalid="true" aria-describedby="' . $name . '-error"' : '',
    ];
};
?>
<main class="container stack">
    <header class="page-header">
        <a class="back-btn" href="<?= e(url('/')) ?>" aria-label="Kembali ke halaman awal"><?= icon('arrow-left') ?></a>
        <div>
            <h1 class="page-title">Daftar Peserta</h1>
            <?php if ($eventLine !== ''): ?><p class="page-subtitle"><?= e($eventLine) ?></p><?php endif; ?>
        </div>
    </header>

    <?php if (!$open): ?>
        <section class="card stack-sm">
            <div class="alert alert-info"><strong>Registrasi sudah ditutup.</strong> Peserta yang sudah terdaftar tetap bisa masuk dengan nomor HP.</div>
            <a class="btn btn-primary btn-block" href="<?= e(url('/masuk')) ?>"><?= icon('log-in', 'icon icon-sm') ?> Masuk</a>
        </section>
    <?php else: ?>
        <p class="note"><?= icon('clipboard') ?> <span>Isi data di bawah untuk melihat jadwal dan informasi perjalanan.</span></p>

        <form class="card form" method="post" action="<?= e(url('/daftar')) ?>" novalidate>
            <?= csrf_field() ?>

            <?php $f = $field('full_name'); ?>
            <div class="form-group">
                <label class="label" for="full_name">Nama lengkap <span class="req">Wajib</span></label>
                <input class="input<?= $f['class'] ?>" type="text" id="full_name" name="full_name"
                       value="<?= e(old('full_name')) ?>" placeholder="Contoh: Rina Wulandari"
                       autocomplete="name" minlength="3" maxlength="100" required<?= $f['aria'] ?>>
                <?php if ($f['err'] !== null): ?><p class="field-error" id="full_name-error"><?= icon('alert-circle', 'icon icon-sm') ?><?= e($f['err']) ?></p><?php endif; ?>
            </div>

            <?php $f = $field('department_id'); $selected = old('department_id'); ?>
            <div class="form-group">
                <label class="label" for="department_id">Departemen <span class="req">Wajib</span></label>
                <select class="input select<?= $f['class'] ?>" id="department_id" name="department_id" required<?= $f['aria'] ?>>
                    <option value="">Pilih departemen</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= e($dept['id']) ?>"<?= (string) $dept['id'] === $selected ? ' selected' : '' ?>><?= e($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($f['err'] !== null): ?><p class="field-error" id="department_id-error"><?= icon('alert-circle', 'icon icon-sm') ?><?= e($f['err']) ?></p><?php endif; ?>
            </div>

            <?php $f = $field('phone'); ?>
            <div class="form-group">
                <label class="label" for="phone">Nomor HP / WhatsApp <span class="req">Wajib</span></label>
                <input class="input<?= $f['class'] ?>" type="tel" id="phone" name="phone"
                       value="<?= e(old('phone')) ?>" placeholder="Contoh: 081234567890"
                       inputmode="tel" autocomplete="tel" maxlength="20" required<?= $f['aria'] ?>>
                <p class="helper">Nomor ini dipakai untuk masuk kembali ke website ini.</p>
                <?php if ($f['err'] !== null): ?><p class="field-error" id="phone-error"><?= icon('alert-circle', 'icon icon-sm') ?><?= e($f['err']) ?></p><?php endif; ?>
            </div>

            <button class="btn btn-primary btn-block" type="submit">Daftar &amp; Lihat Jadwal <?= icon('arrow-right', 'icon icon-sm') ?></button>
        </form>

        <p class="text-center"><a class="link-strong" href="<?= e(url('/masuk')) ?>">Sudah pernah daftar? Masuk di sini</a></p>
    <?php endif; ?>
</main>
