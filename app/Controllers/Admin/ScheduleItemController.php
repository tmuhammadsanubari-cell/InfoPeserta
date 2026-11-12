<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\ScheduleDay;
use App\Models\ScheduleItem;

/**
 * Kelola kegiatan per hari.
 * PRD: 2.2.5
 *
 *   - create() dengan ?day_id=, store(), edit(int $id), update(int $id), destroy(int $id)
 *   - Validasi: end_time > start_time jika diisi
 */
final class ScheduleItemController
{
    public function create(): void
    {
        $days = ScheduleDay::all();
        if ($days === []) {
            Flash::set('info', 'Tambahkan hari terlebih dahulu sebelum menambah kegiatan.');
            redirect('/admin/jadwal/hari/tambah');
        }

        $dayId = isset($_GET['day_id']) && is_string($_GET['day_id']) && ctype_digit($_GET['day_id'])
            ? (int) $_GET['day_id']
            : (int) $days[0]['id'];

        View::render('admin/schedule/item-form', [
            'title' => 'Tambah Kegiatan',
            'days'  => $days,
            'item'  => [
                'id' => null, 'day_id' => $dayId, 'start_time' => '', 'end_time' => '',
                'activity' => '', 'location' => '', 'notes' => '', 'sort_order' => 0,
            ],
        ], 'admin');
    }

    public function store(): void
    {
        $data = $this->validated('/admin/jadwal/kegiatan/tambah?day_id=' . rawurlencode((string) ($_POST['day_id'] ?? '')));
        ScheduleItem::create($data);

        Flash::set('success', 'Kegiatan "' . $data['activity'] . '" berhasil ditambahkan.');
        redirect('/admin/jadwal#hari-' . $data['day_id']);
    }

    public function edit(int $id): void
    {
        $item = ScheduleItem::find($id);
        if ($item === null) {
            View::error(404, ['title' => 'Kegiatan tidak ditemukan']);
            return;
        }

        View::render('admin/schedule/item-form', [
            'title' => 'Ubah Kegiatan',
            'days'  => ScheduleDay::all(),
            'item'  => $item,
        ], 'admin');
    }

    public function update(int $id): void
    {
        if (ScheduleItem::find($id) === null) {
            View::error(404, ['title' => 'Kegiatan tidak ditemukan']);
            return;
        }

        $data = $this->validated('/admin/jadwal/kegiatan/' . $id . '/edit');
        ScheduleItem::update($id, $data);

        Flash::set('success', 'Kegiatan "' . $data['activity'] . '" berhasil disimpan.');
        redirect('/admin/jadwal#hari-' . $data['day_id']);
    }

    public function destroy(int $id): void
    {
        $item = ScheduleItem::find($id);
        if ($item === null) {
            View::error(404, ['title' => 'Kegiatan tidak ditemukan']);
            return;
        }

        ScheduleItem::delete($id);
        Flash::set('success', 'Kegiatan "' . $item['activity'] . '" sudah dihapus.');
        redirect('/admin/jadwal#hari-' . $item['day_id']);
    }

    private function validated(string $back): array
    {
        $data = [
            'day_id'     => trim((string) ($_POST['day_id'] ?? '')),
            'start_time' => trim((string) ($_POST['start_time'] ?? '')),
            'end_time'   => trim((string) ($_POST['end_time'] ?? '')),
            'activity'   => trim((string) ($_POST['activity'] ?? '')),
            'location'   => trim((string) ($_POST['location'] ?? '')),
            'notes'      => trim(str_replace("\r\n", "\n", (string) ($_POST['notes'] ?? ''))),
            'sort_order' => trim((string) ($_POST['sort_order'] ?? '')),
        ];

        $errors = Validator::make($data, [
            'day_id'     => 'required|integer',
            'start_time' => 'required|time',
            'end_time'   => 'time',
            'activity'   => 'required|max:150',
            'location'   => 'max:200',
            'notes'      => 'max:2000',
            'sort_order' => 'range:0,9999',
        ], [
            'day_id'     => 'Hari',
            'start_time' => 'Jam mulai',
            'end_time'   => 'Jam selesai',
            'activity'   => 'Nama kegiatan',
            'location'   => 'Lokasi',
            'notes'      => 'Catatan',
            'sort_order' => 'Urutan',
        ]);

        if (!isset($errors['day_id']) && ScheduleDay::find((int) $data['day_id']) === null) {
            $errors['day_id'] = 'Pilih hari dari daftar.';
        }

        if (!isset($errors['start_time']) && !isset($errors['end_time']) && $data['end_time'] !== ''
            && substr($data['end_time'], 0, 5) <= substr($data['start_time'], 0, 5)) {
            $errors['end_time'] = 'Jam selesai harus lebih besar dari jam mulai.';
        }

        if ($errors !== []) {
            Flash::withErrors($errors, $data);
            redirect($back);
        }

        $data['day_id'] = (int) $data['day_id'];
        $data['start_time'] = substr($data['start_time'], 0, 5) . ':00';
        $data['end_time'] = $data['end_time'] !== '' ? substr($data['end_time'], 0, 5) . ':00' : null;
        $data['sort_order'] = (int) $data['sort_order'];
        return $data;
    }
}
