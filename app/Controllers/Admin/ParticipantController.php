<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

/**
 * Data peserta dan ekspor CSV.
 * PRD: 2.2.7
 *
 * TODO:
 *   - index(): cari (nama/HP), filter department_id, paginasi 50, urut terbaru
 *   - export(): stream CSV peserta-{YYYYMMDD-HHmm}.csv sesuai filter; BOM UTF-8; delimiter CSV_DELIMITER; HP ditulis ="08..."
 *   - destroy(int $id): hapus peserta (sesi ikut terhapus via CASCADE)
 */
final class ParticipantController
{
}
