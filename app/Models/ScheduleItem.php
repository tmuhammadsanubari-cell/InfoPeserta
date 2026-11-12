<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel schedule_items.
 * PRD: 4.2 (schedule_items)
 *
 *   - byDay(int $dayId): array, find, create, update, delete
 */
final class ScheduleItem
{
    public static function byDay(int $dayId): array
    {
        return Database::fetchAll(
            'SELECT * FROM schedule_items WHERE day_id = ? ORDER BY start_time, sort_order, id',
            [$dayId]
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM schedule_items WHERE id = ?', [$id]);
    }

    /**
     * @param array{day_id: int, start_time: string, end_time: ?string, activity: string,
     *              location: ?string, notes: ?string, sort_order?: int} $data
     */
    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO schedule_items (day_id, start_time, end_time, activity, location, notes, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?)',
            self::values($data)
        );
        return Database::lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        Database::execute(
            'UPDATE schedule_items
             SET day_id = ?, start_time = ?, end_time = ?, activity = ?, location = ?, notes = ?, sort_order = ?
             WHERE id = ?',
            [...self::values($data), $id]
        );
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM schedule_items WHERE id = ?', [$id]);
    }

    private static function values(array $data): array
    {
        return [
            (int) $data['day_id'],
            $data['start_time'],
            ($data['end_time'] ?? '') !== '' ? $data['end_time'] : null,
            $data['activity'],
            ($data['location'] ?? '') !== '' ? $data['location'] : null,
            ($data['notes'] ?? '') !== '' ? $data['notes'] : null,
            (int) ($data['sort_order'] ?? 0),
        ];
    }
}
