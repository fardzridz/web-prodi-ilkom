# 00 — Overview Audit Performa Web Prodi

> **Baca ini dulu sebelum eksekusi 01-07.** Dokumen ini rangkum baseline, temuan lintas modul, dan cara ukur biar perbaikan terukur.

## 📌 Ringkasan Kode & Fungsi

| Lapisan | File Utama | Fungsi | Status Saat Ini |
|---------|------------|--------|-----------------|
| **Public pages** | `app/Http/Controllers/PublicController.php:30` (519 line) | `home()`, `activities()`, `lecturers()`, `documents()`, `alumni()`, `profile()` — layani `/`, `/kegiatan`, `/dosen`, `/dokumen`, `/alumni`, `/profil` | Semua list pakai `->get()->map()->all()` tanpa `paginate` |
| **Admin dashboard** | `app/Http/Controllers/Admin/DashboardController.php:22` (314 line) | `index()` → 4 summary cards + `latestContent()` + `chartActivityMonthly()` + `chartCombinedMonthly()` | ~30 query per load |
| **View shared** | `app/Providers/AppServiceProvider.php:26` + `PublicController.php:30` `__construct` | Share `site` & `contactInfo` ke semua view | Duplikasi, `View::composer('*')` per view instance |
| **DB** | `database/migrations/*` (22 file), `config/database.php:20` | MySQL InnoDB `utf8mb4_unicode_ci` (harusnya), 12 tabel | Missing composite index, `VARCHAR(255)` boros, `BIGINT` overkill |
| **Frontend** | `resources/views/layouts/public.blade.php:23`, `vite.config.js:5`, `public/assets/images/*` | Blade + Tailwind v4 + Vite | CSS via Vite, JS via `asset()` static, image 2-3MB, admin.js 946KB |

**Stack terdeteksi:** Laravel 13, PHP 8.3, Tailwind v4, Vite 6.3, Alpine 3.16, ApexCharts 6.8, Flatpickr 4.6.

## 🚨 Isu Lintas Modul (Peta Prioritas)

### HIGH — Wajib Fix Duluan (Hari 1)

| ID | Modul | File:Line | Dampak Terukur |
|----|-------|-----------|----------------|
| H1 | 01 | `PublicController.php:356` `activitiesData()` <br> `444` `lecturersData()` <br> `469` `alumniData()` <br> `498` `documentsData()` | **Root cause #1.** Load semua rows + `SELECT *` (include `mediumText`/`longText`) → 500 rows = 5MB PHP + 500 DOM nodes. HTML `data-search` bengkak. |
| H2 | 02 | `DashboardController.php:251` `chartActivityMonthly()` <br> `290` `chartCombinedMonthly()` | 18 `COUNT(*)` loop (6+12) + 4 `countsByStatus` + 4 `latestContent` + 4 `publicReadiness` = ~30 query/dashboard |
| H3 | 03 | `AppServiceProvider.php:26` `View::composer('*')` <br> `PublicController.php:30` `__construct` | 3 cache hit + 2 `SELECT *` per request, `composer('*')` jalan per `@include` (sidebar, flash, dll) |
| H4 | 06 | `public/assets/images/logo-ilkom-motion.gif:2716733` (2.59MB) <br> `error-bg.gif:3024471` (2.88MB) <br> `hero-*.png:1.4-2.1MB` | LCP >4s mobile, bandwidth 3× lipat |
| H5 | 05 | `resources/js/admin.js:1` → `public/build/assets/admin-DOC2Oh_H.js:969247` | Admin first load 946KB eager, tidak ada `manualChunks` |

### MEDIUM — Fix Hari 1-2

| ID | Modul | File:Line | Ringkas |
|----|-------|-----------|---------|
| M1 | 01,04 | `PublicController.php:157` `distinct pluck('expertise')` tanpa cache/index | Distinct scan tiap request |
| M2 | 01,04 | `Admin/*Controller.php:27` `where('title','like',"%{$search}%")` | Leading `%` tidak pakai index → full scan |
| M3 | 01,06 | `activities/index.blade.php:59` `data-search="{{ $activity['content'] }}"` | Kirim full content di attribute, JS `cards.forEach` tanpa debounce |
| M4 | 05 | `layouts/public.blade.php:23` `@vite('css')` tapi JS `asset('js/app.js')` | Hash Vite tidak kepakai, stale cache |
| M5 | 07 | `config/cache.php:18` `CACHE_STORE=database`, `public/.htaccess:27` minimal | Cache 5ms vs file 0.1ms, tidak ada `immutable`/`deflate` |
| M6 | 06 | `admin/lecturers/index.blade.php:70` `Storage::exists` di loop | 10 `stat()` per halaman, disk `local` vs `public` mismatch |
| M7 | 04 | 22 migrations | Missing `[status,activity_date]`, `[status,expertise]`, `[status,job_position]`, `messages` tanpa index, `VARCHAR(255)` status, `BIGINT` singleton |

### LOW — Polish

- `SecurityHeaders.php:21` `file_exists(public_path('hot'))` tiap request (M6 di 03)
- `2026_08_01_170000_create_pages_table.php:23` seeding di migration
- `admin.css:1` `@import` Google Fonts blocking

## 📊 Baseline — Cara Ukur Sebelum Fix

### 1. Backend Query Count

```bash
# Aktifkan debugbar atau log query sementara
# di AppServiceProvider::boot() tambah:
\DB::listen(fn($q) => \Log::info($q->sql.' ['.implode(',',$q->bindings).'] '.$q->time.'ms'));

# Lalu hit:
php artisan serve
# Buka http://localhost:8000/kegiatan — cek storage/logs/laravel.log
# Hitung jumlah SELECT per halaman (expected sekarang: ~5-7 di home, ~30 di dashboard)

# Atau pakai tinker:
php artisan tinker --execute 'DB::enableQueryLog(); app(App\Http\Controllers\PublicController::class)->activities(); print_r(count(DB::getQueryLog()));'
```

**Expected baseline sekarang:**
- `/` (home): 5-7 query (homeSection + programProfile + 3 activities + 3 alumni + site/contact) + 3 cache hit
- `/kegiatan`: 2 query (activities get + categories pluck) tapi load N rows tanpa limit
- `/komi-panel/dashboard`: ~30 query (4+4+4+6+12)

### 2. DB EXPLAIN

```sql
EXPLAIN SELECT * FROM activities WHERE status='published' ORDER BY activity_date DESC;
-- Expected sekarang: Using where; Using filesort (karena index ['status','published_at'] tidak cover activity_date)

EXPLAIN SELECT DISTINCT category FROM activities WHERE status='published' ORDER BY category;
-- Expected: Using where; Using temporary

EXPLAIN SELECT * FROM lecturers WHERE status='active' ORDER BY sort_order, name, id;
-- Expected: Using where; Using filesort (index ['status','sort_order'] tidak cover name)
```

### 3. Frontend Bundle & Image

```bash
npm run build
ls -lh public/build/assets/
# Expected sekarang:
# admin-DOC2Oh_H.js 969KB
# app-CUMh4Qnv.css 91KB
# app-BJeq2Kcr.js 3.7KB

ls -lh public/assets/images/
# error-bg.gif 3.0M, logo-ilkom-motion.gif 2.7M, hero-*.png 1.4-2.1M

# Lighthouse
npx lighthouse http://localhost:8000 --view --preset=desktop
# Catat LCP, TTFB, Total Blocking Time
```

### 4. HTTP Headers

```bash
curl -I http://localhost:8000/build/assets/app-CUMh4Qnv.css
# Expected sekarang: tidak ada Cache-Control: immutable, tidak ada Content-Encoding: gzip/br
```

## 🗂️ Peta File Terdampak per Modul

```
01: app/Http/Controllers/PublicController.php:356,444,469,498
    resources/views/public/activities/index.blade.php:58
    resources/views/public/lecturers.blade.php:57
    resources/views/public/documents.blade.php:172
    resources/views/public/alumni.blade.php:60
    public/js/list-filter.js:18

02: app/Http/Controllers/Admin/DashboardController.php:251,290,121,133,219

03: app/Providers/AppServiceProvider.php:26
    app/Http/Controllers/PublicController.php:30
    app/Http/Middleware/SecurityHeaders.php:21

04: database/migrations/* (22 files)
    config/database.php:20,47,56
    app/Models/*.php

05: vite.config.js:5
    resources/js/admin.js:1
    resources/js/app.js:1
    resources/views/layouts/public.blade.php:23
    public/js/app.js:5754 (duplikat)

06: public/assets/images/* (gif/png 2-3MB)
    resources/views/components/hero.blade.php:5
    resources/views/components/navbar.blade.php:4
    resources/views/public/home.blade.php:68,114
    resources/views/admin/*/index.blade.php:70
    resources/views/components/footer.blade.php:89

07: config/cache.php:18
    public/.htaccess:27
    bootstrap/app.php:50
    .env.example:35 CACHE_STORE
```

## ✅ Checklist Verifikasi Global (Setelah Semua Modul Selesai)

- [ ] `vendor/bin/pint --dirty --format agent` — no style error
- [ ] `php artisan test --compact` — all pass
- [ ] `php artisan route:list --except-vendor` — routes tetap jalan
- [ ] `EXPLAIN` untuk 4 query H1/H2/M2 sudah `Using index` bukan `filesort`
- [ ] `npm run build` — admin.js <400KB, image <200KB each
- [ ] Lighthouse LCP <2.5s, TTFB <600ms di `/kegiatan` dengan 100+ rows dummy
- [ ] `curl -I` cek `Cache-Control: immutable` untuk `build/assets/*`
- [ ] `php artisan optimize` clear & cache di production

## 💡 Cara Pakai Dokumen Ini

1. Baca **00** ini sampai paham baseline.
2. Eksekusi **01 → 02 → 03** (backend, bisa paralel).
3. Eksekusi **04** (DB) — butuh `php artisan migrate` + `EXPLAIN` ulang.
4. Eksekusi **05 → 06** (frontend) — butuh `npm run build` + cek size.
5. Eksekusi **07** (infra) — terakhir karena ubah `.htaccess` & cache driver.
6. Tiap modul selesai, ceklis di `README.md` + commit terpisah (`git add docs/01... && git commit`).

**Next:** Lanjut ke [`01-backend-public-controller.md`](./01-backend-public-controller.md)
