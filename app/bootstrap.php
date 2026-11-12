<?php

declare(strict_types=1);

/**
 * Inisialisasi aplikasi. Dipanggil dari public/index.php (BASE_PATH sudah didefinisikan).
 * PRD: 4.1, NFR-004, 4.5
 *
 *   - Muat app/config.php dan app/helpers.php
 *   - spl_autoload_register: namespace App\ → BASE_PATH . '/app/'
 *   - date_default_timezone_set(APP_TIMEZONE)
 *   - Error handling: development tampilkan detail; production display_errors=0, log ke storage/logs/app.log, render errors/500
 *   - session_name(SESSION_NAME) + session_set_cookie_params (httponly, samesite Lax, secure jika HTTPS) + session_start()
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

require BASE_PATH . '/app/config.php';
require BASE_PATH . '/app/helpers.php';

// ---- Autoload: App\Core\Router → app/Core/Router.php ----
spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }
    $file = BASE_PATH . '/app/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require $file;
    }
});

// ---- Zona waktu ----
$timezone = (string) config('APP_TIMEZONE', 'Asia/Jakarta');
if (!in_array($timezone, timezone_identifiers_list(), true)) {
    $timezone = 'Asia/Jakarta';
}
date_default_timezone_set($timezone);

// ---- Error handling ----
$isDev = config('APP_ENV') === 'development';

error_reporting(E_ALL);
ini_set('display_errors', $isDev ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/app.log');

// Ubah warning/notice menjadi exception agar tidak lolos diam-diam.
set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
});

set_exception_handler(static function (Throwable $e) use ($isDev): void {
    log_error($e);

    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    if (!headers_sent()) {
        http_response_code(500);
        header('Content-Type: text/html; charset=UTF-8');
    }

    if ($isDev) {
        echo '<!doctype html><meta charset="utf-8"><title>Error</title>'
            . '<pre style="white-space:pre-wrap;font:14px/1.5 monospace;padding:16px">'
            . e(get_class($e) . ': ' . $e->getMessage()) . "\n\n"
            . e($e->getFile() . ':' . $e->getLine()) . "\n\n"
            . e($e->getTraceAsString())
            . '</pre>';
        return;
    }

    try {
        App\Core\View::render('errors/500', ['title' => 'Terjadi kesalahan']);
    } catch (Throwable) {
        // Layout gagal (misalnya database mati): tampilkan pesan polos.
        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        echo '<!doctype html><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
            . '<title>Terjadi kesalahan</title>'
            . '<p style="font-family:system-ui,sans-serif;padding:24px">'
            . 'Maaf, terjadi kesalahan di server. Silakan coba lagi beberapa saat lagi.</p>';
    }
});

// ---- Sesi ----
if (PHP_SAPI !== 'cli' && session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');

    session_name((string) config('SESSION_NAME', 'pip_session'));
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => base_path() . '/',
        'secure'   => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
