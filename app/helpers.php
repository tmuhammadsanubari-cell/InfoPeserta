<?php

declare(strict_types=1);

/**
 * Fungsi bantu global.
 * PRD: 2.2.1, 2.2.3, 2.2.10, 4.1
 *
 *   - e(?string $s): string — htmlspecialchars ENT_QUOTES UTF-8
 *   - url(string $path = '/'): string dan redirect(string $path): never
 *   - normalize_phone(string $raw): ?string — buang non-angka, +62/62 → 0, wajib diawali 08, panjang 10–14
 *   - to_wa_number(string $phone): string — 08xxx → 628xxx
 *   - format_text(string $s): string — escape, nl2br, URL → <a target=_blank rel=noopener>
 *   - setting(string $key, ?string $default = null): ?string — pembungkus Models\Setting::get
 *   - old(string $field, string $default = ''): string dan error(string $field): ?string
 *   - format_date_id(string $date): string — contoh 'Minggu, 21 Desember 2026'
 */

use App\Core\Csrf;
use App\Core\Flash;
use App\Models\Setting;

// ---------------------------------------------------------------------------
// Output
// ---------------------------------------------------------------------------

function e(string|int|float|null $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Teks panjang dari admin: escape, URL jadi tautan, baris baru jadi <br>.
 * HTML dari admin tidak pernah dirender mentah (PRD 2.2.3, 2.2.6).
 */
function format_text(?string $s): string
{
    $escaped = e(str_replace(["\r\n", "\r"], "\n", (string) $s));

    // URL di teks yang sudah di-escape. Tanda baca di ujung tidak ikut jadi tautan.
    $linked = preg_replace_callback(
        '~\b(?:https?://|www\.)[^\s<]+~i',
        static function (array $m): string {
            $url = $m[0];
            $trail = '';
            while ($url !== '' && preg_match('/[.,;:!?)\]]$/', $url)) {
                // Jangan potong ";" yang menjadi bagian entitas seperti &amp;
                if (str_ends_with($url, ';') && preg_match('/&[a-z0-9#]+;$/i', $url)) {
                    break;
                }
                $trail = substr($url, -1) . $trail;
                $url = substr($url, 0, -1);
            }
            $href = stripos($url, 'www.') === 0 ? 'https://' . $url : $url;
            return '<a href="' . $href . '" target="_blank" rel="noopener">' . $url . '</a>' . $trail;
        },
        $escaped
    ) ?? $escaped;

    return nl2br($linked, false);
}

// ---------------------------------------------------------------------------
// URL & request
// ---------------------------------------------------------------------------

/**
 * Path dasar aplikasi dari APP_URL, misalnya "/portal" jika dipasang di subfolder. Kosong jika di root.
 */
function base_path(): string
{
    static $base = null;
    if ($base === null) {
        $path = parse_url((string) config('APP_URL', ''), PHP_URL_PATH) ?: '';
        $base = rtrim($path, '/');
    }
    return $base;
}

function url(string $path = '/'): string
{
    if (preg_match('~^https?://~i', $path)) {
        return $path;
    }
    return base_path() . '/' . ltrim($path, '/');
}

/**
 * URL aset statis dengan versi dari waktu ubah file, agar cache browser diperbarui setelah file diganti.
 */
function asset(string $path): string
{
    $path = ltrim($path, '/');
    $file = BASE_PATH . '/public/' . $path;
    $version = is_file($file) ? (string) filemtime($file) : '';
    return url($path) . ($version !== '' ? '?v=' . $version : '');
}

function redirect(string $path, int $status = 302): never
{
    header('Location: ' . url($path), true, $status);
    exit;
}

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
        return true;
    }
    if ((string) ($_SERVER['SERVER_PORT'] ?? '') === '443') {
        return true;
    }
    return strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';
}

function client_ip(): string
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0');
}

// ---------------------------------------------------------------------------
// Nomor HP
// ---------------------------------------------------------------------------

/**
 * Normalisasi nomor HP ke format 08xxxxxxxxxx. Kembalikan null jika tidak valid (PRD 2.2.1).
 */
function normalize_phone(string $raw): ?string
{
    $digits = preg_replace('/\D+/', '', $raw) ?? '';

    if (str_starts_with($digits, '62')) {
        $digits = '0' . substr($digits, 2);
    }

    if (!str_starts_with($digits, '08')) {
        return null;
    }

    $length = strlen($digits);
    if ($length < 10 || $length > 14) {
        return null;
    }

    return $digits;
}

/**
 * 08xxx → 628xxx untuk tautan wa.me (PRD 2.2.10).
 */
function to_wa_number(string $phone): string
{
    $digits = preg_replace('/\D+/', '', $phone) ?? '';
    if (str_starts_with($digits, '0')) {
        return '62' . substr($digits, 1);
    }
    return $digits;
}

// ---------------------------------------------------------------------------
// Pengaturan, form, flash
// ---------------------------------------------------------------------------

function setting(string $key, ?string $default = null): ?string
{
    return Setting::get($key, $default);
}

function csrf_field(): string
{
    return Csrf::field();
}

function old(string $field, string $default = ''): string
{
    $old = Flash::old();
    if (!array_key_exists($field, $old) || is_array($old[$field])) {
        return $default;
    }
    return (string) $old[$field];
}

function error(string $field): ?string
{
    return Flash::errors()[$field] ?? null;
}

// ---------------------------------------------------------------------------
// Tanggal (Bahasa Indonesia)
// ---------------------------------------------------------------------------

const ID_DAYS = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
const ID_MONTHS = [
    1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
];

/**
 * '2026-12-21' → 'Senin, 21 Desember 2026'. Tanpa nama hari jika $withDay = false.
 */
function format_date_id(string $date, bool $withDay = true): string
{
    $ts = strtotime($date);
    if ($ts === false) {
        return $date;
    }
    $text = (int) date('j', $ts) . ' ' . ID_MONTHS[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    return $withDay ? ID_DAYS[(int) date('w', $ts)] . ', ' . $text : $text;
}

/**
 * Rentang tanggal kegiatan, ringkas jika bulan/tahun sama.
 * Contoh: '20–22 Desember 2026', '30 Desember 2026 – 2 Januari 2027'.
 */
function format_date_range_id(?string $start, ?string $end): string
{
    $s = $start ? strtotime($start) : false;
    $t = $end ? strtotime($end) : false;

    if ($s === false && $t === false) {
        return '';
    }
    if ($s === false || $t === false || date('Y-m-d', $s) === date('Y-m-d', $t)) {
        return format_date_id(date('Y-m-d', $s ?: $t), false);
    }

    if (date('Y-m', $s) === date('Y-m', $t)) {
        return date('j', $s) . '–' . format_date_id(date('Y-m-d', $t), false);
    }
    if (date('Y', $s) === date('Y', $t)) {
        return (int) date('j', $s) . ' ' . ID_MONTHS[(int) date('n', $s)] . ' – ' . format_date_id(date('Y-m-d', $t), false);
    }
    return format_date_id(date('Y-m-d', $s), false) . ' – ' . format_date_id(date('Y-m-d', $t), false);
}

// ---------------------------------------------------------------------------
// Ikon SVG inline (gaya line, stroke 2; docs/UI-GUIDE.md)
// ---------------------------------------------------------------------------

function icon(string $name, string $class = 'icon'): string
{
    static $paths = [
        'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
        'map-pin'      => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/>',
        'calendar'     => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
        'route'        => '<circle cx="6" cy="19" r="3"/><path d="M9 19h8.5a3.5 3.5 0 0 0 0-7h-11a3.5 3.5 0 0 1 0-7H15"/><circle cx="18" cy="5" r="3"/>',
        'megaphone'    => '<path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>',
        'info'         => '<circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/>',
        'arrow-right'  => '<path d="M5 12h14M12 5l7 7-7 7"/>',
        'arrow-left'   => '<path d="M19 12H5M12 19l-7-7 7-7"/>',
        'log-in'       => '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="m10 17 5-5-5-5M15 12H3"/>',
        'refresh'      => '<path d="M21 12a9 9 0 1 1-2.64-6.36L21 8"/><path d="M21 3v5h-5"/>',
        'phone'        => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2z"/>',
    ];

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"'
        . ' stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">'
        . ($paths[$name] ?? '') . '</svg>';
}

// ---------------------------------------------------------------------------
// Log
// ---------------------------------------------------------------------------

function log_error(Throwable|string $error): void
{
    $message = $error instanceof Throwable
        ? sprintf(
            '%s: %s in %s:%d%sStack trace:%s%s',
            get_class($error),
            $error->getMessage(),
            $error->getFile(),
            $error->getLine(),
            PHP_EOL,
            PHP_EOL,
            $error->getTraceAsString()
        )
        : $error;

    $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
    @file_put_contents(BASE_PATH . '/storage/logs/app.log', $line, FILE_APPEND | LOCK_EX);
}
