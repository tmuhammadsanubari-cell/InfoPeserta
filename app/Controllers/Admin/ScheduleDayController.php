<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\ScheduleDay;
use PDOException;

/**
 * Kelola hari dan itinerary.
 * PRD: 2.2.5
 *
 *   - index(): daftar hari beserta kegiatannya
 *   - create(), store(), edit(int $id), update(int $id), destroy(int $id) — hapus ikut menghapus kegiatan (CASCADE)
 */
final class ScheduleDayController
{
    public function index(): void
    {
        View::render('admin/schedule/index', [
            'title'    => 'Jadwal & Itinerary',
            'days'     => ScheduleDay::allWithItems(),
            'todayStr' => date('Y-m-d'),
        ], 'admin');
    }

    public function create(): void
    {
        // Saran isian: nomor dan tanggal setelah hari terakhir.
        $days = ScheduleDay::all();
        $last = $days !== [] ? end($days) : null;
        $date = $last !== null
            ? date('Y-m-d', strtotime($last['date'] . ' +1 day'))
            : (setting('event_start_date') ?? date('Y-m-d'));

        View::render('admin/schedule/day-form', [
            'title' => 'Tambah Hari',
            'day'   => [
                'id'         => null,
                'day_number' => $last !== null ? (int) $last['day_number'] + 1 : 1,
                'date'       => $date,
                'title'      => '',
                'route'      => '',
            ],
        ], 'admin');
    }

    public function store(): void
    {
        $data = $this->validated(null, '/admin/jadwal/hari/tambah');
        $id = $this->save(null, $data, '/admin/jadwal/hari/tambah');

        Flash::set('success', 'Hari ' . $data['day_number'] . ' berhasil ditambahkan.');
        redirect('/admin/jadwal#hari-' . $id);
    }

    public function edit(int $id): void
    {
        $day = ScheduleDay::find($id);
        if ($day === null) {
            View::error(404, ['title' => 'Hari tidak ditemukan']);
            return;
        }

        View::render('admin/schedule/day-form', ['title' => 'Ubah Hari', 'day' => $day], 'admin');
    }

    public function update(int $id): void
    {
        if (ScheduleDay::find($id) === null) {
            View::error(404, ['title' => 'Hari tidak ditemukan']);
            return;
        }

        $back = '/admin/jadwal/hari/' . $id . '/edit';
        $data = $this->validated($id, $back);
        $this->save($id, $data, $back);

        Flash::set('success', 'Hari ' . $data['day_number'] . ' berhasil disimpan.');
        redirect('/admin/jadwal#hari-' . $id);
    }

    public function destroy(int $id): void
    {
        $day = ScheduleDay::find($id);
        if ($day === null) {
            View::error(404, ['title' => 'Hari tidak ditemukan']);
            return;
        }

        ScheduleDay::delete($id);
        Flash::set('success', 'Hari ' . $day['day_number'] . ' beserta kegiatannya sudah dihapus.');
        redirect('/admin/jadwal');
    }

    /** Validasi input; jika gagal kembali ke form dengan pesan per field. */
    private function validated(?int $id, string $back): array
    {
        $data = [
            'day_number' => trim((string) ($_POST['day_number'] ?? '')),
            'date'       => trim((string) ($_POST['date'] ?? '')),
            'title'      => trim((string) ($_POST['title'] ?? '')),
            'route'      => trim((string) ($_POST['route'] ?? '')),
        ];

        $errors = Validator::make($data, [
            'day_number' => 'required|range:1,255',
            'date'       => 'required|date',
            'title'      => 'required|max:150',
            'route'      => 'max:255',
        ], [
            'day_number' => 'Nomor hari',
            'date'       => 'Tanggal',
            'title'      => 'Judul hari',
            'route'      => 'Rute itinerary',
        ]);

        if (!isset($errors['day_number'])) {
            $same = ScheduleDay::findByNumber((int) $data['day_number']);
            if ($same !== null && (int) $same['id'] !== $id) {
                $errors['day_number'] = 'Hari ' . (int) $data['day_number'] . ' sudah ada. Pakai nomor lain.';
            }
        }

        if ($errors !== []) {
            Flash::withErrors($errors, $data);
            redirect($back);
        }

        $data['day_number'] = (int) $data['day_number'];
        return $data;
    }

    private function save(?int $id, array $data, string $back): int
    {
        try {
            if ($id === null) {
                return ScheduleDay::create($data);
            }
            ScheduleDay::update($id, $data);
            return $id;
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                Flash::withErrors(['day_number' => 'Hari ' . $data['day_number'] . ' sudah ada. Pakai nomor lain.'], $data);
                redirect($back);
            }
            throw $e;
        }
    }
}
