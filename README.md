<div align="center">

<img src="public/assets/images/logo/logo.webp" alt="Logo Program Studi Ilmu Komputer" width="340"/>

# Website Profil Program Studi Ilmu Komputer

**Website profil resmi Program Studi Ilmu Komputer — Universitas PGRI Wiranegara**

Situs publik + panel pengelola (admin) dalam satu aplikasi **Laravel**.

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Vite](https://img.shields.io/badge/Vite-6-646CFF?style=for-the-badge&logo=vite&logoColor=white)
![Pest](https://img.shields.io/badge/Pest-4-2a2a40?style=for-the-badge&logo=pest&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-174%20passed-2ea44f?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)

</div>

---

## 📖 Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Arsitektur](#arsitektur)
- [Instalasi](#instalasi)
- [Screenshot](#screenshot)
- [Akun Admin Awal](#akun-admin-awal)
- [Perintah Artisan Tambahan](#perintah-artisan-tambahan)
- [Testing](#testing)
- [Struktur Aset](#struktur-aset)
- [Deployment](#deployment)
- [Dokumentasi Audit](#dokumentasi-audit)
- [Lisensi](#lisensi)

---

## ✨ Fitur

### 🌐 Situs Publik

| Fitur                | Keterangan                                                     |
| -------------------- | -------------------------------------------------------------- |
| 🏠 Beranda           | Hero slider, sambutan, kegiatan & alumni terbaru               |
| 🏛️ Profil Prodi      | Sejarah, visi & misi, tujuan, keunggulan, akreditasi           |
| 👨‍🏫 Dosen & 🎓 Alumni | Profil lengkap dengan foto                                     |
| 📅 Kegiatan          | Daftar, detail, dan **penerbitan otomatis** dari jadwal tayang |
| 📄 Dokumen           | Kategori, unduh, dan pratinjau PDF / DOCX                      |
| ✉️ Kontak            | Info kontak + form pesan                                       |
| 🔗 E-jurnal          | Redirect ke portal jurnal                                      |
| 📃 Halaman Statis    | Kebijakan Privasi, Aksesibilitas                               |
| 🔍 SEO               | `sitemap.xml` dinamis, canonical per halaman, JSON-LD, favicon lengkap + web manifest |

### 🔐 Panel Pengelola (`/komi-panel`)

- 📊 **Dashboard** — ringkasan konten & kesiapan publikasi
- 🖊️ **Editor konten** — Beranda, Profil Prodi, Kontak, Pengaturan Situs (logo, favicon, footer, URL e-jurnal)
- 👥 **Kelola data** — Dosen, Alumni, Kegiatan (draf/terjadwal/terbit), Dokumen & Kategori, Halaman Statis
- 🧑‍💼 **Akun Admin** — kelola akun pengelola
- ⏰ **Jadwal tayang otomatis** — kegiatan terjadwal terbit sendiri via scheduler

---

## 🛠 Teknologi

- **Laravel 13** / PHP 8.3
- **MySQL** (InnoDB, `utf8mb4_unicode_ci`)
- **Blade murni** (server-side rendering) + **Tailwind CSS v4** via **Vite 6**
- **Alpine.js** untuk interaksi ringan, **ApexCharts** (lazy-load, khusus dashboard), **Flatpickr** untuk date picker
- **Intervention Image** untuk konversi & thumbnail WebP otomatis
- **Pest 4** untuk pengujian

---

## 🏗 Arsitektur

Ringkas: controller tipis, logika di service, cache di satu tempat.

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Public/          # 8 controller per domain (Home, Profile, Activity, ...)
│   │   ├── Admin/           # panel pengelola
│   │   └── SitemapController.php
│   ├── Requests/Public/     # validasi query string tiap halaman publik
│   └── Middleware/          # SecurityHeaders (CSP), CacheHeaders
├── Services/
│   ├── Public/              # SiteService, PublicDataService, ImageService
│   ├── DashboardCache.php   # satu pintu invalidasi cache
│   └── ImageOptimizer.php   # konversi WebP + thumbnail
├── Policies/                # otorisasi per model
└── View/Composers/          # SiteComposer: bagikan $site & $contactInfo
```

Beberapa keputusan yang perlu diketahui sebelum menyentuh kode:

- **Cache punya satu pintu.** Semua invalidasi lewat `App\Services\DashboardCache`. Kalau menambah data yang tampil di dashboard atau `sitemap.xml`, daftarkan kuncinya di sana — jangan `Cache::forget()` tersebar.
- **`$site` dan `$contactInfo` sudah tersedia di semua view** lewat `SiteComposer`, tidak perlu dikirim dari controller.
- **Metadata SEO** diambil dari `config/seo.php`; tiap controller publik mengirim `seoTitle`, `seoDesc`, dan `canonical`. `canonical` sengaja tidak memakai `url()->current()` supaya `?page=2` tidak terbaca sebagai halaman berbeda.
- **Satu `<h1>` per halaman** — heading section memakai `<h2>`. Ada tes yang menjaga aturan ini.
- **Gambar** diunggah lewat `ImageOptimizer` yang otomatis membuat versi WebP + thumbnail 400w.

---

## 🚀 Instalasi

**Persyaratan:** PHP ≥ 8.3, Composer, MySQL, Node.js ≥ 20 (hanya bila ingin mengubah CSS/JS). (Opsional: CLI `mysqldump` / `mysql` untuk backup & restore.)

```bash
# 1. Install dependensi
composer install

# 2. Siapkan environment
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database di `.env` (MySQL), lalu:

```bash
# 3. Buat skema database + data awal
php artisan migrate --seed

# 4. Tautkan storage publik
php artisan storage:link

# 5. Jalankan
php artisan serve
```

Aset hasil build sudah ikut di repo (`public/build/`), jadi aplikasi langsung jalan tanpa Node.js. Bila ingin mengubah CSS/JS:

```bash
npm install
npm run dev     # mode pengembangan (hot reload)
npm run build   # build produksi — commit hasilnya
```

---

## 📸 Screenshot

> 📌 `screenshots/home.png` masih placeholder. Ganti dengan tangkapan layar beranda (mis. **Win+Shift+S**) setelah aplikasi berjalan, lalu gambar di bawah akan muncul sendiri.

### Situs Publik

![Beranda](screenshots/home.png)

### Panel Pengelola (`/komi-panel`)

![Dashboard Admin](screenshots/admin-dashboard.png)

---

## 🔐 Akun Admin Awal

Seeder membuat akun admin dari variabel env berikut (lihat `config/initial-data.php`):

```env
INITIAL_ADMIN_NAME="Administrator Prodi"
INITIAL_ADMIN_EMAIL=admin@uniwara.ac.id
INITIAL_ADMIN_PASSWORD=password
```

Login di **`/komi-panel/login`**. Di production, jangan biarkan password tetap `password` — seeder akan menolak.

---

## ⚙ Perintah Artisan Tambahan

| Perintah                                      | Fungsi                                                                                                          |
| --------------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| `php artisan migrate:safe [--fresh] [--seed]` | Migrasi dengan **backup otomatis** (mysqldump) + konfirmasi sebelum operasi destruktif                          |
| `php artisan db:restore {file}`               | Restore backup MySQL dari `storage/app/backups` — contoh: `php artisan db:restore db_prodi_20260803_093000.sql` |
| `php artisan activities:publish-scheduled`    | Terbitkan kegiatan terjadwal yang waktunya tiba                                                                 |

Perintah `activities:publish-scheduled` terdaftar di `routes/console.php`, berjalan **tiap menit**, dan memakai `withoutOverlapping()` agar tidak tumpang tindih. Jalankan scheduler lokal dengan:

```bash
php artisan schedule:work
```

Di server, tambahkan cron: `* * * * * php artisan schedule:run`

> ⚠️ Tanpa cron ini, kegiatan berstatus **Terjadwal** tidak akan pernah terbit sendiri. Pastikan juga `APP_TIMEZONE` di server sama dengan zona waktu yang dipakai saat mengisi jadwal tayang (`Asia/Jakarta`), sebab perbandingan waktunya memakai zona aplikasi.

---

## 🧪 Testing

```bash
php artisan test              # seluruh suite
php artisan test --compact    # ringkas
vendor/bin/pint --dirty       # format kode yang berubah
```

Saat ini **174 test / 417 assertion** hijau. Suite memakai database terpisah (`db_prodi_test`, lihat `phpunit.xml`) dan `RefreshDatabase`, jadi data pengembangan tidak tersentuh.

Cakupan yang perlu diketahui: akses panel admin, keamanan berkas dokumen, halaman publik, cache pengaturan situs, penerbitan kegiatan terjadwal, `sitemap.xml`, metadata SEO (canonical, JSON-LD, satu `<h1>`), favicon & manifest, serta halaman galat.

---

## 📁 Struktur Aset

```
resources/
├── css/          # Sumber CSS (Tailwind v4)
├── js/           # Sumber JavaScript
└── views/        # Blade templates
public/
├── build/        # Hasil build Vite (di-commit, siap deploy)
├── assets/       # Logo & gambar statis (WebP)
└── favicon.*     # Ikon situs + site.webmanifest
storage/app/
├── private/      # Berkas dokumen (tidak dapat diakses langsung)
└── backups/      # Hasil `migrate:safe` / sumber `db:restore`
```

Dokumen sengaja disimpan di disk privat dan disajikan lewat route ber-throttle, bukan dari `public/`.

---

## 🚢 Deployment

```bash
git pull
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan storage:link
php artisan optimize          # cache config, route, view
```

Beberapa hal yang perlu disiapkan:

- Pakai `.env.production.example` sebagai acuan `.env` produksi. Isi `APP_KEY` (`php artisan key:generate`), kredensial DB, dan `INITIAL_ADMIN_PASSWORD` yang kuat — seeder menolak nilai `password` saat `APP_ENV=production`.
- Set `APP_DEBUG=false` dan `APP_URL` ke domain final. `canonical` dan `sitemap.xml` mengikuti nilai ini.
- Tambahkan cron scheduler: `* * * * * php artisan schedule:run`
- `GA4_ID` opsional — bila kosong, skrip analitik tidak disuntikkan.
- Submit `sitemap.xml` ke Google Search Console setelah domain aktif.

Node.js tidak dibutuhkan di server karena `public/build/` sudah ikut repo.

---

## 📚 Dokumentasi Audit

Folder `docs/` memuat catatan audit performa dan SEO beserta perbaikannya — berguna bila ingin menelusuri alasan di balik sebuah keputusan teknis. Mulai dari [`docs/README.md`](docs/README.md).

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](LICENSE).

---

<div align="center">

Dibuat oleh **Mochamad Farid** ([@fardzridz](https://github.com/fardzridz)) untuk **Program Studi Ilmu Komputer** — Universitas PGRI Wiranegara

</div>
