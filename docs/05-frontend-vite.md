# 05 — Frontend Vite: Bundle, Hash & Defer

> **Target:** `vite.config.js:5` (22 line), `resources/js/admin.js:1` (59 line), `resources/js/app.js:1` (145 line), `resources/views/layouts/public.blade.php:23`, `public/js/app.js:5754` duplikat, `public/build/manifest.json`

## 📌 Ringkasan Kode & Fungsi

**vite.config.js saat ini:**
```js
// vite.config.js:10
laravel({ input: ['resources/css/app.css','resources/css/admin.css','resources/js/app.js','resources/js/admin.js'], refresh:true })
```
→ 4 entry, tidak ada `build.rollupOptions`, tidak ada `manualChunks`, tidak ada `assetsInlineLimit`, `cssCodeSplit`, `sourcemap:false`.

**resources/js/admin.js:**
```js
import Alpine from 'alpinejs';          // 3.16, global
import ApexCharts from 'apexcharts';    // 6.8, ~450k min
import flatpickr from 'flatpickr';      // 4.6
import 'flatpickr/dist/flatpickr.min.css'; // CSS via JS
window.ApexCharts = ApexCharts; // eager
// + dynamic import chart-1/2/3 yang cuma wrapper 500 bytes tapi sudah load apexcharts di main
```

**Build output sekarang `public/build/assets/`:**
- `admin-DOC2Oh_H.js` **969,247 bytes** (946KB, ~280k gzip) — 1 chunk besar
- `app-BJeq2Kcr.js` 3,774 bytes — kecil
- `app-CUMh4Qnv.css` 91,331 bytes, `admin-BYWWLcDG.css` 89,179 + `admin-CksuuEqD.css` 15,738

**Layout public:**
```blade
// layouts/public.blade.php:23
@vite('resources/css/app.css') // cuma CSS
// JS tidak via Vite, tapi di tiap public view:
// public/home.blade.php:7 <script src="{{ asset('js/app.js') }}"></script>
// public/activities/index.blade.php:6 sama
```
→ `public/js/app.js` (5,754 bytes) adalah **copy static** dari `resources/js/app.js` (identik) tapi **tanpa hash manifest** (`app-BJeq2Kcr.js`). Browser cache selamanya stale after deploy.

**Layout admin:**
```blade
// layouts/admin.blade.php:12
@vite(['resources/css/admin.css','resources/js/admin.js']) // benar
<script src="{{ asset('js/quill-init.js') }}"></script> // static 1,811 bytes tanpa hash
@once: <link cdn quill.snow.css + quill.js> // load di semua admin pages padahal cuma _form butuh
```

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| H5-1 | `resources/js/admin.js:1-8` → `admin-DOC2Oh_H.js:969247` | **HIGH** | `ApexCharts` + `flatpickr` + `Alpine` import **eager** padahal `apexcharts` cuma dipakai dashboard charts (`resources/js/components/chart-*.js` fetch + `new ApexCharts`). `flatpickr` cuma di form CRUD. Eager = semua admin pages (list, login) load 450k sia-sia. Tidak ada `manualChunks` → 1 JS besar. |
| M4-1 | `layouts/public.blade.php:23` + `public/home.blade.php:7` | **HIGH** | CSS via Vite hash, JS via `asset()` statis → `manifest.json` punya `app-BJeq2Kcr.js` tapi tidak dipakai (dead entry). Duplikat code `resources/js/app.js` vs `public/js/app.js` identik — maintenance drift risk. |
| M5-2 | `vite.config.js:5` no `build` config | **MEDIUM** | Tidak ada `manualChunks`, `sourcemap:false`, `cssCodeSplit:true`, `assetsInlineLimit`, `chunkSizeWarningLimit`. Tailwind v4 JIT scan semua file karena `app.css` tidak ada `@source` (admin.css ada). |
| M5-3 | `layouts/admin.blade.php:15` cdn quill | **MEDIUM** | `<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css">` + JS tanpa `integrity`/`crossorigin`/`defer`, load di **semua admin pages** padahal cuma `admin/activities/_form.blade.php:59` & `admin/pages/_form` butuh. Blocking render. |
| M5-4 | `public/build/manifest.json:27` `chart-*.js 554b` | **MEDIUM** | `chart-1/2/3.js` dynamicImport tapi tetap kecil karena `apexcharts` sudah di main chunk — code-splitting gagal. |
| M5-5 | `public/documents.blade.php:6` cdn `jszip`+`docx-preview` di `@push('head')` | **MEDIUM** | `<script src="https://cdn.../jszip..."></script>` tanpa `defer/async` di `<head>` → 2 blocking roundtrips di `/dokumen` (head). |
| LOW-1 | `resources/css/app.css:718` tailwind | **LOW** | `app.css` 718 line custom `.btn, .rich-text` tanpa purge fine-tune, load di semua public pages. |

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Vite Config — manualChunks + Hash Konsisten

**Before `vite.config.js:5`:**
```js
export default defineConfig({
  server:{host:'localhost',port:5173},
  plugins:[laravel({input:['resources/css/app.css','resources/css/admin.css','resources/js/app.js','resources/js/admin.js'],refresh:true}),tailwindcss()]
});
```

**After:**
```js
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  server:{host:'localhost',port:5173},
  plugins:[
    laravel({
      input:['resources/css/app.css','resources/css/admin.css','resources/js/app.js','resources/js/admin.js'],
      refresh:true,
    }),
    tailwindcss(),
  ],
  build:{
    sourcemap:false,
    cssCodeSplit:true,
    assetsInlineLimit:4096,
    chunkSizeWarningLimit:500,
    rollupOptions:{
      output:{
        manualChunks:{
          vendor: ['alpinejs'],
          charts: ['apexcharts'],
          picker: ['flatpickr'],
        },
      },
    },
  },
});
```

**Plus `resources/css/app.css:1` tambah `@source` biar JIT cepat (mirip admin):**
```css
@import "tailwindcss";
@source "../views/**/*.blade.php";
@source "../js/**/*.js";
/* hapus scan node_modules jika tidak perlu */
```

### B. Admin.js — Dynamic Import (Lazy)

**Before `resources/js/admin.js:1`:**
```js
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
Alpine.start();
```

**After:**
```js
// resources/js/admin.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// Lazy load heavy deps only when needed
// apexcharts hanya di dashboard
if (document.querySelector('[data-chart]') || document.querySelector('#chartOne')) {
  import('apexcharts').then(({default: ApexCharts}) => { window.ApexCharts = ApexCharts; });
}
// flatpickr hanya di form dengan .js-datepicker
if (document.querySelector('.js-datepicker')) {
  Promise.all([import('flatpickr'), import('flatpickr/dist/flatpickr.min.css')]);
}
// Atau lebih granular: import('./components/chart-1') sudah dynamic, jadi apexcharts tidak perlu di main
```

**Update `resources/js/components/chart-1.js` (contoh):**
```js
// resources/js/components/chart-1.js
export default async function initChart1(el){
  const [{default: ApexCharts}] = await Promise.all([import('apexcharts')]);
  const data = await fetch(el.dataset.url).then(r=>r.json());
  new ApexCharts(el, data).render();
}
```

### C. Layout Public — Pakai Vite Hash, Hapus Duplikat

**Before `layouts/public.blade.php:23`:**
```blade
@vite('resources/css/app.css')
@stack('head')
<!-- JS di tiap view: <script src="{{ asset('js/app.js') }}"></script> -->
```

**After:**
```blade
// layouts/public.blade.php
@vite(['resources/css/app.css','resources/js/app.js'])
@stack('head')
```
**Hapus di semua public views:**
```blade
{{-- HAPUS di public/home.blade.php:7, activities/index.blade.php:6, lecturers.blade.php:6, alumni.blade.php:6, documents.blade.php:11, profile.blade.php:7, contact.blade.php:6 --}}
{{-- <script src="{{ asset('js/app.js') }}"></script> --}}
{{-- Diganti via layout @vite di atas, jadi single hashed file app-BJeq2Kcr.js dengan Cache-Control immutable --}}
```
**Hapus file `public/js/app.js` duplikat** (atau keep 1 versi sebagai fallback, tapi gitignore) — source of truth `resources/js/app.js`.

**Juga `public/js/list-filter.js`:** jika sudah pagination server (01), file tidak dipakai lagi — hapus `asset('js/list-filter.js')` dari views. Jika tetap butuh, pindah ke `@vite` entry.

### D. Layout Admin — Defer & Conditional CDN

**Before `layouts/admin.blade.php:14`:**
```blade
@once
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
@endonce
@vite(['resources/css/admin.css','resources/js/admin.js'])
<script src="{{ asset('js/quill-init.js') }}"></script>
```

**After:**
```blade
@vite(['resources/css/admin.css','resources/js/admin.js'])
@stack('styles')
@stack('scripts')

{{-- Pindah quill CDN ke stack yang di-push hanya di _form pages --}}
{{-- di resources/views/admin/activities/_form.blade.php: --}}
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" crossorigin="anonymous" integrity="...">
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js" defer crossorigin="anonymous"></script>
@vite('resources/js/quill-init.js') {{-- pindah quill-init.js ke resources/js/ + Vite hash --}}
@endpush
```

**Buat `resources/js/quill-init.js` (pindah dari `public/js/quill-init.js:1811`):**
```js
// resources/js/quill-init.js — sudah ada, tinggal import via Vite
```

### E. Documents Page — Defer CDN

**Before `public/documents.blade.php:6`:**
```blade
@push('head')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js"></script>
@endpush
```

**After:**
```blade
@push('head')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js" defer crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js" defer crossorigin="anonymous"></script>
@endpush
{{-- Atau lebih baik: npm install jszip docx-preview lalu import via Vite --}}
```

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa | Dampak |
|-----------|--------|--------|
| `manualChunks: {vendor, charts, picker}` | Pisah 450k `apexcharts` + `flatpickr` dari `admin.js` → browser cache vendor terpisah, admin list (tanpa chart) tidak load 450k. `chunkSizeWarningLimit` ingatkan jika chunk >500k. | admin.js 946KB → ~180k (main) + 450k (charts chunk, cached) → first load -70% untuk non-dashboard |
| Dynamic `import('apexcharts')` | Eager `import` bundle ke main. Dynamic → load hanya jika `[data-chart]` ada. | Dashboard load chart async, non-dashboard 0kb charts |
| `@vite(['css','js'])` di layout | `asset('js/app.js')` statis tanpa hash → stale. `@vite` pakai `manifest.json` hash `app-BJeq2Kcr.js` + `immutable` header (lihat 07). | Cache hit 1 tahun, no stale |
| Hapus `public/js/app.js` duplikat | 1 source of truth `resources/js/app.js` → no drift. | Maintainability |
| `defer` + `crossorigin` CDN | Tanpa `defer`, parser blocking di `<head>`. `defer` load async, execute after parse. `integrity` prevent tamper. | TTI -200ms di `/dokumen` |
| Quill CDN pindah ke `@push` | Load 2 file CDN di semua admin pages (login, dashboard) padahal cuma 2 form pages butuh → waste 100KB. | Admin non-form -100KB |

**Risiko & Mitigasi:**
- Dynamic import bisa flash of no chart → tambah skeleton `loading` di `[data-chart]` container.
- Hapus `public/js/app.js` → jika user akses langsung `/js/app.js` 404 → mitigasi keep file tapi `redirect` atau `symlink` sementara 1 versi.
- `@source` di `app.css` salah path → JIT tidak generate class → test `npm run build` + cek `app-CUMh4Qnv.css` masih 91KB (tidak 0).

## ✅ Checklist

- [ ] `vite.config.js` tambah `build.{sourcemap,cssCodeSplit,assetsInlineLimit,rollupOptions.manualChunks}` seperti di atas
- [ ] `resources/css/app.css` tambah `@source` untuk JIT
- [ ] `resources/js/admin.js` ubah eager `ApexCharts`/`flatpickr` jadi dynamic `import()` + `if querySelector`
- [ ] `resources/js/components/chart-*.js` update ke `import('apexcharts')` async
- [ ] `layouts/public.blade.php:23` ganti jadi `@vite(['resources/css/app.css','resources/js/app.js'])` — hapus `@vite('css')` single
- [ ] Hapus `<script src="{{ asset('js/app.js') }}">` dari 7 public views + hapus `<script src="{{ asset('js/list-filter.js') }}">` (jika pagination server)
- [ ] (Opsional) `public/js/app.js` hapus atau keep symlink
- [ ] `layouts/admin.blade.php` pindah quill CDN ke `@push` di `_form.blade.php` + `defer` + `integrity`
- [ ] `resources/js/quill-init.js` pindah dari `public/js/` ke `resources/js/` + `@vite`
- [ ] `public/documents.blade.php` tambah `defer` ke jszip/docx-preview
- [ ] `npm run build` cek output

## 🧪 Verifikasi

```bash
npm run build
ls -lh public/build/assets/
# Expected setelah fix:
# admin-*.js ~180KB (main) + charts-*.js ~450KB + picker-*.js ~30KB + vendor-*.js ~40KB
# bukan 1 file 946KB

cat public/build/manifest.json | grep -E "admin|app|chart"
# cek manualChunks ada, app.js ada hash

# Test di browser
php artisan serve
# Buka / — view source cek <script type="module" src="/build/assets/app-*.js"> ada hash, bukan /js/app.js
# Buka /komi-panel/login — Network tab cek tidak load charts-*.js (hanya admin + vendor)
# Buka /komi-panel/dashboard — cek charts-*.js load lazy
# Buka /dokumen — cek jszip defer tidak blocking

# Lighthouse setelah
npx lighthouse http://localhost:8000 --view
# Total Blocking Time turun 200-400ms

vendor/bin/pint --dirty --format agent
php artisan test --compact
```

**Estimasi:** Admin first load 946KB → 220KB (non-dashboard), public JS cached immutable, TTI -30%.
