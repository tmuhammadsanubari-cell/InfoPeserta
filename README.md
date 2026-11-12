# Portal Info Peserta

Website ringan untuk peserta kegiatan/perjalanan: registrasi dengan nomor HP, jadwal per hari, itinerary, pengumuman, info penting, dan panel admin untuk panitia.

Stack: PHP 8.1+ native, MySQL/MariaDB, CSS dan JavaScript murni. Tanpa Composer, cocok untuk shared hosting cPanel.

## Dokumen

- `CLAUDE.md` — panduan kerja untuk Claude Code (baca pertama)
- `docs/PRD.md` — spesifikasi lengkap
- `docs/TASKS.md` — urutan pengerjaan dan progres
- `docs/UI-GUIDE.md` — token desain dan pola tampilan
- `docs/ui-reference/` — screenshot acuan dari Google Stitch

## Menjalankan di komputer sendiri

```bash
mysql -u root -p -e "CREATE DATABASE portal_info_peserta CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
mysql -u root -p portal_info_peserta < database/schema.sql
mysql -u root -p portal_info_peserta < database/seed.sql
cp .env.example .env        # lalu isi kredensial database
php -S localhost:8000 -t public public/index.php
```

Buka http://localhost:8000. Panel admin di http://localhost:8000/admin/login dengan akun demo `admin` / `GantiSegera123`.

## Instalasi di hosting (cPanel)

Lihat PRD bagian 4.7. Ringkasnya: buat database, impor `schema.sql` (dan `seed.sql` untuk demo), unggah file, arahkan document root ke `public/`, isi `.env`, aktifkan SSL, lalu segera ganti password admin.
