<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel departments.
 * PRD: 4.2 (departments)
 *
 *   - active(): array (urut sort_order), all(): array, find, create, update, delete, usageCount(int $id): int
 */
final class Department
{
    /** Departemen aktif untuk dropdown registrasi. */
    public static function active(): array
    {
        return Database::fetchAll(
            'SELECT id, name FROM departments WHERE is_active = 1 ORDER BY sort_order, name'
        );
    }

    /** Semua departemen beserta jumlah peserta yang memakainya. */
    public static function all(): array
    {
        return Database::fetchAll(
            'SELECT d.*, COUNT(p.id) AS participant_count
             FROM departments d
             LEFT JOIN participants p ON p.department_id = d.id
             GROUP BY d.id
             ORDER BY d.sort_order, d.name'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM departments WHERE id = ?', [$id]);
    }

    public static function findByName(string $name): ?array
    {
        return Database::fetch('SELECT * FROM departments WHERE name = ?', [$name]);
    }

    /** @param array{name: string, sort_order?: int, is_active?: int|bool} $data */
    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO departments (name, sort_order, is_active) VALUES (?, ?, ?)',
            [$data['name'], (int) ($data['sort_order'] ?? 0), (int) (bool) ($data['is_active'] ?? true)]
        );
        return Database::lastInsertId();
    }

    /** @param array{name: string, sort_order: int, is_active: int|bool} $data */
    public static function update(int $id, array $data): void
    {
        Database::execute(
            'UPDATE departments SET name = ?, sort_order = ?, is_active = ? WHERE id = ?',
            [$data['name'], (int) $data['sort_order'], (int) (bool) $data['is_active'], $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM departments WHERE id = ?', [$id]);
    }

    public static function usageCount(int $id): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM participants WHERE department_id = ?', [$id]);
    }
}
