<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Auth\ParticipantAuth;
use App\Core\View;
use App\Models\Announcement;
use App\Models\InfoSection;
use App\Models\ScheduleDay;

/**
 * Halaman informasi peserta.
 * PRD: 2.2.3, 2.2.10
 *
 *   - show(): ambil hari + kegiatan, pengumuman (pinned dulu, lalu terbaru), info_sections, settings
 *   - Tentukan tab aktif: hari yang date = hari ini, selain itu Hari 1
 *   - Hitung apakah ada pengumuman < 24 jam (titik merah)
 *   - Render participant/info
 */
final class InfoController
{
    public function show(): void
    {
        $user = ParticipantAuth::requireParticipant();

        $days = ScheduleDay::allWithItems();
        $today = date('Y-m-d');

        $todayDayId = null;
        foreach ($days as $day) {
            if ($day['date'] === $today) {
                $todayDayId = (int) $day['id'];
                break;
            }
        }
        $activeDayId = $todayDayId ?? ($days[0]['id'] ?? null);

        // Tombol WhatsApp panitia hanya jika nomor diisi (PRD 2.2.10).
        $waUrl = null;
        $committee = setting('committee_whatsapp');
        if ($committee !== null && preg_replace('/\D+/', '', $committee) !== '') {
            $text = 'Halo panitia, saya ' . $user['full_name'] . ' dari ' . $user['department_name'] . '.';
            $waUrl = 'https://wa.me/' . to_wa_number($committee) . '?text=' . rawurlencode($text);
        }

        View::render('participant/info', [
            'title'          => 'Informasi',
            'bodyClass'      => 'page page-info',
            'firstName'      => explode(' ', trim((string) $user['full_name']))[0],
            'eventName'      => setting('event_name', (string) config('APP_NAME')),
            'eventSubtitle'  => setting('event_subtitle'),
            'days'           => $days,
            'todayDayId'     => $todayDayId,
            'activeDayId'    => $activeDayId !== null ? (int) $activeDayId : null,
            'announcements'  => Announcement::listed(),
            'hasRecent'      => Announcement::hasRecent(24),
            'sections'       => InfoSection::ordered(),
            'waUrl'          => $waUrl,
        ]);
    }
}
