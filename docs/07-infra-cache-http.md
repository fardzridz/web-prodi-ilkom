# 07 — Infra Cache & HTTP: Driver, Headers & Optimize

> **Target:** `config/cache.php:18` `CACHE_STORE=database`, `public/.htaccess:27`, `bootstrap/app.php:50`, `.env.example:35`, `config/database.php:20` cache table, `php artisan optimize`

## 📌 Ringkasan Kode & Fungsi

**Cache driver sekarang:**
```php
// config/cache.php:18
'default' => env('CACHE_STORE', 'database'),
// .env.example:35
CACHE_STORE=database
// database/migrations/0001_01_01_000001_create_cache_table.php
// cache table: key VARCHAR(255) PK, value mediumText, expiration BIGINT index
```
→ Setiap `Cache::rememberForever('site_setting', ...)` di `AppServiceProvider.php:28` + `PublicController.php:33` + `DashboardController.php` (6 keys di 02) = `SELECT value FROM cache WHERE key=?` per remember. Database cache = **paling lambat** (DB roundtrip 5ms vs file 0.4ms vs redis 0.1ms). Juga pakai `database.sqlite` fallback risk (lihat 04).

**.htaccess sekarang:**
```apache
# public/.htaccess:27
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 month"
  ExpiresByType text/html "access plus 0 seconds"
</IfModule>
```
→ Hanya `html` 0s + default 1 month. Tidak ada `image/*`, `font/*`, `text/css`, `application/javascript`, tidak ada `Cache-Control: immutable` untuk `build/assets/*` hashed, tidak ada `mod_deflate`/`mod_brotli`.

**Bootstrap:**
```php
// bootstrap/app.php:50 minimal, tidak ada cache.headers middleware, trustProxies('*') ok
```

**Optimize:**
- Belum ada `php artisan route:cache`, `config:cache`, `view:cache`, `event:cache` di production (`.env.example` `APP_ENV=local`, `APP_DEBUG=true`).
- `config/app.php` & `bootstrap/app.php` tidak tune `view.compiled` etc.

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| M5-1 | `config/cache.php:18` + `.env.example:35` | **MEDIUM** | `CACHE_STORE=database` → tiap `Cache::get` = `SELECT * FROM cache WHERE key=?`. Untuk `site_setting` + `contact_info` + 6 dashboard keys = 8 SELECT per dashboard load (cache hit). Jika traffic 100 req/min → 800 DB query cuma untuk cache. |
| M5-2 | `public/.htaccess:27` | **MEDIUM** | Missing `ExpiresByType` untuk `image/webp`, `font/woff2`, `text/css`, `application/javascript`, `video/*`. Browser revalidate tiap request. Tidak ada `Header set Cache-Control "public, immutable, max-age=31536000"` untuk `build/assets/*` (Vite hash). Tidak ada `mod_deflate` gzip. |
| M5-3 | `public/build/assets/*` | **MEDIUM** | `manifest.json` hash sudah benar (`app-BJeq2Kcr.js`, `admin-DOC2Oh_H.js`) tapi tanpa `.htaccess` immutable, browser tetap `If-None-Match` tiap load. Tidak ada pre-compress `*.gz`/`*.br`. |
| M5-4 | `php artisan optimize` belum | **MEDIUM** | `route:cache` + `config:cache` + `view:cache` tidak jalan → tiap request parse 86 `routes/web.php` lines + 126 `config/app.php` + compile Blade. Di production bisa -50ms. |
| LOW-1 | `database/migrations/0001...create_cache_table.php:17` `expiration BIGINT signed` | **LOW** | `expiration` unix timestamp harus `unsignedInteger`/`unsignedBigInteger`, sekarang `signed` waste range. Tidak urgent. |
| LOW-2 | `bootstrap/app.php` no http cache | **LOW** | Public pages `/kegiatan`, `/dosen` bisa `Cache-Control: public, max-age=60` + `ETag` untuk CDN. Sekarang no header → selalu 200 tanpa 304. |

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Cache Driver — Database → File (Quick Win) atau Redis (Best)

**Before `.env.example:35` & `config/cache.php:18`:**
```
CACHE_STORE=database
```
```php
'default' => env('CACHE_STORE', 'database'),
```

**After (Opsi 1: File — no infra, 0.4ms):**

**.env.example:35 & .env:35:**
```
CACHE_STORE=file
# or for production with redis:
# CACHE_STORE=redis
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
```

**config/cache.php:18:**
```php
'default' => env('CACHE_STORE', 'file'),
```

**Jika Redis tersedia (Opsi 2 — best):**
```php
// config/cache.php sudah ada redis store
// .env
CACHE_STORE=redis
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_DB=0
// tambah di config/database.php redis connection jika belum
```

**Bersihkan cache lama:**
```bash
php artisan cache:clear
php artisan cache:table # jika tetap pakai database untuk queue, tapi store file
```

**Kenapa file > database?** File pakai `file_get_contents` + `opcache` (0.4ms), database pakai `SELECT` + PDO + InnoDB buffer (5ms). Untuk `rememberForever` yang hit tiap request, 8×5ms=40ms vs 8×0.4=3ms.

### B. .htaccess — Expires, Immutable & Deflate

**Before `public/.htaccess:27`:**
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 month"
  ExpiresByType text/html "access plus 0 seconds"
</IfModule>
```

**After:**
```apache
<IfModule mod_expires.c>
  ExpiresActive On
  ExpiresDefault "access plus 1 month"
  ExpiresByType text/html "access plus 0 seconds"
  ExpiresByType text/css "access plus 1 year"
  ExpiresByType application/javascript "access plus 1 year"
  ExpiresByType application/x-javascript "access plus 1 year"
  ExpiresByType font/woff "access plus 1 year"
  ExpiresByType font/woff2 "access plus 1 year"
  ExpiresByType image/avif "access plus 1 year"
  ExpiresByType image/webp "access plus 1 year"
  ExpiresByType image/jpeg "access plus 1 year"
  ExpiresByType image/png "access plus 1 year"
  ExpiresByType image/gif "access plus 1 year"
  ExpiresByType image/svg+xml "access plus 1 year"
  ExpiresByType video/mp4 "access plus 1 year"
  ExpiresByType video/webm "access plus 1 year"
</IfModule>

# Immutable for Vite hashed assets (build/assets/*)
<IfModule mod_headers.c>
  <FilesMatch "\.(css|js|woff2|webp|avif|jpg|jpeg|png|gif|svg|mp4|webm)$">
    Header set Cache-Control "public, max-age=31536000, immutable"
  </FilesMatch>
  # HTML no cache
  <FilesMatch "\.(html|htm)$">
    Header set Cache-Control "no-cache, must-revalidate"
  </FilesMatch>
</IfModule>

# Gzip / Brotli compress
<IfModule mod_deflate.c>
  AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css text/javascript application/javascript application/json application/xml font/woff2 image/svg+xml
</IfModule>
<IfModule mod_brotli.c>
  AddOutputFilterByType BROTLI_COMPRESS text/html text/plain text/xml text/css text/javascript application/javascript application/json
</IfModule>

# Security headers (jaga yang sudah ada di SecurityHeaders middleware)
<IfModule mod_headers.c>
  Header always set X-Content-Type-Options "nosniff"
  Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

**Alternatif jika pakai Nginx (bukan Apache):** tambah di `nginx.conf`:
```nginx
location ~* ^/build/assets/ {
  expires 1y;
  add_header Cache-Control "public, immutable";
}
location ~* \.(woff2|webp|avif)$ { expires 1y; }
gzip on; brotli on;
```

### C. HTTP Cache Headers untuk Public Pages (Opsional)

Tambah middleware `app/Http/Middleware/CacheHeaders.php`:
```php
public function handle(Request $request, Closure $next): SymfonyResponse {
  $response = $next($request);
  if ($request->is('kegiatan*','dosen*','dokumen*','alumni*') && $response->isSuccessful()) {
    $response->headers->set('Cache-Control', 'public, max-age=60, stale-while-revalidate=300');
    $response->setEtag(md5($response->getContent()));
  }
  return $response;
}
```
Daftar di `bootstrap/app.php:50` `->withMiddleware(fn($m)=> $m->append(CacheHeaders::class))` untuk public routes.

**Tapi hati-hati:** pages pagination harus `max-age` pendek (60s) bukan `immutable` karena content dinamis.

### D. Laravel Optimize di Production

**Before:** tidak ada cache optimize, tiap deploy manual `php artisan serve`.

**After — Tambah ke deploy script / `composer.json` post-install:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
# php artisan optimize sudah include 3 di atas di Laravel 11+
php artisan optimize
```

**Tambah `scripts` di `composer.json`:**
```json
"scripts": {
  "post-install-cmd": ["@php artisan optimize:clear", "@php artisan optimize"]
}
```

**Pastikan `.env` production:**
```
APP_ENV=production
APP_DEBUG=false
CACHE_STORE=file
# atau redis
```

**Jangan lupa clear saat development:**
```bash
php artisan optimize:clear
php artisan cache:clear
php artisan view:clear
```

### E. Vite Pre-compress (Opsional)

Install `vite-plugin-compression` untuk generate `.gz` + `.br` saat `npm run build`:

```js
// vite.config.js
import viteCompression from 'vite-plugin-compression';
export default defineConfig({
  plugins:[ laravel(...), tailwindcss(), viteCompression({algorithm:'gzip'}), viteCompression({algorithm:'brotliCompress'}) ]
});
```
→ `public/build/assets/app-*.js.gz` + `.br` — server serve compressed tanpa CPU per request.

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa | Dampak |
|-----------|--------|--------|
| `database` → `file`/`redis` | DB cache = `SELECT` per `Cache::get`, file = `file_get_contents`, redis = in-memory. Untuk `rememberForever` 8 keys tiap request, DB 40ms → file 3ms. | Cache hit -90%, DB QPS -800/min |
| `.htaccess` expires 1y + immutable | `build/assets/*` Vite hash unik per build (`app-BJeq2Kcr.js`). Jika immutable, browser cache 1 tahun tanpa revalidate. Sebelumnya `1 month` + no immutable → tiap reload `If-None-Match`. | Repeat visit 0 request untuk assets, bandwidth -50% |
| `mod_deflate` + `brotli` | CSS 91KB + JS 180KB tanpa compress → 30KB gzip. Tanpa `deflate`, tiap request kirim full. | Transfer -70%, LCP -200ms |
| `max-age=60` untuk public list | `/kegiatan?page=2` dinamis, tapi boleh cache 60s di browser/CDN (`stale-while-revalidate` 5 menit). | CDN hit, TTFB -80% untuk anon user |
| `php artisan optimize` | Parse `routes/web.php` 86 lines + `config/*` 10 files tiap request → cache compiled `bootstrap/cache/routes-v7.php`. | Request -30ms, CPU -20% |

**Risiko & Mitigasi:**
- `CACHE_STORE=file` di multi-server (load balancer) tidak shared → pakai `redis` jika >1 instance. Untuk single VPS, `file` cukup dan paling cepat.
- `.htaccess` `immutable` untuk `build/assets` aman karena Vite hash. Jangan pakai untuk `storage/*` (file upload tanpa hash) → keep `1 month` saja.
- `route:cache` gagal jika ada closure routes → cek `php artisan route:list` pastikan semua controller-based (sudah). `route:cache` error jika ada `Route::get('/', fn()=>...)`.
- `mod_deflate` butuh `a2enmod deflate` di Apache — cek `apachectl -M | grep deflate`.

## ✅ Checklist

- [ ] `.env.example:35` + `.env` ganti `CACHE_STORE=database` → `file` (atau `redis` jika ada)
- [ ] `config/cache.php:18` default `file`
- [ ] `php artisan cache:clear` + `php artisan config:clear`
- [ ] `public/.htaccess:27` ganti dengan versi lengkap (expires 1y, immutable, deflate, brotli, headers) seperti di atas
- [ ] (Opsional) `app/Http/Middleware/CacheHeaders.php` untuk public list `max-age=60`
- [ ] `bootstrap/app.php` register middleware jika pakai CacheHeaders
- [ ] `composer.json` tambah `post-install-cmd` `optimize`
- [ ] Jalankan `php artisan optimize` di production, `php artisan optimize:clear` di dev
- [ ] (Opsional) `vite.config.js` + `vite-plugin-compression` untuk `.gz/.br`
- [ ] `npm run build` + `php artisan serve` test headers

## 🧪 Verifikasi

```bash
# Cache driver
php artisan config:show cache.default
# harus file (atau redis)
php artisan tinker --execute 'Cache::put("test",123,60); echo Cache::get("test");'

# .htaccess headers
php artisan serve &
curl -I http://localhost:8000/build/assets/app-CUMh4Qnv.css
# harus ada: Cache-Control: public, max-age=31536000, immutable
#           Content-Encoding: gzip atau br (jika deflate on)
curl -I http://localhost:8000/kegiatan
# harus ada: Cache-Control: public, max-age=60 (jika pakai middleware)

curl -I http://localhost:8000/ | grep Cache-Control
# text/html harus no-cache

# Optimize
php artisan optimize
ls -lh bootstrap/cache/
# harus ada config.php, routes-v7.php, ...
php artisan route:list --except-vendor # tetap jalan meski cached
php artisan optimize:clear # clear untuk dev

vendor/bin/pint --dirty --format agent
php artisan test --compact
```

**Estimasi:** Cache hit 5ms→0.4ms, assets repeat 0 request, transfer -70% gzip, TTFB public list -60% via CDN cache, server CPU -20% via optimize.
