<div align="center">

<img src="public/assets/images/logo/logo.png" alt="Logo Program Studi Ilmu Komputer" width="340"/>

# Website Profil Program Studi Ilmu Komputer

**Website profil resmi Program Studi Ilmu Komputer — Universitas PGRI Wiranegara**

Situs publik + panel pengelola (admin) dalam satu aplikasi **Laravel**.

![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwindcss&logoColor=white)
![Pest](https://img.shields.io/badge/Pest-4-2a2a40?style=for-the-badge&logo=pest&logoColor=white)
![Tests](https://img.shields.io/badge/Tests-%E2%9C%93%20passed-2ea44f?style=for-the-badge)
![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)

</div>

---

## 📖 Daftar Isi

- [Fitur](#fitur)
- [Teknologi](#teknologi)
- [Instalasi](#instalasi)
- [Screenshot](#screenshot)
- [Akun Admin Awal](#akun-admin-awal)
- [Perintah Artisan Tambahan](#perintah-artisan-tambahan)
- [Testing](#testing)
- [Struktur Aset](#struktur-aset)
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

### 🔐 Panel Pengelola (`/komi-panel`)

- 📊 **Dashboard** — ringkasan konten & kesiapan publikasi
- 🖊️ **Editor konten** — Beranda, Profil Prodi, Kontak, Pengaturan Situs (logo, favicon, footer, URL e-jurnal)
- 👥 **Kelola data** — Dosen, Alumni, Kegiatan (draf/terjadwal/terbit), Dokumen & Kategori, Halaman Statis
- 🧑‍💼 **Akun Admin** — kelola akun pengelola
- ⏰ **Jadwal tayang otomatis** — kegiatan terjadwal terbit sendiri via scheduler

---

## 🛠 Teknologi

- **Laravel 13** / PHP 8.3
- **MySQL** (InnoDB, `utf8mb4`)
- **Blade murni** (server-side rendering) + **Tailwind CSS v4** + **Font Awesome** (CDN)

---

## 🚀 Instalasi

**Persyaratan:** PHP ≥ 8.3, Composer, MySQL. (Opsional: CLI `mysqldump` / `mysql` untuk backup & restore.)

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

---

## 📸 Screenshot

### Situs Publik

![Beranda](screenshots/home.png)

### Panel Pengelola (`/komi-panel`)

![Dashboard Admin](screenshots/admin-dashboard.png)

> 📌 Screenshot akan tersedia di folder `screenshots/`. Belum sempat ambil? Bisa pakai tool apa saja (mis. **Win+Shift+S** di Windows) setelah aplikasi berjalan.

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

Perintah `activities:publish-scheduled` terdaftar di `routes/console.php` dan berjalan **tiap menit**. Jalankan scheduler lokal dengan:

```bash
php artisan schedule:work
```

Di server, tambahkan cron: `* * * * * php artisan schedule:run`

---

## 🧪 Testing

```bash
php artisan test
```

Suite test menggunakan database terpisah (`db_prodi_test`, lihat `phpunit.xml`).

---

## 📁 Struktur Aset

```
resources/
├── css/          # Sumber CSS (Tailwind v4)
├── js/           # Sumber JavaScript
└── views/        # Blade templates
public/
├── build/        # Hasil build Vite (di-commit, siap deploy)
└── assets/       # Font, logo, dan gambar statis
```

---

## 📄 Lisensi

Proyek ini dilisensikan di bawah [MIT License](https://opensource.org/licenses/MIT).

---

<div align="center">

Dibuat dengan ❤️ oleh **Ridz** ([@fardzridz](https://github.com/fardzridz)) untuk **Program Studi Ilmu Komputer** — Universitas PGRI Wiranegara

</div>
