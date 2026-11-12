<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel schedule_days.
 * PRD: 4.2 (schedule_days)
 *
 *   - allWithItems(): array — hari urut day_number, kegiatan urut start_time, sort_order
 *   - find, create, update, delete
 */
final class ScheduleDay
{
    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM schedule_days ORDER BY day_number');
    }

    /** Semua hari, masing-masing dengan key 'items' berisi kegiatan terurut. Dua query. */
    public static function allWithItems(): array
    {
        $days = [];
        foreach (self::all() as $day) {
            $day['items'] = [];
            $days[(int) $day['id']] = $day;
        }

        if ($days !== []) {
            $items = Database::fetchAll(
                'SELECT * FROM schedule_items ORDER BY day_id, start_time, sort_order, id'
            );
            foreach ($items as $item) {
                $dayId = (int) $item['day_id'];
                if (isset($days[$dayId])) {
                    $days[$dayId]['items'][] = $item;
                }
            }
        }

        return array_values($days);
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM schedule_days WHERE id = ?', [$id]);
    }

    public static function findByNumber(int $dayNumber): ?array
    {
        return Database::fetch('SELECT * FROM schedule_days WHERE day_number = ?', [$dayNumber]);
    }

    /** @param array{day_number: int, date: string, title: string, route: ?string} $data */
    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO schedule_days (day_number, `date`, title, route) VALUES (?, ?, ?, ?)',
            [(int) $data['day_number'], $data['date'], $data['title'], $data['route'] ?: null]
        );
        return Database::lastInsertId();
    }

    /** @param array{day_number: int, date: string, title: string, route: ?string} $data */
    public static function update(int $id, array $data): void
    {
        Database::execute(
            'UPDATE schedule_days SET day_number = ?, `date` = ?, title = ?, route = ? WHERE id = ?',
            [(int) $data['day_number'], $data['date'], $data['title'], $data['route'] ?: null, $id]
        );
    }

    /** Kegiatan di hari ini ikut terhapus lewat ON DELETE CASCADE. */
    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM schedule_days WHERE id = ?', [$id]);
    }
}
