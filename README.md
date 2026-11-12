# Portal Info Peserta

Website ringan untuk peserta kegiatan/perjalanan: registrasi dengan nomor HP, jadwal per hari, itinerary, pengumuman, info penting, dan panel admin untuk panitia.

Stack: PHP 8.1+ native, MySQL/MariaDB, CSS dan JavaScript murni. Tanpa Composer, tanpa framework, tanpa CDN. Cocok untuk shared hosting cPanel tanpa SSH.

## Fitur

**Peserta** (dibuka dari HP)

- Daftar dengan nama lengkap, departemen, dan nomor HP/WhatsApp. Satu nomor hanya bisa mendaftar sekali.
- Masuk lagi cukup dengan nomor HP. Perangkat diingat 30 hari.
- Halaman informasi: jadwal per hari (hari ini otomatis terbuka), rute itinerary, tautan Google Maps, pengumuman (yang disematkan di atas), dan info penting.
- Tombol WhatsApp untuk menghubungi panitia.

**Panitia** (panel admin di `/admin`)

- Kelola hari, rute, dan kegiatan.
- Kelola pengumuman dan info penting.
- Data peserta: cari, filter per departemen, hapus, dan unduh ke Excel (CSV).
- Kelola departemen, pengaturan acara (nama, tanggal, nomor WhatsApp panitia, warna, buka/tutup registrasi), dan ganti password.
- Dashboard jumlah pendaftar per departemen.

## Kebutuhan server

- PHP 8.1 atau lebih baru dengan ekstensi `pdo_mysql`, `mbstring`, dan `ctype` (umumnya sudah aktif di cPanel).
- MySQL 5.7+ atau MariaDB 10.4+.
- Apache dengan `mod_rewrite` (standar di cPanel). `.htaccess` sudah disiapkan.
- SSL/HTTPS (AutoSSL atau Let's Encrypt di cPanel).

## Instalasi di hosting cPanel

Siapkan file `portal-info-peserta-YYYYMMDD.zip` (lihat bagian [Membuat file zip](#membuat-file-zip-untuk-diunggah)). Zip ini tidak berisi `.env` dan log.

### 1. Buat database

1. Buka **cPanel → MySQL® Database Wizard**.
2. Buat database, misalnya `namaakun_infopeserta`.
3. Buat user database beserta password yang kuat.
4. Beri user tersebut **ALL PRIVILEGES** ke database tadi.

Catat nama database, user, dan password. Ketiganya dipakai di langkah 4.

### 2. Impor tabel dan data awal

1. Buka **cPanel → phpMyAdmin**, pilih database yang baru dibuat.
2. Tab **Import** → pilih `database/schema.sql` dari zip (ekstrak dulu di komputer) → **Go**.
3. Ulangi untuk `database/seed.sql`.

`seed.sql` berisi akun admin, pengaturan acara, daftar departemen, dan data contoh: jadwal 3 hari, pengumuman, info penting, serta 8 peserta contoh. Untuk acara sungguhan, hapus data contoh setelah impor dengan menjalankan SQL berikut di tab **SQL** phpMyAdmin:

```sql
DELETE FROM participants;
DELETE FROM schedule_days;
DELETE FROM announcements;
```

Info penting contoh (Titik Kumpul, Kontak Panitia, dan seterusnya) sebaiknya disunting saja lewat panel admin. Departemen bisa diubah di menu **Departemen**.

### 3. Unggah file

Pilih salah satu cara. Cara A paling aman.

**Cara A: subdomain/domain mengarah ke `public/` (disarankan)**

1. **File Manager** → masuk ke folder home (satu tingkat di atas `public_html`) → **Upload** zip → **Extract**. Hasilnya folder `portal-info-peserta/`.
2. **cPanel → Domains** (atau **Subdomains**) → buat atau ubah domain, lalu isi **Document Root** dengan `portal-info-peserta/public`.

**Cara B: hanya bisa memakai `public_html`**

1. Ekstrak zip di folder home sehingga ada `/home/namaakun/portal-info-peserta/`.
2. Pindahkan **isi** folder `portal-info-peserta/public/` ke `public_html/`, termasuk `.htaccess` dan folder `assets`. Aktifkan "Show Hidden Files" di File Manager agar `.htaccess` terlihat.
3. Sunting `public_html/index.php`, lalu ganti baris `BASE_PATH`:

   ```php
   define('BASE_PATH', dirname(__DIR__) . '/portal-info-peserta');
   ```

**Cara C: semua file di `public_html` (pilihan terakhir)**

Ekstrak zip langsung ke `public_html/` (atau subfolder di dalamnya). `.htaccess` di root proyek mengarahkan request ke `public/` dan menolak akses ke `app/`, `database/`, `storage/`, `docs/`, serta `.env`. Pastikan file `.htaccess` ikut terekstrak.

### 4. Isi file `.env`

1. Di File Manager, salin `.env.example` menjadi `.env` di folder proyek (sejajar dengan `app/`).
2. Sunting `.env`:

   ```env
   APP_ENV=production
   APP_URL=https://info.domainanda.com
   DB_HOST=localhost
   DB_NAME=namaakun_infopeserta
   DB_USER=namaakun_infouser
   DB_PASSWORD=password_database
   ```

`APP_URL` harus sama persis dengan alamat website, tanpa garis miring di akhir. Jika website dipasang di subfolder, sertakan subfoldernya, misalnya `https://domainanda.com/info`.

Daftar lengkap variabel ada di `.env.example`. Nilai bawaannya sudah sesuai untuk kebanyakan acara.

### 5. Folder log dan HTTPS

1. Pastikan `storage/logs/` bisa ditulis (permission **755**; jika error tetap tidak tercatat, coba **775**).
2. Aktifkan SSL di **cPanel → SSL/TLS Status → Run AutoSSL**.
3. Setelah HTTPS aktif, buka `public/.htaccess` (atau `public_html/.htaccess` pada Cara B), lalu hapus tanda `#` di dua baris berikut:

   ```apache
   RewriteCond %{HTTPS} !=on
   RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
   ```

### 6. Masuk admin dan ganti password

1. Buka `https://domainanda.com/admin/login`.
2. Masuk dengan akun bawaan: username `admin`, password `GantiSegera123`.
3. **Segera** buka menu **Ganti Password** dan ganti password-nya. Siapa pun yang membaca README ini tahu password bawaan.
4. Buka **Pengaturan**, lalu isi nama kegiatan, tanggal, nomor WhatsApp panitia, dan warna.

### 7. Uji dari HP

Daftar sebagai peserta, keluar, masuk lagi, buka jadwal, lalu ketuk tombol WhatsApp. Terakhir, hapus peserta uji coba dari menu **Data Peserta**.

### Checklist sebelum dibagikan ke peserta

- [ ] `.env` berisi `APP_ENV=production` dan `APP_URL` yang benar.
- [ ] Alamat `/.env`, `/app/config.php`, `/database/seed.sql`, dan `/storage/logs/` menampilkan 403 atau 404, bukan isi file.
- [ ] HTTPS aktif dan pengalihan ke HTTPS sudah dinyalakan.
- [ ] Password admin bawaan sudah diganti.
- [ ] Data contoh sudah dihapus atau disunting.
- [ ] Tampilan dicek di HP (Chrome Android dan Safari iPhone).
- [ ] File CSV dari **Unduh Excel (CSV)** terbuka rapi di Excel: kolom terpisah dan angka 0 di depan nomor HP tidak hilang.

## Ganti password admin

**Masih ingat password lama:** masuk ke panel admin → menu **Ganti Password**. Password baru minimal 8 karakter.

**Lupa password:** kembalikan ke password bawaan lewat phpMyAdmin (tab **SQL**), lalu segera ganti lagi lewat menu **Ganti Password**:

```sql
UPDATE admins
SET password_hash = '$2y$10$97wx8F0/fzqht7sSqoATm.Y.Mq4txvqk8YEkA1MqJECTd5PQcdoDm'
WHERE username = 'admin';
```

Setelah perintah ini, password menjadi `GantiSegera123`.

**Terkunci karena salah password 5 kali:** login ditolak selama 15 menit dari alamat IP tersebut. Tunggu 15 menit, atau kosongkan catatannya lewat phpMyAdmin dengan `DELETE FROM login_attempts;`.

## Menjalankan di komputer sendiri

Butuh PHP 8.1+ dan MySQL/MariaDB (misalnya Laragon, XAMPP, atau Docker).

```bash
mysql -u root -p -e "CREATE DATABASE portal_info_peserta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p portal_info_peserta < database/schema.sql
mysql -u root -p portal_info_peserta < database/seed.sql
cp .env.example .env        # lalu isi kredensial database, APP_URL=http://localhost:8000
php -S localhost:8000 -t public public/index.php
```

Buka http://localhost:8000. Panel admin di http://localhost:8000/admin/login dengan akun demo `admin` / `GantiSegera123`.

Saat `APP_ENV=development`, detail error tampil di layar. Saat `production`, pengguna hanya melihat pesan umum dan detailnya dicatat di `storage/logs/app.log`.

## Membuat file zip untuk diunggah

Dari folder proyek (repo git), setelah semua perubahan di-commit:

```bash
git archive --format=zip -o portal-info-peserta-$(date +%Y%m%d).zip HEAD -- . ':!docs' ':!CLAUDE.md'
```

Zip hanya berisi file yang tercatat di git, jadi `.env` dan log tidak ikut. Folder `docs/` dan `CLAUDE.md` tidak dibutuhkan di server.

## Struktur folder

```
app/          Kode aplikasi (router, controller, model, view). Tidak boleh dibuka dari browser.
database/     schema.sql dan seed.sql untuk diimpor lewat phpMyAdmin.
public/       Satu-satunya folder yang perlu bisa diakses publik (index.php, .htaccess, assets/).
storage/logs/ Log error aplikasi.
docs/         PRD, daftar tugas, panduan UI, dan screenshot acuan (untuk pengembang).
.env.example  Contoh konfigurasi. Salin menjadi .env.
```

## Dokumen pengembang

- `CLAUDE.md`: panduan kerja untuk Claude Code (baca pertama)
- `docs/PRD.md`: spesifikasi lengkap
- `docs/TASKS.md`: urutan pengerjaan dan progres
- `docs/UI-GUIDE.md`: token desain dan pola tampilan
- `docs/ui-reference/`: screenshot acuan dari Google Stitch
