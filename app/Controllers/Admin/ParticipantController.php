<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\View;
use App\Models\Department;
use App\Models\Participant;

/**
 * Data peserta dan ekspor CSV.
 * PRD: 2.2.7
 *
 *   - index(): cari (nama/HP), filter department_id, paginasi 50, urut terbaru
 *   - export(): stream CSV peserta-{YYYYMMDD-HHmm}.csv sesuai filter; BOM UTF-8; delimiter CSV_DELIMITER; HP ditulis ="08..."
 *   - destroy(int $id): hapus peserta (sesi ikut terhapus via CASCADE)
 */
final class ParticipantController
{
    private const PER_PAGE = 50;

    public function index(): void
    {
        [$q, $departmentId] = self::filters($_GET);
        $page = isset($_GET['page']) && is_string($_GET['page']) && ctype_digit($_GET['page']) ? (int) $_GET['page'] : 1;

        View::render('admin/participants/index', [
            'title'        => 'Data Peserta',
            'q'            => $q,
            'departmentId' => $departmentId,
            'departments'  => Department::all(),
            'result'       => Participant::paginate($q, $departmentId, $page, self::PER_PAGE),
            'totalAll'     => Participant::countAll(),
        ], 'admin');
    }

    public function export(): void
    {
        [$q, $departmentId] = self::filters($_GET);

        $rows = Participant::allForExport($q, $departmentId);
        $delimiter = self::delimiter();
        $filename = 'peserta-' . date('Ymd-Hi') . '.csv';

        // Sesi tidak dibutuhkan lagi; lepas kunci agar unduhan besar tidak menahan request lain.
        session_write_close();

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, max-age=0');
        header('X-Content-Type-Options: nosniff');

        $out = fopen('php://output', 'wb');
        fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8

        fputcsv($out, ['No', 'Nama Lengkap', 'Departemen', 'Nomor HP', 'Waktu Daftar', 'Terakhir Masuk'], $delimiter, '"', '');

        $no = 0;
        foreach ($rows as $row) {
            fputcsv($out, [
                ++$no,
                self::safeCell((string) $row['full_name']),
                self::safeCell((string) $row['department_name']),
                '="' . $row['phone'] . '"', // dipaksa teks agar angka 0 di depan tidak hilang
                date('d/m/Y H:i', strtotime((string) $row['created_at'])),
                $row['last_login_at'] ? date('d/m/Y H:i', strtotime((string) $row['last_login_at'])) : '',
            ], $delimiter, '"', '');
        }

        fclose($out);
        exit;
    }

    public function destroy(int $id): void
    {
        $participant = Participant::find($id);
        if ($participant === null) {
            View::error(404, ['title' => 'Peserta tidak ditemukan']);
            return;
        }

        Participant::delete($id);
        Flash::set('success', 'Peserta "' . $participant['full_name'] . '" sudah dihapus.');

        // Kembali ke daftar dengan filter dan halaman yang sama.
        [$q, $departmentId] = self::filters($_POST);
        $page = isset($_POST['page']) && is_string($_POST['page']) && ctype_digit($_POST['page']) ? (int) $_POST['page'] : 1;
        $query = http_build_query(array_filter([
            'q'             => $q,
            'department_id' => $departmentId,
            'page'          => $page > 1 ? $page : null,
        ]));
        redirect('/admin/peserta' . ($query !== '' ? '?' . $query : ''));
    }

    /** @return array{0: ?string, 1: ?int} */
    private static function filters(array $source): array
    {
        $q = isset($source['q']) && is_string($source['q']) ? mb_substr(trim($source['q']), 0, 100) : '';
        $dept = isset($source['department_id']) && is_string($source['department_id']) && ctype_digit($source['department_id'])
            ? (int) $source['department_id']
            : 0;
        return [$q !== '' ? $q : null, $dept > 0 ? $dept : null];
    }

    /** CSV_DELIMITER dari .env: satu karakter, atau "\t" untuk tab. Default ";" (Excel lokal Indonesia). */
    private static function delimiter(): string
    {
        $value = (string) config('CSV_DELIMITER', ';');
        if ($value === '\t' || strtolower($value) === 'tab') {
            return "\t";
        }
        return $value !== '' ? $value[0] : ';';
    }

    /** Cegah CSV/formula injection: sel yang diawali = + - @ diberi tanda kutip tunggal. */
    private static function safeCell(string $value): string
    {
        return $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'" . $value : $value;
    }
}
