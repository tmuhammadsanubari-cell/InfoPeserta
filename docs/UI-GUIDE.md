# Panduan UI — Portal Info Peserta

Acuan visual untuk `public/assets/css/app.css`. Kalau ada screenshot dari Google Stitch di `docs/ui-reference/`, screenshot itu yang diutamakan; file ini berisi token dan pola dasarnya.

## Prinsip

- Mobile-first, mulai dari lebar 360 px.
- Ringan: tanpa gambar dekoratif, tanpa gradien, tanpa font eksternal.
- Mudah dibaca di luar ruangan: kontras tinggi, teks dasar 16 px, target sentuh minimal 48 px.
- Semua teks Bahasa Indonesia.

## Token desain (CSS custom properties di `:root`)

| Token | Nilai | Kegunaan |
|-------|-------|----------|
| `--color-primary` | `#0F766E` | Header, tombol utama, tab aktif. Bisa ditimpa dari pengaturan admin |
| `--color-primary-soft` | `#CCFBF1` | Latar pengumuman disematkan, badge "Hari ini" |
| `--color-bg` | `#F8FAFC` | Latar halaman |
| `--color-surface` | `#FFFFFF` | Kartu |
| `--color-text` | `#0F172A` | Teks utama |
| `--color-muted` | `#64748B` | Teks sekunder, jam, waktu terbit |
| `--color-border` | `#E2E8F0` | Garis kartu dan input |
| `--color-danger` | `#DC2626` | Error, tombol hapus |
| `--color-warning-bg` | `#FEF9C3` | Alert info/peringatan |
| `--color-whatsapp` | `#16A34A` | Tombol hubungi panitia |
| `--radius` | `12px` | Kartu, input, tombol |
| `--shadow` | `0 1px 3px rgba(15,23,42,.08)` | Bayangan kartu |
| `--font` | `system-ui, -apple-system, "Segoe UI", Roboto, sans-serif` | Semua teks |

## Pola halaman peserta

- **Header**: latar `--color-primary`, teks putih, sapaan "Halo, {nama depan}", nama kegiatan, tombol "Keluar" kecil di kanan.
- **Tab hari**: baris pill yang bisa digeser horizontal. Pill aktif berwarna primer; hari ini diberi badge "Hari ini".
- **Kartu itinerary**: judul hari + rute dengan ikon rute.
- **Timeline kegiatan**: jam di kiri (lebar tetap), detail di kanan, garis vertikal tipis penghubung. Link "Buka di Maps" kecil di bawah lokasi.
- **Bottom navigation**: tetap di bawah, 3 tab (Jadwal, Pengumuman, Info) dengan ikon dan label. Titik merah kecil di Pengumuman jika ada yang baru 24 jam.
- **Tombol WhatsApp**: bulat 56 px, hijau, mengambang di kanan bawah di atas bottom navigation.
- **Form**: label di atas input, helper text abu-abu di bawah, pesan error merah di bawah field.

## Pola halaman admin

- Desktop: sidebar kiri tetap 240 px. HP: tombol hamburger membuka menu.
- Tabel di desktop berubah menjadi tumpukan kartu kecil di bawah 640 px.
- Tombol hapus selalu meminta konfirmasi (`data-confirm` ditangani `app.js`).

## Ikon

Pakai SVG inline sederhana (gaya line, stroke 2). Tidak perlu pustaka ikon.
