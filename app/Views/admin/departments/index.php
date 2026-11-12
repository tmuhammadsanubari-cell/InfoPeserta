<?php
/**
 * View: admin/departments/index.php — daftar + form inline tambah/ubah
 * PRD: 2.2.8
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $departments (dengan participant_count), $nextOrder
 * Error/old untuk form baris memakai akhiran _{id} (lihat DepartmentController).
 */
use App\Core\Flash;

$old = Flash::old();
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Departemen</h1>
        <p class="muted">Daftar ini tampil di pilihan departemen form registrasi. Departemen nonaktif tidak bisa dipilih pendaftar baru, tapi tetap tampil di data peserta lama.</p>
    </div>
</div>

<form class="card form dept-add" method="post" action="<?= e(url('/admin/departemen')) ?>" novalidate>
    <?= csrf_field() ?>
    <h2 class="card-title">Tambah departemen</h2>
    <div class="dept-fields">
        <div class="form-group dept-name">
            <label class="label" for="name">Nama</label>
            <input class="input<?= invalid('name') ?>" type="text" id="name" name="name" maxlength="100" value="<?= e(old('name')) ?>" placeholder="Contoh: Gudang" required>
            <?= field_error('name') ?>
        </div>
        <div class="form-group dept-order">
            <label class="label" for="sort_order">Urutan</label>
            <input class="input<?= invalid('sort_order') ?>" type="number" id="sort_order" name="sort_order" min="0" max="9999" inputmode="numeric" value="<?= e(old('sort_order', (string) $nextOrder)) ?>">
            <?= field_error('sort_order') ?>
        </div>
        <label class="checkbox dept-active">
            <input type="checkbox" name="is_active" value="1"<?= !isset($old['name']) || old('is_active') === '1' ? ' checked' : '' ?>>
            <span>Aktif</span>
        </label>
        <button class="btn btn-primary dept-submit" type="submit"><?= icon('plus', 'icon icon-sm') ?> Tambah</button>
    </div>
</form>

<?php if ($departments === []): ?>
    <div class="card empty">Belum ada departemen. Tambahkan minimal satu agar peserta bisa mendaftar.</div>
<?php else: ?>
    <div class="stack-sm">
        <?php foreach ($departments as $d):
            $id = (int) $d['id'];
            $sfx = '_' . $id;
            $hasOld = isset($old['name' . $sfx]);
            $active = $hasOld ? old('is_active' . $sfx) === '1' : (int) $d['is_active'] === 1;
            $used = (int) $d['participant_count']; ?>
            <div class="card dept-row<?= $active ? '' : ' is-inactive' ?>" id="dept-<?= $id ?>">
                <form class="dept-fields" method="post" action="<?= e(url('/admin/departemen/' . $id)) ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="form-group dept-name">
                        <label class="label" for="name<?= $sfx ?>">Nama <span class="opt"><?= $used ?> peserta</span></label>
                        <input class="input<?= invalid('name' . $sfx) ?>" type="text" id="name<?= $sfx ?>" name="name" maxlength="100"
                               value="<?= e(old('name' . $sfx, (string) $d['name'])) ?>" required>
                        <?= field_error('name' . $sfx) ?>
                    </div>
                    <div class="form-group dept-order">
                        <label class="label" for="sort_order<?= $sfx ?>">Urutan</label>
                        <input class="input<?= invalid('sort_order' . $sfx) ?>" type="number" id="sort_order<?= $sfx ?>" name="sort_order" min="0" max="9999"
                               inputmode="numeric" value="<?= e(old('sort_order' . $sfx, (string) $d['sort_order'])) ?>">
                        <?= field_error('sort_order' . $sfx) ?>
                    </div>
                    <label class="checkbox dept-active">
                        <input type="checkbox" name="is_active" value="1"<?= $active ? ' checked' : '' ?>>
                        <span>Aktif</span>
                    </label>
                    <button class="btn btn-secondary dept-submit" type="submit">Simpan</button>
                </form>
                <form class="dept-delete" method="post" action="<?= e(url('/admin/departemen/' . $id . '/hapus')) ?>">
                    <?= csrf_field() ?>
                    <?php if ($used > 0): ?>
                        <button class="icon-btn" type="button" disabled title="Sudah dipakai <?= $used ?> peserta. Nonaktifkan saja." aria-label="Tidak bisa dihapus: sudah dipakai <?= $used ?> peserta"><?= icon('trash', 'icon icon-sm') ?></button>
                    <?php else: ?>
                        <button class="icon-btn icon-btn-danger" type="submit" title="Hapus" aria-label="Hapus <?= e($d['name']) ?>"
                                data-confirm="Hapus departemen &quot;<?= e($d['name']) ?>&quot;?"><?= icon('trash', 'icon icon-sm') ?></button>
                    <?php endif; ?>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
