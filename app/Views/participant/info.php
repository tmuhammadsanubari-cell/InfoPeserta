<?php
/**
 * View: Halaman informasi
 * PRD: 2.2.3, 2.2.10
 * Semua variabel dicetak lewat e(); teks panjang dari admin lewat format_text().
 *
 *   - Header sapaan + tombol Keluar
 *   - Tab hari (badge Hari ini), kartu itinerary, timeline kegiatan, link Buka di Maps
 *   - Panel Pengumuman dan Info (accordion)
 *   - Bottom navigation 3 tab + tombol WhatsApp
 *   - Tanpa JS semua panel tetap tampil berurutan
 *
 * Variabel: $firstName, $eventName, $eventSubtitle, $days, $todayDayId, $activeDayId,
 *           $announcements, $hasRecent, $sections, $waUrl
 */
$now = time();
$isRecent = static function (string $publishedAt) use ($now): bool {
    $ts = strtotime($publishedAt);
    return $ts !== false && $ts <= $now && $ts >= $now - 86400;
};
?>
<header class="info-header">
    <div class="info-header-inner">
        <div class="info-header-text">
            <p class="hello">Halo, <?= e($firstName) ?></p>
            <p class="event-line"><?= e(implode(' • ', array_filter([$eventName, $eventSubtitle]))) ?></p>
        </div>
        <form method="post" action="<?= e(url('/keluar')) ?>">
            <?= csrf_field() ?>
            <button class="btn-logout" type="submit"><?= icon('log-out', 'icon icon-sm') ?> Keluar</button>
        </form>
    </div>
</header>

<main class="info-main">
    <!-- ================= Jadwal ================= -->
    <section class="panel is-active" id="jadwal" data-panel="jadwal" aria-labelledby="jadwal-heading">
        <h2 class="panel-heading" id="jadwal-heading"><?= icon('calendar') ?> Jadwal</h2>

        <?php if ($days === []): ?>
            <div class="card empty">Jadwal akan segera diumumkan.</div>
        <?php else: ?>
            <nav class="day-tabs" aria-label="Pilih hari">
                <?php foreach ($days as $day): $isToday = (int) $day['id'] === $todayDayId; $isActive = (int) $day['id'] === $activeDayId; ?>
                    <a class="day-tab<?= $isActive ? ' is-active' : '' ?>" href="#hari-<?= e($day['day_number']) ?>"
                       data-day="<?= e($day['id']) ?>"<?= $isActive ? ' aria-current="true"' : '' ?>>
                        <?php if ($isToday): ?><span class="today-badge">Hari ini</span><?php endif; ?>
                        <span class="day-tab-label">Hari <?= e($day['day_number']) ?> · <?= e(format_date_short_id($day['date'])) ?></span>
                        <span class="day-tab-title"><?= e($day['title']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>

            <?php foreach ($days as $day): $isToday = (int) $day['id'] === $todayDayId; ?>
                <article class="day<?= (int) $day['id'] === $activeDayId ? ' is-active' : '' ?>" id="hari-<?= e($day['day_number']) ?>" data-day-panel="<?= e($day['id']) ?>">
                    <div class="card itinerary">
                        <div class="itinerary-top">
                            <p class="eyebrow">Hari <?= e($day['day_number']) ?><?= $isToday ? ' · Hari ini' : '' ?></p>
                            <span class="chip"><?= count($day['items']) ?> agenda</span>
                        </div>
                        <h3 class="itinerary-title"><?= e($day['title']) ?></h3>
                        <p class="muted"><?= e(format_date_id($day['date'])) ?></p>
                        <?php if (!empty($day['route'])): ?>
                            <p class="route"><?= icon('navigation', 'icon icon-sm') ?><span><?= e($day['route']) ?></span></p>
                        <?php endif; ?>
                    </div>

                    <h4 class="section-label">Rincian Kegiatan</h4>

                    <?php if ($day['items'] === []): ?>
                        <div class="card empty">Kegiatan hari ini akan segera diumumkan.</div>
                    <?php else: ?>
                        <ol class="timeline">
                            <?php foreach ($day['items'] as $item): ?>
                                <li class="timeline-item">
                                    <div class="card activity">
                                        <p class="activity-time">
                                            <?= icon('clock', 'icon icon-sm') ?>
                                            <?= e(format_time_id($item['start_time'])) ?><?= $item['end_time'] ? ' – ' . e(format_time_id($item['end_time'])) : '' ?>
                                        </p>
                                        <h5 class="activity-title"><?= e($item['activity']) ?></h5>
                                        <?php if (!empty($item['location'])): ?>
                                            <p class="activity-location"><?= icon('map-pin', 'icon icon-sm') ?><span><?= e($item['location']) ?></span></p>
                                        <?php endif; ?>
                                        <?php if (!empty($item['notes'])): ?>
                                            <p class="activity-notes"><?= format_text($item['notes']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($item['location'])): ?>
                                            <a class="maps-link" href="https://www.google.com/maps/search/?api=1&amp;query=<?= e(rawurlencode($item['location'])) ?>" target="_blank" rel="noopener">
                                                <?= icon('map-pin', 'icon icon-sm') ?> Buka di Maps
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <!-- ================= Pengumuman ================= -->
    <section class="panel" id="pengumuman" data-panel="pengumuman" aria-labelledby="pengumuman-heading">
        <div class="panel-head">
            <h2 class="panel-heading" id="pengumuman-heading"><?= icon('megaphone') ?> Pengumuman</h2>
            <?php if ($announcements !== []): ?><span class="chip"><?= count($announcements) ?> pesan</span><?php endif; ?>
        </div>

        <?php if ($announcements === []): ?>
            <div class="card empty">Belum ada pengumuman.</div>
        <?php else: ?>
            <div class="stack-sm">
                <?php foreach ($announcements as $a): $pinned = (int) $a['is_pinned'] === 1; ?>
                    <article class="card announcement<?= $pinned ? ' is-pinned' : '' ?>">
                        <div class="announcement-meta">
                            <?php if ($pinned): ?>
                                <span class="badge badge-primary"><?= icon('pin', 'icon icon-xs') ?> Disematkan</span>
                            <?php elseif ($isRecent($a['published_at'])): ?>
                                <span class="badge badge-new">Baru</span>
                            <?php else: ?>
                                <span></span>
                            <?php endif; ?>
                            <time class="announcement-time" datetime="<?= e(date('c', strtotime($a['published_at']))) ?>">
                                <?= icon('clock', 'icon icon-xs') ?> <?= e(format_datetime_id($a['published_at'])) ?>
                            </time>
                        </div>
                        <h3 class="announcement-title"><?= e($a['title']) ?></h3>
                        <p class="announcement-body"><?= format_text($a['body']) ?></p>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- ================= Info penting ================= -->
    <section class="panel" id="info" data-panel="info" aria-labelledby="info-heading">
        <h2 class="panel-heading" id="info-heading"><?= icon('info') ?> Informasi Penting</h2>
        <p class="muted panel-sub">Titik kumpul, kontak panitia, dan panduan selama acara.</p>

        <?php if ($sections === []): ?>
            <div class="card empty">Informasi penting akan segera diumumkan.</div>
        <?php else: ?>
            <div class="stack-sm">
                <?php foreach ($sections as $i => $s): ?>
                    <details class="card accordion"<?= $i === 0 ? ' open' : '' ?>>
                        <summary class="accordion-summary">
                            <span><?= e($s['title']) ?></span>
                            <?= icon('chevron-down', 'icon accordion-chevron') ?>
                        </summary>
                        <div class="accordion-body"><?= format_text($s['body']) ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</main>

<?php if ($waUrl !== null): ?>
    <a class="wa-fab" href="<?= e($waUrl) ?>" target="_blank" rel="noopener" aria-label="Hubungi panitia lewat WhatsApp" title="Hubungi panitia">
        <?= icon('chat') ?>
    </a>
<?php endif; ?>

<nav class="bottom-nav" aria-label="Navigasi utama">
    <a class="bottom-nav-item is-active" href="#jadwal" data-tab="jadwal" aria-current="page">
        <?= icon('calendar') ?><span>Jadwal</span>
    </a>
    <a class="bottom-nav-item" href="#pengumuman" data-tab="pengumuman">
        <span class="bottom-nav-icon"><?= icon('megaphone') ?><?php if ($hasRecent): ?><span class="dot" aria-hidden="true"></span><?php endif; ?></span>
        <span>Pengumuman</span>
        <?php if ($hasRecent): ?><span class="sr-only">(ada pengumuman baru)</span><?php endif; ?>
    </a>
    <a class="bottom-nav-item" href="#info" data-tab="info">
        <?= icon('info') ?><span>Info</span>
    </a>
</nav>
