<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

/**
 * Render template PHP di app/Views dengan layout.
 * PRD: 4.1
 *
 *   - static render(string $template, array $data = [], ?string $layout = 'participant'): void
 *   - extract($data), output buffering, isi variabel $content untuk layout
 *   - static status(int $code): set http_response_code sebelum render halaman error
 */
final class View
{
    public static function render(string $template, array $data = [], ?string $layout = 'participant'): void
    {
        $content = self::capture($template, $data);

        if ($layout === null) {
            echo $content;
            return;
        }

        echo self::capture('layouts/' . $layout, $data + ['content' => $content]);
    }

    /** Render template ke string (dipakai juga untuk potongan/partial). */
    public static function capture(string $template, array $data = []): string
    {
        $file = BASE_PATH . '/app/Views/' . $template . '.php';
        if (!preg_match('~^[a-z0-9_/-]+$~i', $template) || !is_file($file)) {
            throw new RuntimeException('View tidak ditemukan: ' . $template);
        }

        $level = ob_get_level();
        ob_start();
        try {
            (static function (string $__file, array $__data): void {
                extract($__data, EXTR_SKIP);
                require $__file;
            })($file, $data);
        } catch (\Throwable $e) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            throw $e;
        }

        return (string) ob_get_clean();
    }

    public static function status(int $code): void
    {
        if (!headers_sent()) {
            http_response_code($code);
        }
    }

    /** Halaman error (404, 419, 429, 500) dengan status HTTP yang sesuai. */
    public static function error(int $code, array $data = []): void
    {
        self::status($code);
        self::render('errors/' . $code, $data);
    }
}
