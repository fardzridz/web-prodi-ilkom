# 📚 Docs Optimasi Performa — Web Prodi Ilmu Komputer

> **Tujuan:** Menjawab keluhan "web lambat" dengan audit menyeluruh Laravel + MySQL InnoDB + Blade + Vite, lalu perbaikan bertahap yang **terukur, reversible, dan tidak ada yang terlewat**.

## 🗺️ Peta Modul

| # | File | Fokus | Urgensi | Estimasi |
|---|------|-------|---------|----------|
| 00 | [`00-overview.md`](./00-overview.md) | Baseline, peta file terdampak, prioritas, cara ukur | — | 30 menit baca |
| 01 | [`01-backend-public-controller.md`](./01-backend-public-controller.md) | `PublicController` pagination + `select()` + cache | **HIGH** | 0.5 hari |
| 02 | [`02-backend-dashboard.md`](./02-backend-dashboard.md) | `DashboardController` 18 query loop → 1 GROUP BY | **HIGH** | 0.25 hari |
| 03 | [`03-backend-view-composer.md`](./03-backend-view-composer.md) | Duplikasi `AppServiceProvider` vs `PublicController::__construct` | **HIGH** | 0.25 hari |
| 04 | [`04-database-mysql.md`](./04-database-mysql.md) | Index komposit, FULLTEXT, FK, tipe data efisien | **MEDIUM-HIGH** | 0.5 hari |
| 05 | [`05-frontend-vite.md`](./05-frontend-vite.md) | `vite.config.js` manualChunks, hash asset | **HIGH** | 0.25 hari |
| 06 | [`06-frontend-media-blade.md`](./06-frontend-media-blade.md) | Image 2-3MB, hero `fetchpriority`, Blade N-stat, Maps lazy | **HIGH-MED** | 0.5 hari |
| 07 | [`07-infra-cache-http.md`](./07-infra-cache-http.md) | `CACHE_STORE`, `.htaccess` expires/deflate, `php artisan optimize` | **MEDIUM** | 0.25 hari |
| 08 | [`08-seo-audit-kompetitif-rencana-strategis.md`](./08-seo-audit-kompetitif-rencana-strategis.md) | SEO audit kompetitif UNMERPAS/Yudharta/UB, keyword gap, JSON-LD, sitemap, GSC/GA4 — **Fase 1 lock 2026-08-23, Fase 2/3 backlog** | **HIGH** | 0.5 hari (Fase 1) |

**Total estimasi eksekusi berurutan:** ~2.5 hari (bisa paralel 01 & 04, 05 & 06, 08 Fase 1 quick wins). **Update 2026-08-25:** 01-07 done, **08 Fase 1 done (kode + tes)**, Fase 2/3 backlog tunggu konten asli.

## 🔄 Urutan Eksekusi Rekomendasi

```
00 (baca dulu)
 └─► 01 ─┐
         ├─► 04 ─► 07 ─► 08 Fase1 (Quick Wins)
 02 ─┤   │              └─► 08 Fase2 (Interlinking 4 artikel)
 03 ─┘   └─► 05 ─► 06 ─► 08 Fase3 (Landing konversi)
```

* **01** harus sebelum **06** (Blade pagination ubah HTML yang dipakai 06).
* **04** harus sebelum **01** bagian `whereFullText` (butuh index dulu) — atau pakai fallback `LIKE 'prefix%'`.
* **05** & **06** bisa paralel setelah **01**.

## ✅ Checklist Global — UPDATE 2026-08-25

- [x] Baca `00-overview.md` — catat baseline TTFB, query count, Lighthouse
- [x] Eksekusi 01 — `PublicController.php` pagination
- [x] Eksekusi 02 — `DashboardController.php` GROUP BY
- [x] Eksekusi 03 — `AppServiceProvider.php` view composer scope
- [x] Eksekusi 04 — migration index baru + `EXPLAIN`
- [x] Eksekusi 05 — `vite.config.js` + `layouts/public.blade.php` `@vite`
- [x] Eksekusi 06 — kompres image, hero attrs, Blade exists cache
- [x] Eksekusi 07 — `config/cache.php`, `.htaccess`, `php artisan optimize`
- [x] Eksekusi 08 Fase 1 — **DONE**: `SitemapController` + `resources/views/sitemap.blade.php`, `config/seo.php`, `components/seo-jsonld.blade.php`, canonical + title/desc per halaman di 8 controller `App\Http\Controllers\Public\*`, `robots.txt` disallow admin/berkas, single-`h1` per halaman, alt hero deskriptif. Tes: `tests/Feature/SeoMetaTest.php` + `SitemapTest.php`
- [ ] Backlog 08 Fase 2/3 — tunda tunggu 4 artikel 600-1000 kata (penulis) + copy Biaya/FAQ + `GA4_ID`
- [x] Verifikasi akhir: `vendor/bin/pint --dirty` pass, `php artisan test --compact` 151 pass / 358 assertions, `npm run build` pass, sitemap `Content-Type: text/xml`

## 🧪 Cara Verifikasi Tiap Modul

Tiap modul punya section **Verifikasi** dengan perintah siap copy-paste:

```bash
# Contoh global
php artisan test --compact
vendor/bin/pint --dirty --format agent
php artisan route:list --except-vendor
php artisan config:show database.default
```

Untuk DB:

```sql
EXPLAIN SELECT * FROM activities WHERE status='published' ORDER BY activity_date DESC;
SHOW INDEX FROM activities;
```

Untuk frontend:

```bash
npm run build
# cek public/build/assets/*.js size
npx lighthouse http://localhost:8000 --view
```

## 📌 Konvensi Penulisan Modul

Setiap modul mengikuti struktur `AGENTS.md:26`:

1. **📌 Ringkasan Kode & Fungsi** — file:line terdampak
2. **🚨 Isu / Celah (Low/Med/High)** — evidence
3. **🛠️ Rekomendasi (Before → After, kode utuh siap pakai)**
4. **💡 Penjelasan Perubahan + Verifikasi**

## ⚠️ Catatan Penting

- Database utama **MySQL InnoDB `utf8mb4_unicode_ci`** — abaikan `database/database.sqlite`.
- Jangan ubah `.env` production tanpa cek `config/database.php:20` default `sqlite` (akan di-fix di 04 & 07).
- Semua perubahan **reversible** via `php artisan migrate:rollback` atau `git diff`.

---

**Mulai dari:** [`00-overview.md`](./00-overview.md) → lalu `01` dst. Tanya bang kalau ada yang kurang jelas sebelum eksekusi code.
