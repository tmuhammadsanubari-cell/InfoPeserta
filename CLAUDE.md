# CLAUDE.md — Portal Info Peserta

Panduan kerja untuk Claude Code di repo ini. Baca file ini dulu, lalu `docs/PRD.md` dan `docs/TASKS.md`, sebelum menulis kode apa pun.

## Konteks proyek

Website ringan dan mobile-first untuk peserta satu kegiatan/perjalanan. Peserta mendaftar (nama lengkap, departemen, nomor HP/WhatsApp), lalu membuka halaman informasi berisi jadwal per hari, itinerary, pengumuman, dan info penting. Panitia mengelola semua isi lewat panel admin dan bisa mengunduh data peserta ke CSV.

Ini proyek freelance (Projects.co.id). Versi pertama dibangun sebagai MVP sekaligus demo untuk penawaran, jadi target utamanya: selesai cepat, rapi, berjalan di shared hosting murah.

## Sumber kebenaran

- `docs/PRD.md` adalah spesifikasi utama. Skema database, daftar rute, acceptance criteria, dan aturan validasi ada di sana. Kalau ragu, ikuti PRD.
- `docs/TASKS.md` adalah urutan kerja. Kerjakan berurutan, centang `[x]` setiap tugas yang selesai, dan jangan lompat ke hari berikutnya sebelum tugas hari ini lolos pengecekan.
- `docs/UI-GUIDE.md` berisi token desain dan pola tampilan. Kalau ada screenshot di `docs/ui-reference/`, tiru tampilannya.
- `database/schema.sql` dan `app/routes.php` sudah ditulis sesuai PRD. Jangan ubah struktur tabel atau path rute tanpa memperbarui PRD juga.

Kalau menemukan konflik antara file-file di atas, berhenti dan tanyakan ke pengguna. Jangan menebak.

## Batasan stack (wajib)

- PHP 8.1+ native. **Tanpa Composer, tanpa framework** (bukan Laravel, bukan Slim). Autoload dengan `spl_autoload_register` di `app/bootstrap.php`.
- MySQL 5.7+/MariaDB 10.4+ lewat PDO. Semua query wajib prepared statement.
- Frontend: HTML server-rendered, satu file `public/assets/css/app.css`, satu file `public/assets/js/app.js`. **Tanpa Tailwind, tanpa Bootstrap, tanpa jQuery, tanpa CDN, tanpa Google Fonts.** Pakai font sistem dan ikon SVG inline.
- Harus jalan di shared hosting cPanel tanpa SSH.
- Halaman peserta di bawah 150 KB, nyaman di lebar 360 px.

## Konvensi kode

- Setiap file PHP diawali `<?php` lalu `declare(strict_types=1);`.
- Namespace `App\` dipetakan ke folder `app/` (contoh: `App\Core\Router` → `app/Core/Router.php`, `App\Controllers\Admin\ScheduleDayController` → `app/Controllers/Admin/ScheduleDayController.php`).
- Gaya PSR-12. Nama kelas PascalCase, method camelCase, kolom database snake_case.
- Model berisi static method yang membungkus query (contoh `Participant::findByPhone(string $phone): ?array`). Tidak perlu ORM.
- Controller tipis: ambil input, validasi, panggil model, render view atau redirect.
- View adalah file PHP biasa. Semua output variabel lewat helper `e()` (htmlspecialchars). Teks panjang dari admin lewat `format_text()`.
- Semua teks antarmuka dalam **Bahasa Indonesia** yang sederhana. Komentar kode boleh Bahasa Indonesia atau Inggris, konsisten per file.
- Nomor HP selalu lewat `normalize_phone()` sebelum disimpan atau dicari.

## Aturan keamanan (jangan dilewati)

- Semua form POST membawa field `_csrf` dan diverifikasi di router sebelum controller jalan. Gagal → halaman 419.
- Password admin: `password_hash()` / `password_verify()`.
- Token sesi peserta: `random_bytes(32)`, simpan hash SHA-256 di `participant_sessions`, bandingkan dengan `hash_equals()`.
- `session_regenerate_id(true)` setiap login peserta maupun admin.
- Cookie: `HttpOnly`, `SameSite=Lax`, `Secure` saat HTTPS.
- Rate limit login admin: 5 percobaan gagal per IP per 15 menit (tabel `login_attempts`).
- Isi dari admin disimpan sebagai teks biasa, tidak pernah dirender sebagai HTML mentah.
- `APP_ENV=production` → `display_errors` mati, error ditulis ke `storage/logs/app.log`.

## Menjalankan secara lokal

1. Siapkan MySQL/MariaDB (XAMPP, Laragon, atau Docker).
2. Buat database, lalu impor:
   ```bash
   mysql -u root -p portal_info_peserta < database/schema.sql
   mysql -u root -p portal_info_peserta < database/seed.sql
   ```
3. Salin `.env.example` menjadi `.env`, isi kredensial database, set `APP_ENV=development` dan `APP_URL=http://localhost:8000`.
4. Jalankan server bawaan PHP:
   ```bash
   php -S localhost:8000 -t public public/index.php
   ```
   `public/index.php` sudah menangani file statis saat dijalankan dengan server bawaan.
5. Akun admin demo: `admin` / `GantiSegera123`.

## Cara memverifikasi pekerjaan

Setelah menyelesaikan setiap tugas di `docs/TASKS.md`:

1. Lint semua file PHP:
   ```bash
   find app public -name '*.php' -print0 | xargs -0 -n1 php -l
   ```
2. Jalankan server lokal dan uji alur yang baru dibuat dengan `curl` atau browser (lebar 360 px). Cocokkan dengan acceptance criteria di PRD bagian 2.2.
3. Untuk fitur peserta, uji minimal: registrasi berhasil, registrasi dengan nomor yang sama (harus diarahkan ke /masuk), masuk, hapus cookie sesi PHP lalu buka /info (harus tetap masuk lewat cookie remember), keluar.
4. Untuk ekspor CSV, pastikan ada BOM UTF-8, delimiter sesuai `CSV_DELIMITER`, dan nomor HP ditulis `="08..."`.

## Yang tidak boleh dikerjakan (di luar scope versi ini)

Multi-event, OTP SMS/WhatsApp, notifikasi otomatis, pembayaran, QR check-in, aplikasi native, upload gambar. Kalau tergoda menambahkan, catat sebagai ide di bagian bawah `docs/TASKS.md` saja.

## Gaya kerja

- Kerjakan satu tugas, verifikasi, centang, baru lanjut.
- Jangan membuat file baru di luar struktur yang sudah ada kecuali memang dibutuhkan; kalau menambah file, sebutkan alasannya.
- Jangan menghapus komentar `PRD:` di kepala file stub; ganti blok `TODO` dengan implementasi.
- Pesan commit singkat dalam Bahasa Indonesia, contoh: `feat: registrasi peserta dan normalisasi nomor HP`.
