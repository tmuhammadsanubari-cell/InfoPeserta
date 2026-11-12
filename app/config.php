<?php

declare(strict_types=1);

/**
 * Baca .env (tanpa Composer) dan kembalikan array konfigurasi.
 * PRD: 4.6
 *
 * - config(string $key, mixed $default = null): mixed
 * - Parser .env: abaikan komentar (#) dan baris kosong, dukung nilai berkutip ganda/tunggal
 * - Default aman jika .env tidak ada (APP_ENV=production)
 */

/**
 * Parse isi file .env menjadi array key => string.
 */
function parse_env_file(string $path): array
{
    if (!is_file($path) || !is_readable($path)) {
        return [];
    }

    $values = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES) ?: [];

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_starts_with($line, 'export ')) {
            $line = trim(substr($line, 7));
        }

        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }

        $key = trim(substr($line, 0, $pos));
        $value = trim(substr($line, $pos + 1));
        if ($key === '' || !preg_match('/^[A-Z0-9_]+$/i', $key)) {
            continue;
        }

        $quote = $value[0] ?? '';
        if (($quote === '"' || $quote === "'") && ($end = strpos($value, $quote, 1)) !== false) {
            // Nilai berkutip: ambil isi di antara kutip, abaikan sisa (komentar).
            $value = substr($value, 1, $end - 1);
            if ($quote === '"') {
                $value = str_replace(['\\n', '\\"'], ["\n", '"'], $value);
            }
        } else {
            // Nilai tanpa kutip: buang komentar di akhir baris (" #...").
            $hash = strpos($value, ' #');
            if ($hash !== false) {
                $value = rtrim(substr($value, 0, $hash));
            }
        }

        $values[$key] = $value;
    }

    return $values;
}

/**
 * Ambil nilai konfigurasi. Nilai dari .env menimpa default di bawah.
 */
function config(string $key, mixed $default = null): mixed
{
    static $config = null;

    if ($config === null) {
        $defaults = [
            'APP_NAME'           => 'Portal Info Peserta',
            'APP_ENV'            => 'production',
            'APP_URL'            => '',
            'APP_TIMEZONE'       => 'Asia/Jakarta',

            'DB_HOST'            => '127.0.0.1',
            'DB_PORT'            => '3306',
            'DB_NAME'            => 'portal_info_peserta',
            'DB_USER'            => 'root',
            'DB_PASSWORD'        => '',

            'SESSION_NAME'       => 'pip_session',
            'REMEMBER_COOKIE'    => 'pip_remember',
            'REMEMBER_DAYS'      => '30',
            'ADMIN_IDLE_MINUTES' => '120',

            'LOGIN_MAX_ATTEMPTS' => '5',
            'LOGIN_LOCK_MINUTES' => '15',

            'CSV_DELIMITER'      => ';',
        ];

        $config = array_merge($defaults, parse_env_file(BASE_PATH . '/.env'));
    }

    return array_key_exists($key, $config) ? $config[$key] : $default;
}
