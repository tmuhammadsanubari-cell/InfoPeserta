<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;

/**
 * Halaman awal pengunjung.
 * PRD: 2.2.1, 4.3 (GET /)
 *
 *   - index(): render participant/home dengan nama kegiatan, subjudul, tanggal dari settings
 */
final class HomeController
{
    public function index(): void
    {
        $committee = setting('committee_whatsapp');

        View::render('participant/home', [
            'eventName'        => setting('event_name', (string) config('APP_NAME')),
            'eventSubtitle'    => setting('event_subtitle'),
            'eventDates'       => format_date_range_id(setting('event_start_date'), setting('event_end_date')),
            'registrationOpen' => setting('registration_open', '1') === '1',
            'committeeWa'      => $committee !== null ? to_wa_number($committee) : null,
        ]);
    }
}
