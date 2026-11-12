<?php
/**
 * View: admin/auth/login.php — form username/password, pesan error
 * PRD: 2.2.4
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - form username/password, pesan error (pesan tampil lewat flash di layout)
 */
$eventLine = implode(' • ', array_filter([setting('event_name'), setting('event_subtitle')]));
?>
<main class="container auth-wrap">
    <form class="card form auth-card" method="post" action="<?= e(url('/admin/login')) ?>">
        <?= csrf_field() ?>
        <div class="auth-head">
            <span class="auth-logo"><?= icon('shield-check') ?></span>
            <h1 class="page-title">Panel Admin</h1>
            <?php if ($eventLine !== ''): ?><p class="page-subtitle"><?= e($eventLine) ?></p><?php endif; ?>
        </div>

        <div class="form-group">
            <label class="label" for="username">Username</label>
            <input class="input" type="text" id="username" name="username" value="<?= e(old('username')) ?>"
                   autocomplete="username" autocapitalize="none" spellcheck="false" maxlength="50" required autofocus>
        </div>

        <div class="form-group">
            <label class="label" for="password">Password</label>
            <input class="input" type="password" id="password" name="password" autocomplete="current-password" required>
        </div>

        <button class="btn btn-primary btn-block" type="submit"><?= icon('log-in', 'icon icon-sm') ?> Masuk</button>

        <p class="note note-sm"><?= icon('shield-check', 'icon icon-sm') ?> <span>Khusus panitia. Lupa password? Hubungi pengelola website.</span></p>
    </form>

    <p class="text-center"><a class="link-strong" href="<?= e(url('/')) ?>"><?= icon('arrow-left', 'icon icon-sm') ?> Kembali ke halaman peserta</a></p>
</main>
