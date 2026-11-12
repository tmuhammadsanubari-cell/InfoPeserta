-- Portal Info Peserta — data demo
-- PRD: bagian 4.2 "Data Seed". Jalankan setelah schema.sql.
-- Admin demo: admin / GantiSegera123  (WAJIB diganti setelah instalasi)

SET NAMES utf8mb4;

INSERT INTO admins (username, password_hash) VALUES
('admin', '$2y$10$97wx8F0/fzqht7sSqoATm.Y.Mq4txvqk8YEkA1MqJECTd5PQcdoDm');

INSERT INTO settings (setting_key, setting_value) VALUES
('event_name', 'Family Gathering 2026'),
('event_subtitle', 'Malang – Batu'),
('event_start_date', '2026-12-20'),
('event_end_date', '2026-12-22'),
('committee_whatsapp', '081234567890'),
('primary_color', '#0F766E'),
('registration_open', '1');

INSERT INTO departments (name, sort_order) VALUES
('Keuangan', 1),
('HRD', 2),
('Marketing', 3),
('Operasional', 4),
('IT', 5),
('Produksi', 6);

INSERT INTO schedule_days (id, day_number, `date`, title, route) VALUES
(1, 1, '2026-12-20', 'Berangkat ke Batu', 'Lamongan → Batu'),
(2, 2, '2026-12-21', 'Wisata Batu', 'Batu → Jatim Park → Alun-alun Batu'),
(3, 3, '2026-12-22', 'Pulang', 'Batu → Malang → Lamongan');

INSERT INTO schedule_items (day_id, start_time, end_time, activity, location, notes, sort_order) VALUES
(1, '05:30:00', '06:00:00', 'Kumpul dan registrasi ulang', 'Halaman Kantor Pusat Lamongan', 'Tunjukkan nama ke panitia untuk pembagian bus.', 1),
(1, '06:00:00', NULL, 'Berangkat ke Batu', 'Halaman Kantor Pusat Lamongan', 'Bus 1: Keuangan, HRD, Marketing. Bus 2: Operasional, IT, Produksi.', 2),
(1, '09:30:00', '10:00:00', 'Istirahat dan sarapan ringan', 'Rest Area Jombang', NULL, 3),
(1, '12:00:00', '13:00:00', 'Check-in hotel dan makan siang', 'Hotel Purnama Batu', 'Kunci kamar dibagikan per departemen.', 4),
(1, '15:00:00', '17:00:00', 'Acara bebas', NULL, 'Peserta boleh beristirahat atau menikmati fasilitas hotel.', 5),
(1, '19:00:00', '21:30:00', 'Makan malam dan malam keakraban', 'Ballroom Hotel Purnama Batu', 'Dress code: kaos acara.', 6),
(2, '06:30:00', '07:30:00', 'Sarapan', 'Restoran Hotel', NULL, 1),
(2, '08:00:00', NULL, 'Berangkat ke Jatim Park', 'Lobby Hotel Purnama Batu', 'Bus 1 dan Bus 2, harap tepat waktu.', 2),
(2, '08:45:00', '15:00:00', 'Wisata Jatim Park 2', 'Jatim Park 2, Batu', 'Tiket sudah termasuk. Makan siang di area food court.', 3),
(2, '15:30:00', '17:30:00', 'Belanja oleh-oleh', 'Alun-alun Kota Batu', NULL, 4),
(2, '19:00:00', '21:00:00', 'Makan malam dan doorprize', 'Ballroom Hotel Purnama Batu', NULL, 5),
(3, '06:30:00', '08:00:00', 'Sarapan dan check-out', 'Restoran Hotel', 'Pastikan tidak ada barang tertinggal di kamar.', 1),
(3, '08:30:00', NULL, 'Berangkat ke Malang', 'Lobby Hotel Purnama Batu', NULL, 2),
(3, '09:30:00', '11:30:00', 'Kunjungan Kampung Warna-Warni Jodipan', 'Kampung Warna-Warni Jodipan, Malang', NULL, 3),
(3, '12:00:00', '13:00:00', 'Makan siang', 'Rumah Makan Inggil, Malang', NULL, 4),
(3, '13:30:00', NULL, 'Perjalanan pulang ke Lamongan', NULL, 'Perkiraan tiba pukul 17.30.', 5);

INSERT INTO announcements (title, body, is_pinned, published_at) VALUES
('Perubahan titik kumpul hari ke-2', 'Titik kumpul pindah ke lobby belakang hotel karena ada perbaikan jalan di depan.\nBus tetap berangkat pukul 08.00.', 1, '2026-12-21 06:10:00'),
('Jangan lupa bawa jaket', 'Suhu di Batu malam hari bisa turun sampai 16°C.', 0, '2026-12-20 19:45:00'),
('Selamat datang!', 'Terima kasih sudah mendaftar. Semua info perjalanan ada di website ini. Simpan halaman ini di layar utama HP agar mudah dibuka.', 0, '2026-12-18 09:00:00');

INSERT INTO info_sections (title, body, sort_order) VALUES
('Titik Kumpul', 'Keberangkatan: Halaman Kantor Pusat Lamongan, pukul 05.30.\nSelama di Batu: Lobby Hotel Purnama Batu, Jl. Raya Selecta No. 1.\nKumpul 15 menit sebelum jadwal berangkat.', 1),
('Kontak Panitia', 'Koordinator: Budi Santoso — 081234567890\nBus 1: Sari Rahma — 081298765432\nBus 2: Agus Prasetyo — 085712345678', 2),
('Barang yang Perlu Dibawa', 'KTP atau kartu karyawan\nJaket dan pakaian hangat\nObat pribadi\nPower bank\nKaos acara untuk malam keakraban', 3),
('Aturan Selama Perjalanan', 'Selalu ikuti arahan panitia dan koordinator bus.\nKabari koordinator bus jika ingin memisahkan diri dari rombongan.\nJaga kebersihan bus dan hotel.', 4);

-- Peserta contoh (untuk mencoba panel admin dan ekspor CSV)
INSERT INTO participants (full_name, department_id, phone) VALUES
('Rina Wulandari', 3, '081211110001'),
('Dimas Arya Pratama', 5, '081211110002'),
('Siti Nurhaliza', 1, '081211110003'),
('Fajar Nugroho', 4, '081211110004'),
('Ayu Lestari', 2, '081211110005'),
('Rizky Ramadhan', 6, '081211110006'),
('Dewi Kartika', 3, '081211110007'),
('Hendra Gunawan', 4, '081211110008');
