<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Query tabel participants.
 * PRD: 4.2 (participants)
 *
 * TODO:
 *   - findByPhone(string $phone): ?array, find(int $id): ?array, create(array $data): int
 *   - paginate(?string $q, ?int $departmentId, int $page, int $perPage = 50): array
 *   - allForExport(?string $q, ?int $departmentId): iterable, delete(int $id), touchLogin(int $id)
 *   - countAll(), countSince(string $datetime), countByDepartment()
 */
final class Participant
{
}
