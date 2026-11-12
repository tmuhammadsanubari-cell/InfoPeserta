<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel info_sections.
 * PRD: 4.2 (info_sections)
 *
 *   - ordered(): array, find, create, update, delete
 */
final class InfoSection
{
    public static function ordered(): array
    {
        return Database::fetchAll('SELECT * FROM info_sections ORDER BY sort_order, id');
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM info_sections WHERE id = ?', [$id]);
    }

    /** @param array{title: string, body: string, sort_order?: int} $data */
    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO info_sections (title, body, sort_order) VALUES (?, ?, ?)',
            [$data['title'], $data['body'], (int) ($data['sort_order'] ?? 0)]
        );
        return Database::lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::execute(
            'UPDATE info_sections SET title = ?, body = ?, sort_order = ? WHERE id = ?',
            [$data['title'], $data['body'], (int) ($data['sort_order'] ?? 0), $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM info_sections WHERE id = ?', [$id]);
    }
}
