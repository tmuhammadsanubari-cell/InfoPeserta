<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel announcements.
 * PRD: 4.2 (announcements)
 *
 *   - listed(): array — is_pinned DESC, published_at DESC
 *   - hasRecent(int $hours = 24): bool
 *   - find, create, update, delete
 */
final class Announcement
{
    public static function listed(): array
    {
        return Database::fetchAll(
            'SELECT * FROM announcements ORDER BY is_pinned DESC, published_at DESC, id DESC'
        );
    }

    /** Ada pengumuman yang terbit dalam $hours jam terakhir (dan bukan dijadwalkan di masa depan). */
    public static function hasRecent(int $hours = 24): bool
    {
        return Database::fetchValue(
            'SELECT 1 FROM announcements WHERE published_at >= NOW() - INTERVAL ? HOUR AND published_at <= NOW() LIMIT 1',
            [$hours]
        ) !== null;
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM announcements WHERE id = ?', [$id]);
    }

    /** published_at terisi otomatis (DEFAULT CURRENT_TIMESTAMP). */
    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO announcements (title, body, is_pinned) VALUES (?, ?, ?)',
            [$data['title'], $data['body'], (int) (bool) ($data['is_pinned'] ?? false)]
        );
        return Database::lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::execute(
            'UPDATE announcements SET title = ?, body = ?, is_pinned = ? WHERE id = ?',
            [$data['title'], $data['body'], (int) (bool) ($data['is_pinned'] ?? false), $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM announcements WHERE id = ?', [$id]);
    }
}
