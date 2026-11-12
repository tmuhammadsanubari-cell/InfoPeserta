<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\Department;
use PDOException;

/**
 * Kelola departemen.
 * PRD: 2.2.8
 *
 *   - index, store, update (nama, urutan, aktif), destroy
 *   - Tolak hapus jika dipakai: 'Departemen ini sudah dipakai {n} peserta. Nonaktifkan saja.'
 *
 * Form ubah ada di setiap baris; key error/old diberi akhiran _{id} agar tidak tertukar antar baris.
 */
final class DepartmentController
{
    public function index(): void
    {
        $departments = Department::all();
        $next = $departments !== [] ? max(array_map(static fn ($d) => (int) $d['sort_order'], $departments)) + 1 : 1;

        View::render('admin/departments/index', [
            'title'       => 'Departemen',
            'departments' => $departments,
            'nextOrder'   => $next,
        ], 'admin');
    }

    public function store(): void
    {
        $data = $this->validated($_POST, null, '');
        if ($data === null) {
            redirect('/admin/departemen');
        }

        $this->guardDuplicate(static fn () => Department::create($data), $data['name'], '', $data);

        Flash::set('success', 'Departemen "' . $data['name'] . '" berhasil ditambahkan.');
        redirect('/admin/departemen');
    }

    public function update(int $id): void
    {
        if (Department::find($id) === null) {
            View::error(404, ['title' => 'Departemen tidak ditemukan']);
            return;
        }

        $suffix = '_' . $id;
        $data = $this->validated($_POST, $id, $suffix);
        if ($data === null) {
            redirect('/admin/departemen#dept-' . $id);
        }

        $this->guardDuplicate(static fn () => Department::update($id, $data), $data['name'], $suffix, $data);

        Flash::set('success', 'Departemen "' . $data['name'] . '" berhasil disimpan.');
        redirect('/admin/departemen#dept-' . $id);
    }

    public function destroy(int $id): void
    {
        $department = Department::find($id);
        if ($department === null) {
            View::error(404, ['title' => 'Departemen tidak ditemukan']);
            return;
        }

        $used = Department::usageCount($id);
        if ($used > 0) {
            Flash::set('error', 'Departemen ini sudah dipakai ' . $used . ' peserta. Nonaktifkan saja.');
            redirect('/admin/departemen#dept-' . $id);
        }

        try {
            Department::delete($id);
        } catch (PDOException $e) {
            // Pengaman kedua: peserta mendaftar di antara pengecekan dan penghapusan (ON DELETE RESTRICT).
            if (($e->errorInfo[1] ?? null) === 1451) {
                Flash::set('error', 'Departemen ini sudah dipakai peserta. Nonaktifkan saja.');
                redirect('/admin/departemen#dept-' . $id);
            }
            throw $e;
        }

        Flash::set('success', 'Departemen "' . $department['name'] . '" sudah dihapus.');
        redirect('/admin/departemen');
    }

    /** @return array{name: string, sort_order: int, is_active: bool}|null null jika gagal (error sudah di-flash) */
    private function validated(array $post, ?int $id, string $suffix): ?array
    {
        $input = [
            'name'       => preg_replace('/\s+/u', ' ', trim((string) ($post['name'] ?? ''))) ?? '',
            'sort_order' => trim((string) ($post['sort_order'] ?? '')),
            'is_active'  => isset($post['is_active']) ? '1' : '0',
        ];

        $errors = Validator::make($input, [
            'name'       => 'required|max:100',
            'sort_order' => 'range:0,9999',
        ], [
            'name'       => 'Nama departemen',
            'sort_order' => 'Urutan',
        ]);

        if (!isset($errors['name'])) {
            $same = Department::findByName($input['name']);
            if ($same !== null && (int) $same['id'] !== $id) {
                $errors['name'] = 'Nama departemen sudah ada.';
            }
        }

        if ($errors !== []) {
            Flash::withErrors(self::suffixKeys($errors, $suffix), self::suffixKeys($input, $suffix) + ['_form' => $suffix]);
            return null;
        }

        return [
            'name'       => $input['name'],
            'sort_order' => (int) $input['sort_order'],
            'is_active'  => $input['is_active'] === '1',
        ];
    }

    private function guardDuplicate(callable $save, string $name, string $suffix, array $data): void
    {
        try {
            $save();
        } catch (PDOException $e) {
            if (($e->errorInfo[1] ?? null) === 1062) {
                Flash::withErrors(['name' . $suffix => 'Nama departemen sudah ada.'], self::suffixKeys([
                    'name' => $name, 'sort_order' => (string) $data['sort_order'], 'is_active' => $data['is_active'] ? '1' : '0',
                ], $suffix) + ['_form' => $suffix]);
                redirect('/admin/departemen');
            }
            throw $e;
        }
    }

    private static function suffixKeys(array $values, string $suffix): array
    {
        $out = [];
        foreach ($values as $key => $value) {
            $out[$key . $suffix] = $value;
        }
        return $out;
    }
}
