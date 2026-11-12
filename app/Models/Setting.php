<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Pengaturan key-value, di-cache per request.
 * PRD: 4.2 (settings), 2.2.9
 *
 *   - static all(): array (cache statis), static get(string $key, ?string $default = null): ?string
 *   - static setMany(array $pairs): void — INSERT ... ON DUPLICATE KEY UPDATE
 */
final class Setting
{
    private static ?array $cache = null;

    /** @return array<string, ?string> */
    public static function all(): array
    {
        if (self::$cache === null) {
            $rows = Database::fetchAll('SELECT setting_key, setting_value FROM settings');
            self::$cache = array_column($rows, 'setting_value', 'setting_key');
        }
        return self::$cache;
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = self::all()[$key] ?? null;
        return ($value === null || $value === '') ? $default : (string) $value;
    }

    /** @param array<string, ?string> $pairs */
    public static function setMany(array $pairs): void
    {
        if ($pairs === []) {
            return;
        }

        $pdo = Database::pdo();
        $stmt = $pdo->prepare(
            'INSERT INTO settings (setting_key, setting_value) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
        );

        $pdo->beginTransaction();
        try {
            foreach ($pairs as $key => $value) {
                $stmt->execute(['k' => (string) $key, 'v' => $value]);
            }
            $pdo->commit();
        } catch (\Throwable $e) {
            $pdo->rollBack();
            throw $e;
        }

        self::$cache = null;
    }
}
