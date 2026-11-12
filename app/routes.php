<?php

declare(strict_types=1);

/**
 * Daftar rute aplikasi.
 * PRD: bagian 4.3 (API Endpoints) dan 4.4 (Role & Permission).
 *
 * Format tiap baris: [METHOD, PATH, [ControllerClass, 'method'], 'middleware']
 *
 * Middleware:
 *   'public'      : siapa saja
 *   'guest'       : pengunjung; peserta yang sudah masuk dialihkan ke /info
 *   'participant' : wajib peserta masuk (sesi PHP atau cookie remember)
 *   'admin'       : wajib admin masuk, timeout 2 jam
 *
 * Semua request POST wajib lolos verifikasi CSRF di Router sebelum controller dipanggil.
 * Parameter {id} hanya menerima angka dan diteruskan ke method controller sebagai int.
 */

use App\Controllers\HomeController;
use App\Controllers\ParticipantController;
use App\Controllers\InfoController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\AccountController;
use App\Controllers\Admin\ScheduleDayController;
use App\Controllers\Admin\ScheduleItemController;
use App\Controllers\Admin\AnnouncementController;
use App\Controllers\Admin\InfoSectionController;
use App\Controllers\Admin\DepartmentController;
use App\Controllers\Admin\ParticipantController as AdminParticipantController;
use App\Controllers\Admin\SettingController;

return [
    // ---- Peserta ----
    ['GET',  '/',       [HomeController::class, 'index'],                'guest'],
    ['GET',  '/daftar', [ParticipantController::class, 'showRegister'],  'guest'],
    ['POST', '/daftar', [ParticipantController::class, 'register'],      'guest'],
    ['GET',  '/masuk',  [ParticipantController::class, 'showLogin'],     'guest'],
    ['POST', '/masuk',  [ParticipantController::class, 'login'],         'guest'],
    ['POST', '/keluar', [ParticipantController::class, 'logout'],        'participant'],
    ['GET',  '/info',   [InfoController::class, 'show'],                 'participant'],

    // ---- Admin: autentikasi & akun ----
    ['GET',  '/admin/login',  [AuthController::class, 'showLogin'],      'public'],
    ['POST', '/admin/login',  [AuthController::class, 'login'],          'public'],
    ['POST', '/admin/logout', [AuthController::class, 'logout'],         'admin'],
    ['GET',  '/admin',        [DashboardController::class, 'index'],     'admin'],
    ['GET',  '/admin/akun',   [AccountController::class, 'edit'],        'admin'],
    ['POST', '/admin/akun',   [AccountController::class, 'update'],      'admin'],

    // ---- Admin: jadwal & itinerary ----
    ['GET',  '/admin/jadwal',                     [ScheduleDayController::class, 'index'],    'admin'],
    ['GET',  '/admin/jadwal/hari/tambah',         [ScheduleDayController::class, 'create'],   'admin'],
    ['POST', '/admin/jadwal/hari',                [ScheduleDayController::class, 'store'],    'admin'],
    ['GET',  '/admin/jadwal/hari/{id}/edit',      [ScheduleDayController::class, 'edit'],     'admin'],
    ['POST', '/admin/jadwal/hari/{id}',           [ScheduleDayController::class, 'update'],   'admin'],
    ['POST', '/admin/jadwal/hari/{id}/hapus',     [ScheduleDayController::class, 'destroy'],  'admin'],
    ['GET',  '/admin/jadwal/kegiatan/tambah',     [ScheduleItemController::class, 'create'],  'admin'], // ?day_id=
    ['POST', '/admin/jadwal/kegiatan',            [ScheduleItemController::class, 'store'],   'admin'],
    ['GET',  '/admin/jadwal/kegiatan/{id}/edit',  [ScheduleItemController::class, 'edit'],    'admin'],
    ['POST', '/admin/jadwal/kegiatan/{id}',       [ScheduleItemController::class, 'update'],  'admin'],
    ['POST', '/admin/jadwal/kegiatan/{id}/hapus', [ScheduleItemController::class, 'destroy'], 'admin'],

    // ---- Admin: pengumuman ----
    ['GET',  '/admin/pengumuman',            [AnnouncementController::class, 'index'],   'admin'],
    ['GET',  '/admin/pengumuman/tambah',     [AnnouncementController::class, 'create'],  'admin'],
    ['POST', '/admin/pengumuman',            [AnnouncementController::class, 'store'],   'admin'],
    ['GET',  '/admin/pengumuman/{id}/edit',  [AnnouncementController::class, 'edit'],    'admin'],
    ['POST', '/admin/pengumuman/{id}',       [AnnouncementController::class, 'update'],  'admin'],
    ['POST', '/admin/pengumuman/{id}/hapus', [AnnouncementController::class, 'destroy'], 'admin'],

    // ---- Admin: info penting ----
    ['GET',  '/admin/info',            [InfoSectionController::class, 'index'],   'admin'],
    ['GET',  '/admin/info/tambah',     [InfoSectionController::class, 'create'],  'admin'],
    ['POST', '/admin/info',            [InfoSectionController::class, 'store'],   'admin'],
    ['GET',  '/admin/info/{id}/edit',  [InfoSectionController::class, 'edit'],    'admin'],
    ['POST', '/admin/info/{id}',       [InfoSectionController::class, 'update'],  'admin'],
    ['POST', '/admin/info/{id}/hapus', [InfoSectionController::class, 'destroy'], 'admin'],

    // ---- Admin: departemen ----
    ['GET',  '/admin/departemen',            [DepartmentController::class, 'index'],   'admin'],
    ['POST', '/admin/departemen',            [DepartmentController::class, 'store'],   'admin'],
    ['POST', '/admin/departemen/{id}',       [DepartmentController::class, 'update'],  'admin'],
    ['POST', '/admin/departemen/{id}/hapus', [DepartmentController::class, 'destroy'], 'admin'],

    // ---- Admin: peserta ----
    ['GET',  '/admin/peserta',            [AdminParticipantController::class, 'index'],   'admin'], // ?q=&department_id=&page=
    ['GET',  '/admin/peserta/ekspor',     [AdminParticipantController::class, 'export'],  'admin'], // ?q=&department_id=
    ['POST', '/admin/peserta/{id}/hapus', [AdminParticipantController::class, 'destroy'], 'admin'],

    // ---- Admin: pengaturan ----
    ['GET',  '/admin/pengaturan', [SettingController::class, 'edit'],   'admin'],
    ['POST', '/admin/pengaturan', [SettingController::class, 'update'], 'admin'],
];
