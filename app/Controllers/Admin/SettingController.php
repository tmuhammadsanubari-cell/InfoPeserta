<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Flash;
use App\Core\Validator;
use App\Core\View;
use App\Models\Setting;

/**
 * Pengaturan acara.
 * PRD: 2.2.9
 *
 *   - edit(), update(): event_name, event_subtitle, event_start_date, event_end_date, committee_whatsapp, primary_color, registration_open
 */
final class SettingController
{
    /** Pilihan warna cepat; semua cukup gelap untuk teks putih. */
    public const PRESET_COLORS = [
        '#0F766E' => 'Teal',
        '#1D4ED8' => 'Biru',
        '#15803D' => 'Hijau',
        '#7C3AED' => 'Ungu',
        '#BE123C' => 'Merah',
        '#C2410C' => 'Oranye',
        '#334155' => 'Abu tua',
    ];

    public function edit(): void
    {
        View::render('admin/settings/edit', [
            'title'    => 'Pengaturan Acara',
            'settings' => Setting::all(),
        ], 'admin');
    }

    public function update(): void
    {
        $input = [
            'event_name'         => trim((string) ($_POST['event_name'] ?? '')),
            'event_subtitle'     => trim((string) ($_POST['event_subtitle'] ?? '')),
            'event_start_date'   => trim((string) ($_POST['event_start_date'] ?? '')),
            'event_end_date'     => trim((string) ($_POST['event_end_date'] ?? '')),
            'committee_whatsapp' => trim((string) ($_POST['committee_whatsapp'] ?? '')),
            'primary_color'      => strtoupper(trim((string) ($_POST['primary_color'] ?? ''))),
            'registration_open'  => isset($_POST['registration_open']) ? '1' : '0',
        ];

        $errors = Validator::make($input, [
            'event_name'       => 'required|max:100',
            'event_subtitle'   => 'max:150',
            'event_start_date' => 'required|date',
            'event_end_date'   => 'required|date',
            'primary_color'    => 'required|color',
        ], [
            'event_name'       => 'Nama kegiatan',
            'event_subtitle'   => 'Subjudul',
            'event_start_date' => 'Tanggal mulai',
            'event_end_date'   => 'Tanggal selesai',
            'primary_color'    => 'Warna utama',
        ]);

        if (!isset($errors['event_start_date']) && !isset($errors['event_end_date'])
            && $input['event_end_date'] < $input['event_start_date']) {
            $errors['event_end_date'] = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
        }

        if ($input['committee_whatsapp'] !== '') {
            $phone = normalize_phone($input['committee_whatsapp']);
            if ($phone === null) {
                $errors['committee_whatsapp'] = 'Nomor WhatsApp tidak valid. Contoh: 081234567890';
            } else {
                $input['committee_whatsapp'] = $phone;
            }
        }

        // Header dan tombol memakai teks putih: tolak warna yang terlalu terang (kontras < 3:1).
        if (!isset($errors['primary_color']) && self::contrastWithWhite($input['primary_color']) < 3.0) {
            $errors['primary_color'] = 'Warna terlalu terang, teks putih akan sulit dibaca. Pilih warna yang lebih gelap.';
        }

        if ($errors !== []) {
            Flash::withErrors($errors, $input);
            redirect('/admin/pengaturan');
        }

        Setting::setMany($input);
        Flash::set('success', 'Pengaturan acara berhasil disimpan.');
        redirect('/admin/pengaturan');
    }

    /** Rasio kontras WCAG antara warna #RRGGBB dan putih. */
    private static function contrastWithWhite(string $hex): float
    {
        $channels = array_map(static function (string $pair): float {
            $c = hexdec($pair) / 255;
            return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
        }, str_split(substr($hex, 1), 2));

        $luminance = 0.2126 * $channels[0] + 0.7152 * $channels[1] + 0.0722 * $channels[2];
        return 1.05 / ($luminance + 0.05);
    }
}
