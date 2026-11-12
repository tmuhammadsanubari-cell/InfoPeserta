<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\InfoSection;

/**
 * CRUD info penting.
 * PRD: 2.2.6
 *
 *   - index, create, store, edit, update, destroy; field title, body, sort_order
 */
final class InfoSectionController
{
    public function index(): void
    {
        View::render('admin/info/index', [
            'title'    => 'Info Penting',
            'sections' => InfoSection::ordered(),
        ], 'admin');
    }

    public function create(): void
    {
        $sections = InfoSection::ordered();
        $next = $sections !== [] ? (int) end($sections)['sort_order'] + 1 : 1;

        View::render('admin/info/form', [
            'title'   => 'Tambah Info Penting',
            'section' => ['id' => null, 'title' => '', 'body' => '', 'sort_order' => $next],
        ], 'admin');
    }

    public function store(): void
    {
        $data = $this->validated('/admin/info/tambah');
        InfoSection::create($data);

        Flash::set('success', 'Bagian "' . $data['title'] . '" berhasil ditambahkan.');
        redirect('/admin/info');
    }

    public function edit(int $id): void
    {
        $section = InfoSection::find($id);
        if ($section === null) {
            View::error(404, ['title' => 'Bagian tidak ditemukan']);
            return;
        }

        View::render('admin/info/form', ['title' => 'Ubah Info Penting', 'section' => $section], 'admin');
    }

    public function update(int $id): void
    {
        if (InfoSection::find($id) === null) {
            View::error(404, ['title' => 'Bagian tidak ditemukan']);
            return;
        }

        $data = $this->validated('/admin/info/' . $id . '/edit');
        InfoSection::update($id, $data);

        Flash::set('success', 'Bagian "' . $data['title'] . '" berhasil disimpan.');
        redirect('/admin/info');
    }

    public function destroy(int $id): void
    {
        $section = InfoSection::find($id);
        if ($section === null) {
            View::error(404, ['title' => 'Bagian tidak ditemukan']);
            return;
        }

        InfoSection::delete($id);
        Flash::set('success', 'Bagian "' . $section['title'] . '" sudah dihapus.');
        redirect('/admin/info');
    }

    private function validated(string $back): array
    {
        $data = [
            'title'      => trim((string) ($_POST['title'] ?? '')),
            'body'       => trim(str_replace("\r\n", "\n", (string) ($_POST['body'] ?? ''))),
            'sort_order' => trim((string) ($_POST['sort_order'] ?? '')),
        ];

        $errors = Validator::make($data, [
            'title'      => 'required|max:100',
            'body'       => 'required|max:10000',
            'sort_order' => 'range:0,9999',
        ], [
            'title'      => 'Judul',
            'body'       => 'Isi',
            'sort_order' => 'Urutan',
        ]);

        if ($errors !== []) {
            Flash::withErrors($errors, $data);
            redirect($back);
        }

        $data['sort_order'] = (int) $data['sort_order'];
        return $data;
    }
}
