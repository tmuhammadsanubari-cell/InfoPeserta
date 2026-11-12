<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\Announcement;

/**
 * CRUD pengumuman.
 * PRD: 2.2.6
 *
 *   - index, create, store, edit, update, destroy; judul maks 150 karakter; checkbox is_pinned
 */
final class AnnouncementController
{
    public function index(): void
    {
        View::render('admin/announcements/index', [
            'title'         => 'Pengumuman',
            'announcements' => Announcement::listed(),
        ], 'admin');
    }

    public function create(): void
    {
        View::render('admin/announcements/form', [
            'title'        => 'Tambah Pengumuman',
            'announcement' => ['id' => null, 'title' => '', 'body' => '', 'is_pinned' => 0],
        ], 'admin');
    }

    public function store(): void
    {
        $data = $this->validated('/admin/pengumuman/tambah');
        Announcement::create($data);

        Flash::set('success', 'Pengumuman berhasil diterbitkan.');
        redirect('/admin/pengumuman');
    }

    public function edit(int $id): void
    {
        $announcement = Announcement::find($id);
        if ($announcement === null) {
            View::error(404, ['title' => 'Pengumuman tidak ditemukan']);
            return;
        }

        View::render('admin/announcements/form', ['title' => 'Ubah Pengumuman', 'announcement' => $announcement], 'admin');
    }

    public function update(int $id): void
    {
        if (Announcement::find($id) === null) {
            View::error(404, ['title' => 'Pengumuman tidak ditemukan']);
            return;
        }

        $data = $this->validated('/admin/pengumuman/' . $id . '/edit');
        Announcement::update($id, $data);

        Flash::set('success', 'Pengumuman berhasil disimpan.');
        redirect('/admin/pengumuman');
    }

    public function destroy(int $id): void
    {
        if (Announcement::find($id) === null) {
            View::error(404, ['title' => 'Pengumuman tidak ditemukan']);
            return;
        }

        Announcement::delete($id);
        Flash::set('success', 'Pengumuman sudah dihapus.');
        redirect('/admin/pengumuman');
    }

    private function validated(string $back): array
    {
        $data = [
            'title'     => trim((string) ($_POST['title'] ?? '')),
            'body'      => trim(str_replace("\r\n", "\n", (string) ($_POST['body'] ?? ''))),
            'is_pinned' => isset($_POST['is_pinned']) ? '1' : '0',
        ];

        $errors = Validator::make($data, [
            'title' => 'required|max:150',
            'body'  => 'required|max:10000',
        ], [
            'title' => 'Judul',
            'body'  => 'Isi pengumuman',
        ]);

        if ($errors !== []) {
            Flash::withErrors($errors, $data);
            redirect($back);
        }

        $data['is_pinned'] = $data['is_pinned'] === '1';
        return $data;
    }
}
