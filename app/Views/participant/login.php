<?php
/**
 * View: Form masuk
 * PRD: 2.2.2
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Satu field nomor HP, prefill dari ?phone=
 *   - Pesan 'Nomor belum terdaftar.' + link daftar
 *
 * Variabel: $phone (prefill), $notFound (bool)
 */
$eventLine = implode(' • ', array_filter([setting('event_name'), setting('event_subtitle')]));
$err = $notFound ? null : error('phone');
?>
<main class="container stack">
    <header class="page-header">
        <a class="back-btn" href="<?= e(url('/')) ?>" aria-label="Kembali ke halaman awal"><?= icon('arrow-left') ?></a>
        <div>
            <h1 class="page-title">Masuk</h1>
            <?php if ($eventLine !== ''): ?><p class="page-subtitle"><?= e($eventLine) ?></p><?php endif; ?>
        </div>
    </header>

    <div>
        <h2 class="headline">Selamat datang kembali</h2>
        <p class="muted">Masukkan nomor HP yang Anda pakai saat mendaftar.</p>
    </div>

    <form class="card form" method="post" action="<?= e(url('/masuk')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="form-group">
            <label class="label" for="phone">Nomor HP / WhatsApp</label>
            <input class="input<?= $err !== null || $notFound ? ' is-invalid' : '' ?>" type="tel" id="phone" name="phone"
                   value="<?= e($phone) ?>" placeholder="Contoh: 081234567890"
                   inputmode="tel" autocomplete="tel" maxlength="20" required
                   <?= $err !== null ? 'aria-invalid="true" aria-describedby="phone-error"' : '' ?>
                   <?= $notFound ? 'aria-invalid="true" aria-describedby="phone-not-found"' : '' ?>>
            <?php if ($err !== null): ?><p class="field-error" id="phone-error"><?= icon('alert-circle', 'icon icon-sm') ?><?= e($err) ?></p><?php endif; ?>
        </div>
        <button class="btn btn-primary btn-block" type="submit">Masuk <?= icon('log-in', 'icon icon-sm') ?></button>
    </form>

    <?php if ($notFound): ?>
        <div class="alert alert-error alert-row" id="phone-not-found" role="alert">
            <?= icon('alert-circle') ?>
            <div>
                <strong>Nomor belum terdaftar.</strong>
                <p>Periksa lagi nomornya, atau daftar dulu jika belum pernah mendaftar.</p>
                <a class="link-strong" href="<?= e(url('/daftar')) ?>">Daftar sekarang <?= icon('arrow-right', 'icon icon-sm') ?></a>
            </div>
        </div>
    <?php endif; ?>

    <p class="text-center">Belum mendaftar? <a class="link-strong" href="<?= e(url('/daftar')) ?>">Daftar di sini</a></p>
</main>
