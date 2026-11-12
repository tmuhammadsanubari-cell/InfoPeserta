<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Token CSRF per sesi.
 * PRD: NFR-004, 4.5 (CSRF_MISMATCH 419)
 *
 *   - static token(): string — buat dengan bin2hex(random_bytes(32)) jika belum ada
 *   - static field(): string — <input type=hidden name=_csrf>
 *   - static verify(?string $token): bool — hash_equals
 */
final class Csrf
{
    private const KEY = '_csrf';

    public static function token(): string
    {
        if (empty($_SESSION[self::KEY]) || !is_string($_SESSION[self::KEY])) {
            $_SESSION[self::KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::KEY];
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . e(self::token()) . '">';
    }

    public static function verify(mixed $token): bool
    {
        $expected = $_SESSION[self::KEY] ?? null;
        if (!is_string($expected) || $expected === '' || !is_string($token) || $token === '') {
            return false;
        }
        return hash_equals($expected, $token);
    }
}
