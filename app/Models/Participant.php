<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

/**
 * Query tabel participants.
 * PRD: 4.2 (participants)
 *
 *   - findByPhone(string $phone): ?array, find(int $id): ?array, create(array $data): int
 *   - paginate(?string $q, ?int $departmentId, int $page, int $perPage = 50): array
 *   - allForExport(?string $q, ?int $departmentId): iterable, delete(int $id), touchLogin(int $id)
 *   - countAll(), countSince(string $datetime), countByDepartment()
 *
 * Nomor HP yang masuk ke method ini harus sudah lewat normalize_phone().
 */
final class Participant
{
    private const SELECT = 'SELECT p.id, p.full_name, p.department_id, p.phone, p.last_login_at, p.created_at,
                                   d.name AS department_name
                            FROM participants p
                            JOIN departments d ON d.id = p.department_id';

    public static function findByPhone(string $phone): ?array
    {
        return Database::fetch(self::SELECT . ' WHERE p.phone = ?', [$phone]);
    }

    /** Peserta beserta nama departemen. */
    public static function find(int $id): ?array
    {
        return Database::fetch(self::SELECT . ' WHERE p.id = ?', [$id]);
    }

    /** @param array{full_name: string, department_id: int, phone: string} $data */
    public static function create(array $data): int
    {
        Database::execute(
            'INSERT INTO participants (full_name, department_id, phone) VALUES (?, ?, ?)',
            [$data['full_name'], (int) $data['department_id'], $data['phone']]
        );
        return Database::lastInsertId();
    }

    public static function touchLogin(int $id): void
    {
        Database::execute('UPDATE participants SET last_login_at = NOW() WHERE id = ?', [$id]);
    }

    /** Sesi peserta ikut terhapus lewat ON DELETE CASCADE. */
    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM participants WHERE id = ?', [$id]);
    }

    /**
     * Daftar peserta untuk admin, terbaru dulu.
     * @return array{rows: array, total: int, page: int, pages: int, per_page: int}
     */
    public static function paginate(?string $q, ?int $departmentId, int $page = 1, int $perPage = 50): array
    {
        [$where, $params] = self::filters($q, $departmentId);

        $total = (int) Database::fetchValue('SELECT COUNT(*) FROM participants p' . $where, $params);
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min(max(1, $page), $pages);
        $offset = ($page - 1) * $perPage;

        $rows = Database::fetchAll(
            self::SELECT . $where . ' ORDER BY p.created_at DESC, p.id DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset,
            $params
        );

        return ['rows' => $rows, 'total' => $total, 'page' => $page, 'pages' => $pages, 'per_page' => $perPage];
    }

    /** Semua baris sesuai filter, untuk di-stream ke CSV. */
    public static function allForExport(?string $q, ?int $departmentId): iterable
    {
        [$where, $params] = self::filters($q, $departmentId);
        return Database::execute(self::SELECT . $where . ' ORDER BY p.created_at DESC, p.id DESC', $params);
    }

    public static function countAll(): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM participants');
    }

    public static function countSince(string $datetime): int
    {
        return (int) Database::fetchValue('SELECT COUNT(*) FROM participants WHERE created_at >= ?', [$datetime]);
    }

    /** Jumlah peserta per departemen (termasuk departemen yang belum punya peserta). */
    public static function countByDepartment(): array
    {
        return Database::fetchAll(
            'SELECT d.id, d.name, d.is_active, COUNT(p.id) AS total
             FROM departments d
             LEFT JOIN participants p ON p.department_id = d.id
             GROUP BY d.id
             ORDER BY d.sort_order, d.name'
        );
    }

    /** @return array{0: string, 1: array} */
    private static function filters(?string $q, ?int $departmentId): array
    {
        $conditions = [];
        $params = [];

        $q = trim((string) $q);
        if ($q !== '') {
            $like = '%' . addcslashes($q, '%_\\') . '%';
            $condition = 'p.full_name LIKE ?';
            $params[] = $like;

            // Cari nomor juga dalam bentuk ternormalisasi: "+62 812" → "0812".
            $digits = preg_replace('/\D+/', '', $q) ?? '';
            if (str_starts_with($digits, '62')) {
                $digits = '0' . substr($digits, 2);
            }
            if ($digits !== '') {
                $condition .= ' OR p.phone LIKE ?';
                $params[] = '%' . $digits . '%';
            }
            $conditions[] = '(' . $condition . ')';
        }

        if ($departmentId !== null && $departmentId > 0) {
            $conditions[] = 'p.department_id = ?';
            $params[] = $departmentId;
        }

        return [$conditions ? ' WHERE ' . implode(' AND ', $conditions) : '', $params];
    }
}
