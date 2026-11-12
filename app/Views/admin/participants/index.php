<?php
/**
 * View: admin/participants/index.php — filter, tabel, paginasi, tombol Unduh Excel (CSV)
 * PRD: 2.2.7
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 * Variabel: $q, $departmentId, $departments, $result (rows,total,page,pages,per_page), $totalAll
 */
$filters = array_filter(['q' => $q, 'department_id' => $departmentId]);
$pageUrl = static function (int $page) use ($filters): string {
    $query = http_build_query($filters + ($page > 1 ? ['page' => $page] : []));
    return url('/admin/peserta' . ($query !== '' ? '?' . $query : ''));
};
$exportQuery = http_build_query($filters);
$from = $result['total'] > 0 ? ($result['page'] - 1) * $result['per_page'] + 1 : 0;
$to = $from > 0 ? $from + count($result['rows']) - 1 : 0;
$isFiltered = $filters !== [];
?>
<div class="admin-page-head">
    <div>
        <h1 class="admin-title">Data Peserta <span class="chip chip-primary"><?= e($totalAll) ?> terdaftar</span></h1>
        <p class="muted">Cari, filter per departemen, dan unduh data untuk absensi, kamar, atau bus.</p>
    </div>
    <div class="admin-actions">
        <a class="btn btn-primary" href="<?= e(url('/admin/peserta/ekspor' . ($exportQuery !== '' ? '?' . $exportQuery : ''))) ?>">
            <?= icon('arrow-down', 'icon icon-sm') ?> Unduh Excel (CSV)
        </a>
    </div>
</div>

<form class="card filter-bar" method="get" action="<?= e(url('/admin/peserta')) ?>" role="search">
    <label class="sr-only" for="q">Cari nama atau nomor HP</label>
    <input class="input filter-search" type="search" id="q" name="q" value="<?= e($q ?? '') ?>" placeholder="Cari nama atau nomor HP…" maxlength="100">
    <label class="sr-only" for="department_id">Departemen</label>
    <select class="input select filter-select" id="department_id" name="department_id">
        <option value="">Semua departemen</option>
        <?php foreach ($departments as $d): ?>
            <option value="<?= e($d['id']) ?>"<?= (int) $d['id'] === $departmentId ? ' selected' : '' ?>>
                <?= e($d['name']) ?><?= (int) $d['is_active'] !== 1 ? ' (nonaktif)' : '' ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="btn btn-secondary" type="submit">Terapkan</button>
    <?php if ($isFiltered): ?><a class="link-strong filter-reset" href="<?= e(url('/admin/peserta')) ?>">Reset</a><?php endif; ?>
</form>

<?php if ($result['rows'] === []): ?>
    <div class="card empty"><?= $isFiltered ? 'Tidak ada peserta yang cocok dengan pencarian ini.' : 'Belum ada peserta yang mendaftar.' ?></div>
<?php else: ?>
    <div class="card table-card">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">No</th>
                    <th scope="col">Nama lengkap</th>
                    <th scope="col">Departemen</th>
                    <th scope="col">Nomor HP</th>
                    <th scope="col">Waktu daftar</th>
                    <th scope="col" class="col-actions"><span class="sr-only">Aksi</span></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($result['rows'] as $i => $p): ?>
                <tr>
                    <td data-label="No" class="cell-no muted"><?= $from + $i ?></td>
                    <td data-label="Nama lengkap" class="cell-strong"><?= e($p['full_name']) ?></td>
                    <td data-label="Departemen"><?= e($p['department_name']) ?></td>
                    <td data-label="Nomor HP">
                        <a class="wa-link" href="https://wa.me/<?= e(to_wa_number($p['phone'])) ?>" target="_blank" rel="noopener" title="Buka WhatsApp">
                            <?= icon('chat', 'icon icon-sm') ?><?= e(format_phone($p['phone'])) ?>
                        </a>
                    </td>
                    <td data-label="Waktu daftar" class="cell-date">
                        <?= e(format_date_short_id($p['created_at'])) ?> <?= e(date('Y', strtotime($p['created_at']))) ?>
                        <small class="muted"><?= e(date('H.i', strtotime($p['created_at']))) ?></small>
                    </td>
                    <td class="col-actions">
                        <form method="post" action="<?= e(url('/admin/peserta/' . $p['id'] . '/hapus')) ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="q" value="<?= e($q ?? '') ?>">
                            <input type="hidden" name="department_id" value="<?= e($departmentId ?? '') ?>">
                            <input type="hidden" name="page" value="<?= e($result['page']) ?>">
                            <button class="icon-btn icon-btn-danger" type="submit" aria-label="Hapus <?= e($p['full_name']) ?>" title="Hapus"
                                    data-confirm="Hapus peserta &quot;<?= e($p['full_name']) ?>&quot;? Peserta ini akan keluar dari semua perangkat. Tindakan ini tidak bisa dibatalkan.">
                                <?= icon('trash', 'icon icon-sm') ?>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div class="table-foot">
            <span class="muted">Menampilkan <strong><?= $from ?>–<?= $to ?></strong> dari <strong><?= e($result['total']) ?></strong> peserta</span>
            <?php if ($result['pages'] > 1): ?>
                <nav class="pagination" aria-label="Halaman">
                    <?php if ($result['page'] > 1): ?>
                        <a class="page-link" href="<?= e($pageUrl($result['page'] - 1)) ?>" aria-label="Halaman sebelumnya"><?= icon('arrow-left', 'icon icon-sm') ?></a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $result['page'] - 2);
                    $end = min($result['pages'], $start + 4);
                    $start = max(1, $end - 4);
                    for ($n = $start; $n <= $end; $n++): ?>
                        <a class="page-link<?= $n === $result['page'] ? ' is-active' : '' ?>" href="<?= e($pageUrl($n)) ?>"<?= $n === $result['page'] ? ' aria-current="page"' : '' ?>><?= $n ?></a>
                    <?php endfor; ?>
                    <?php if ($result['page'] < $result['pages']): ?>
                        <a class="page-link" href="<?= e($pageUrl($result['page'] + 1)) ?>" aria-label="Halaman berikutnya"><?= icon('arrow-right', 'icon icon-sm') ?></a>
                    <?php endif; ?>
                </nav>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
