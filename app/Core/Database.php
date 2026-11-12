<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOStatement;

/**
 * Koneksi PDO tunggal (singleton).
 * PRD: 1.8, NFR-004
 *
 *   - static pdo(): PDO — DSN mysql dengan charset utf8mb4
 *   - ATTR_ERRMODE => ERRMODE_EXCEPTION, ATTR_DEFAULT_FETCH_MODE => FETCH_ASSOC, ATTR_EMULATE_PREPARES => false
 *   - Helper: fetch(), fetchAll(), fetchValue(), execute(), lastInsertId()
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
                (string) config('DB_HOST'),
                (int) config('DB_PORT'),
                (string) config('DB_NAME')
            );

            self::$pdo = new PDO($dsn, (string) config('DB_USER'), (string) config('DB_PASSWORD'), [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);

            // Samakan zona waktu MySQL dengan PHP agar NOW() dan kolom TIMESTAMP konsisten.
            self::$pdo->exec("SET time_zone = '" . date('P') . "'");
        }

        return self::$pdo;
    }

    /** Jalankan query dengan prepared statement. */
    public static function execute(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Satu baris atau null. */
    public static function fetch(string $sql, array $params = []): ?array
    {
        $row = self::execute($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Semua baris. */
    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::execute($sql, $params)->fetchAll();
    }

    /** Nilai kolom pertama dari baris pertama, atau null. */
    public static function fetchValue(string $sql, array $params = []): mixed
    {
        $value = self::execute($sql, $params)->fetchColumn();
        return $value === false ? null : $value;
    }

    public static function lastInsertId(): int
    {
        return (int) self::pdo()->lastInsertId();
    }
}
