<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Pesan flash, error per field, dan old input di sesi.
 * PRD: 4.5 (format pesan error form)
 *
 *   - static set(string $type, string $message): void dan static get(): array
 *   - static withErrors(array $errors, array $old): void
 *   - static errors(): array dan static old(): array — dibaca sekali lalu dihapus
 *
 * Data dari request sebelumnya dipindah dari sesi ke memori saat pertama kali dibaca,
 * sehingga bisa dibaca berkali-kali di satu request tetapi tidak muncul lagi di request berikutnya.
 */
final class Flash
{
    private const KEY = '_flash';

    /** Kolom yang tidak pernah disimpan sebagai old input. */
    private const NEVER_OLD = ['_csrf', 'password', 'password_confirmation', 'current_password', 'new_password'];

    private static ?array $current = null;

    /** @param string $type success | error | info */
    public static function set(string $type, string $message): void
    {
        $_SESSION[self::KEY]['messages'][] = ['type' => $type, 'message' => $message];
    }

    /** @return list<array{type: string, message: string}> */
    public static function get(): array
    {
        return self::load()['messages'];
    }

    public static function withErrors(array $errors, array $old = []): void
    {
        foreach (self::NEVER_OLD as $field) {
            unset($old[$field]);
        }
        $_SESSION[self::KEY]['errors'] = $errors;
        $_SESSION[self::KEY]['old'] = $old;
    }

    /** @return array<string, string> */
    public static function errors(): array
    {
        return self::load()['errors'];
    }

    public static function old(): array
    {
        return self::load()['old'];
    }

    private static function load(): array
    {
        $pending = $_SESSION[self::KEY] ?? [];
        unset($_SESSION[self::KEY]);

        $current = self::$current ?? ['messages' => [], 'errors' => [], 'old' => []];
        $current['messages'] = array_merge($current['messages'], $pending['messages'] ?? []);
        $current['errors'] = array_merge($current['errors'], $pending['errors'] ?? []);
        $current['old'] = array_merge($current['old'], $pending['old'] ?? []);

        return self::$current = $current;
    }
}
