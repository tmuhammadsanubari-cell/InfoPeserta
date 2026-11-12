# Daftar Tugas — Portal Info Peserta

Urutan kerja MVP (target 5 hari, lihat PRD bagian 4.9). Centang `[x]` setelah tugas selesai dan lolos pengecekan di `CLAUDE.md`.

## Hari 1 — Fondasi

- [x] `app/bootstrap.php`: autoloader `App\` → `app/`, load `.env`, set timezone, error handler (dev tampil, production ke log), mulai sesi dengan cookie aman
- [x] `app/config.php`: parser `.env` sederhana (abaikan komentar dan baris kosong, dukung nilai berkutip), kembalikan array konfigurasi dengan default
- [x] `app/Core/Database.php`: singleton PDO, `ERRMODE_EXCEPTION`, `FETCH_ASSOC`, charset `utf8mb4`
- [x] `app/Core/Router.php`: cocokkan method + path dari `app/routes.php`, dukung parameter `{id}` (angka), jalankan middleware, 404 jika tidak cocok
- [x] `app/Core/Csrf.php`: buat token per sesi, `field()` untuk hidden input, `verify()` untuk semua POST
- [x] `app/Core/Flash.php`: pesan sekali tampil, simpan error per field dan old input
- [x] `app/Core/View.php`: `render(template, data, layout)` dengan output buffering
- [x] `app/Core/Validator.php`: aturan `required`, `min`, `max`, `in`, `date`, `time`
- [x] `app/helpers.php`: `e()`, `url()`, `redirect()`, `normalize_phone()`, `to_wa_number()`, `format_text()`, `setting()`, `old()`, `error()`
- [x] `app/Models/Setting.php` dan halaman awal `/` (HomeController + `participant/home.php` + layout peserta)
- [x] Cek: `php -S` jalan, halaman awal tampil dengan nama kegiatan dari database seed

## Hari 2 — Alur peserta

- [x] Model `Department`, `Participant`, `ParticipantSession`
- [x] `app/Auth/ParticipantAuth.php`: login, cookie remember 30 hari, pulihkan sesi dari cookie, logout, `requireParticipant()`
- [x] Registrasi `/daftar` (GET/POST) sesuai PRD 2.2.1, termasuk kondisi registrasi ditutup
- [x] Masuk `/masuk` (GET/POST) dan keluar `/keluar` sesuai PRD 2.2.2
- [x] Model `ScheduleDay`, `ScheduleItem`, `Announcement`, `InfoSection`
- [x] Halaman `/info` sesuai PRD 2.2.3: tab hari (otomatis hari ini), itinerary, timeline kegiatan, link Maps, tab pengumuman (pinned di atas, titik baru 24 jam), tab info (accordion), bottom navigation
- [x] Tombol WhatsApp panitia (PRD 2.2.10)
- [x] `app.css` mobile-first lengkap untuk halaman peserta, `app.js` untuk tab dan accordion (tetap terbaca tanpa JS)
- [x] Cek: semua skenario peserta di `CLAUDE.md` bagian verifikasi

## Hari 3 — Admin inti

- [ ] Model `Admin`, `LoginAttempt`; `app/Auth/AdminAuth.php` dengan rate limit dan timeout 2 jam
- [ ] Login/logout admin dan layout admin (sidebar desktop, menu hamburger di HP)
- [ ] Kelola hari dan kegiatan (PRD 2.2.5)
- [ ] Kelola pengumuman (PRD 2.2.6)
- [ ] Kelola info penting (PRD 2.2.6)
- [ ] Cek: ubah jadwal di admin, langsung terlihat di `/info`

## Hari 4 — Admin lengkap

- [ ] Data peserta: tabel, cari, filter departemen, paginasi 50, hapus (PRD 2.2.7)
- [ ] Ekspor CSV sesuai filter (BOM, delimiter, `="08..."`)
- [ ] Kelola departemen dengan larangan hapus jika dipakai (PRD 2.2.8)
- [ ] Pengaturan acara, termasuk warna utama via `--color-primary` (PRD 2.2.9)
- [ ] Ganti password admin (PRD 2.2.4)
- [ ] Dashboard ringkasan (PRD 2.2.11)
- [ ] Halaman error 404, 419, 429, 500

## Hari 5 — Rapikan dan siapkan demo

- [ ] Uji tampilan di 360 px, 768 px, desktop
- [ ] Uji semua acceptance criteria PRD bagian 2.2 satu per satu
- [ ] Pastikan `.env`, `app/`, `database/`, `storage/` tidak bisa diakses dari browser
- [ ] Lengkapi `README.md` (instalasi cPanel, ganti password admin)
- [ ] Buat zip siap unggah (tanpa `.env`, tanpa log)

## Ide di luar scope (catat saja, jangan dikerjakan)

-
