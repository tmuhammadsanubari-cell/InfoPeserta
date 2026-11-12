<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel admins.
 * PRD: 4.2 (admins)
 *
 *   - findByUsername(string $u): ?array, find(int $id), updatePassword(int $id, string $hash), touchLogin(int $id)
 */
final class Admin
{
    public static function findByUsername(string $username): ?array
    {
        return Database::fetch('SELECT * FROM admins WHERE username = ?', [$username]);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM admins WHERE id = ?', [$id]);
    }

    /** @param string $hash hasil password_hash() */
    public static function updatePassword(int $id, string $hash): void
    {
        Database::execute('UPDATE admins SET password_hash = ? WHERE id = ?', [$hash, $id]);
    }

    public static function touchLogin(int $id): void
    {
        Database::execute('UPDATE admins SET last_login_at = NOW() WHERE id = ?', [$id]);
    }
}
