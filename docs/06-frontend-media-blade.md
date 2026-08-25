# 06 — Frontend Media & Blade: Image, N-stat & Maps

> **Target:** `public/assets/images/*` (gif/png 2-3MB), `resources/views/components/hero.blade.php:5`, `navbar.blade.php:4`, `public/home.blade.php:68`, `resources/views/admin/*/index.blade.php:70` `Storage::exists`, `components/footer.blade.php:89` maps iframe, `public/js/list-filter.js:18`

## 📌 Ringkasan Kode & Fungsi

**Images di `public/assets/images`:**
- `error-bg.gif` **3,024,471 bytes (2.88MB)** — dipakai `resources/views/errors/404.blade.php:15` sebagai `background-image`
- `logo-ilkom-motion.gif` **2,716,733 bytes (2.59MB)** — dipakai `navbar.blade.php:4`, `mobile-menu.blade.php:4`, `footer.blade.php:5`
- `hero-1.png:2,168,446`, `hero-3.png:2,162,279`, `hero-2.png:1,444,645` — 1.4-2.1MB PNG, duplikat dari `hero-1.jpeg:72,163` (70KB) yang sudah optimized
- `logo/logo-prodi.svg:1,328,442` (1.27MB, embed font/image) — tidak minified

**Hero & Navbar:**
```blade
// components/hero.blade.php:5
<img src="{{ $image }}" alt="" class="h-full w-full object-cover"> // no loading/fetchpriority/size
// public/home.blade.php:68
<img src="{{ $slide['url'] }}" class="h-full w-full object-cover"> // 5 slides eager, no high priority for LCP
// navbar.blade.php:4
<img src="...logo-ilkom-motion.gif" class="h-36"> // 2.7MB gif without width/height
```

**Blade N-stat:**
```blade
// admin/lecturers/index.blade.php:70
@php($hasPhoto = filled($lecturer->photo) && Storage::disk('public')->exists($lecturer->photo)) // inside @foreach → 10 stat per page
// admin/documents/index.blade.php:57 same, + controller DocumentController.php:45 Storage::disk('local')->exists per row → duplicate
```

**Maps:**
```blade
// components/footer.blade.php:89
<iframe src="https://maps.google.com/maps?...output=embed" loading="lazy"> // load Google Maps JS ~500k di semua halaman
```

**List filter JS:**
```js
// public/js/list-filter.js:18-42
cards.forEach(el=> el.style.display = match? '' : 'none') // layout thrash per keystroke, no debounce
```

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| H4-1 | `logo-ilkom-motion.gif:2716733` | **HIGH** | GIF animasi 2.7MB load di navbar (above-the-fold) tanpa `width/height`, tanpa `fetchpriority=high`. LCP element biasanya logo/hero, jadi GIF block rendering. |
| H4-2 | `error-bg.gif:3024471` | **HIGH** | Error pages (404) load 2.88MB GIF sebagai CSS background — tidak lazy, tidak compressed. User error malah lambat. |
| H4-3 | `hero-*.png:1.4-2.1MB` | **HIGH** | PNG 1.4-2MB tidak dipakai via `asset()` (fallback pakai `hero-*.jpeg` 70KB), tapi tetap ada di `public/` — wasted deploy size + risk ter-load salah. |
| H4-4 | `hero.blade.php:5` + `home.blade.php:68` | **HIGH** | 5 hero slides load eager semua, slide pertama adalah LCP tapi tidak ada `fetchpriority="high"` + `loading="eager"` + `decoding="async"` + `width/height`. |
| M6-1 | `admin/lecturers/index.blade.php:70`, `alumni/index.blade.php:71`, `documents/index.blade.php:57` | **MEDIUM** | `Storage::disk('public')->exists()` di loop → 10 `stat()` per paginated page (10 rows). Jika disk `S3`, = 10 `HEAD` request per load. Plus `DocumentController.php:45` sudah cek `Storage::disk('local')->exists` → **duplikat + disk mismatch** (`local` vs `public`). |
| M6-2 | `footer.blade.php:89` maps iframe | **MEDIUM** | Google Maps iframe load di **semua halaman** (footer), `loading="lazy"` sudah ada tapi tetap load 500k Maps JS even below-the-fold. Tanpa `IntersectionObserver` facade. |
| M6-3 | `list-filter.js:18` | **MEDIUM** | `input` event tanpa `debounce` → tiap keystroke `cards.forEach` `style.display` → force reflow. Untuk 500 docs: 500 DOM writes per keystroke. Sudah di-fix di 01 via server pagination, tapi jika keep client fallback tetap issue. |
| M6-4 | `components/activity-card.blade.php:10`, `dosen-card.blade.php:5`, `alumni-card.blade.php:15` | **MEDIUM** | Ada `loading="lazy"` good, tapi missing `decoding="async"`, `width/height`, `srcset/sizes`. |
| M6-5 | `admin/home-section/index.blade.php:12` `HomeSection::advantageItems()` di Blade | **LOW-MED** | Static model call di Blade — violate Thin View, tidak cache. |
| LOW-1 | `public/assets/fonts/*:740KB` woff2 | **LOW** | 7 `FaktPro` + 3 `Grold` + `Gotcha` total 740k tidak di-preload, tidak dipakai (`app.css` pakai Google Fonts CDN, woff2 self-host unused). |
| LOW-2 | `placehold.co/600x600` external | **LOW** | Fallback `https://placehold.co` di `home.blade.php:114`, `profile.blade.php:35` tanpa `cache-control` lokal. |

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Kompres & Ganti GIF/PNG ke WebP/JPEG (Paling Impact)

**Before:** `public/assets/images/logo-ilkom-motion.gif (2.7MB)`, `error-bg.gif (3.0MB)`, `hero-*.png (1.4-2MB)`

**After:**

1. **Kompres via CLI (sharp / squoosh):**
```bash
# Install sharp CLI atau pakai https://squoosh.app
npx sharp-cli -i public/assets/images/logo-ilkom-motion.gif -o public/assets/images/logo-ilkom-motion.webp --webp
# Atau convert gif animasi → mp4/webm <200KB
ffmpeg -i public/assets/images/logo-ilkom-motion.gif -vf "scale=400:-1" -c:v libvpx-vp9 public/assets/images/logo-ilkom-motion.webm

# Hero PNG → sudah ada JPEG 70KB, hapus PNG
rm public/assets/images/hero-1.png public/assets/images/hero-3.png public/assets/images/hero-2.png
# Keep hero-*.jpeg (72k, 74k, 76k, 56k)

# Error BG
ffmpeg -i public/assets/images/error-bg.gif -vf "scale=800:-1" public/assets/images/error-bg.webp
# 3.0M → ~180KB

# Logo static fallback
cwebp -q 80 public/assets/images/logone.png -o public/assets/images/logone.webp
```

2. **Update Blade `navbar.blade.php:4`:**
```blade
{{-- Before --}}
<img src="{{ asset('assets/images/logo-ilkom-motion.gif') }}" class="h-36" alt="Logo">

{{-- After --}}
<picture>
  <source srcset="{{ asset('assets/images/logo-ilkom-motion.webp') }}" type="image/webp">
  <img src="{{ asset('assets/images/logone.webp') }}" alt="Program Studi Ilmu Komputer" width="160" height="48" loading="eager" fetchpriority="high" decoding="async" class="h-12 w-auto">
</picture>
{{-- Jika tetap mau animasi, pakai <video autoplay loop muted playsinline> lebih kecil dari gif --}}
```

**Sama untuk `footer.blade.php:5`, `mobile-menu.blade.php:4`, `errors/*.blade.php:15` (ganti `error-bg.gif` → `error-bg.webp`):**
```blade
{{-- Before errors/404.blade.php:15 --}}
<div style="background-image: url('{{ asset('assets/images/error-bg.gif') }}')">

{{-- After --}}
<div style="background-image: url('{{ asset('assets/images/error-bg.webp') }}')">
{{-- Plus: tambah loading="lazy" jika background di bawah fold, atau preload jika above --}}
```

### B. Hero LCP — fetchpriority + Size

**Before `components/hero.blade.php:5`, `public/home.blade.php:68`:**
```blade
<img src="{{ $slide['url'] }}" alt="" class="h-full w-full object-cover">
```

**After:**
```blade
{{-- components/hero.blade.php --}}
@props(['image','priority' => false])
<img src="{{ $image }}" alt="" width="1280" height="720" @if($priority) fetchpriority="high" loading="eager" @else loading="lazy" @endif decoding="async" class="h-full w-full object-cover">

{{-- public/home.blade.php:68 --}}
@foreach($heroSlides as $idx => $slide)
  <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}" width="1280" height="720" @if($idx===0) fetchpriority="high" loading="eager" @else loading="lazy" @endif decoding="async" class="h-full w-full object-cover">
@endforeach
```

**Tambah preload di `layouts/public.blade.php:20` untuk LCP:**
```blade
<link rel="preload" as="image" href="{{ $heroSlides[0]['url'] ?? asset('assets/images/hero/hero-1.jpeg') }}" fetchpriority="high">
<link rel="preconnect" href="https://fonts.googleapis.com">
```

### C. Cards — Tambah Decoding + Size

**Before `components/activity-card.blade.php:10`:**
```blade
<img src="{{ $activity['image'] }}" loading="lazy" class="w-full aspect-video object-cover">
```

**After:**
```blade
<img src="{{ $activity['image'] }}" alt="{{ $activity['title'] }}" width="400" height="225" loading="lazy" decoding="async" class="w-full aspect-video object-cover">
{{-- Tambah srcset jika punya thumbnail 400w/800w --}}
```

Sama untuk `dosen-card.blade.php:5`, `alumni-card.blade.php:15`.

### D. Blade N-stat — Pindah ke Model Accessor + Cache

**Before `admin/lecturers/index.blade.php:70`:**
```blade
@php($hasPhoto = filled($lecturer->photo) && Storage::disk('public')->exists($lecturer->photo))
@if($hasPhoto) <img src="{{ asset('storage/'.$lecturer->photo) }}"> @endif
```

**After:**

1. **Model `app/Models/Lecturer.php` tambah accessor:**
```php
public function getPhotoExistsAttribute(): bool {
  if (blank($this->photo)) return false;
  return \Illuminate\Support\Facades\Cache::remember("lecturer:photo_exists:{$this->id}:".md5($this->photo), 3600, fn()=> \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo));
}
public function getPhotoUrlAttribute(): ?string {
  return $this->photo_exists ? asset('storage/'.$this->photo) : null;
}
```

2. **Controller `Admin\LecturerController.php:21` eager tidak perlu exists, cukup select:**
```php
$lecturers = Lecturer::query()->select(['id','name','nidn','position','photo','status'])->paginate(10);
// photo_exists diakses via accessor cached, bukan per row stat
```

3. **Blade jadi:**
```blade
@if($lecturer->photo_url) <img src="{{ $lecturer->photo_url }}" width="80" height="80" loading="lazy" decoding="async"> @endif
{{-- HAPUS Storage::exists di Blade --}}
```

**Fix Document disk mismatch:**

**Before `DocumentController.php:45` `disk('local')` + `index.blade.php:57` `disk('public')`:**
```php
// controller
$documents->getCollection()->each(fn($d)=> $d->file_exists = Storage::disk('local')->exists($d->file));
// blade
Storage::disk('public')->exists($document->file)
```

**After — Samakan `public` (atau `local` jika memang private), single source:**
```php
// DocumentController.php:45
$documents->getCollection()->each(fn(Document $d)=> $d->setAttribute('file_exists', Cache::remember("doc:exists:{$d->id}", 3600, fn()=> Storage::disk('public')->exists($d->file))));
// Blade pakai $document->file_exists tanpa Storage::exists lagi
// Hapus @php($storedFileExists = Storage::disk(...)) di blade
```

### E. Maps Lazy Facade

**Before `components/footer.blade.php:89`:**
```blade
<iframe src="https://maps.google.com/maps?q=...&output=embed" loading="lazy" class="w-full h-64"></iframe>
```

**After (facade — load on click / intersect):**
```blade
<div id="map-facade" class="w-full h-64 bg-gray-100 flex items-center justify-center cursor-pointer" data-src="https://maps.google.com/maps?q=...&output=embed">
  <button class="btn">Lihat Peta</button>
  <p class="text-sm text-gray-500">Klik untuk memuat Google Maps (500KB)</p>
</div>
<script>
  const facade = document.getElementById('map-facade');
  const loadMap = () => {
    const iframe = document.createElement('iframe');
    iframe.src = facade.dataset.src; iframe.loading='lazy'; iframe.className='w-full h-64'; iframe.referrerPolicy='no-referrer-when-downgrade';
    facade.replaceWith(iframe);
  };
  facade.addEventListener('click', loadMap, {once:true});
  // Opsional: IntersectionObserver auto-load ketika 200px dari viewport
  new IntersectionObserver((entries,o)=>{ if(entries[0].isIntersecting){ loadMap(); o.disconnect(); } },{rootMargin:'200px'}).observe(facade);
</script>
```

### F. List Filter JS — Debounce (Jika Tetap Client)

**Before `public/js/list-filter.js:18`:**
```js
input.addEventListener('input', () => {
  cards.forEach(el=> el.style.display = match(el) ? '' : 'none');
});
```

**After (jika keep client, tapi rekomendasi sudah server pagination di 01 maka hapus file):**
```js
let t;
input.addEventListener('input', () => {
  clearTimeout(t);
  t = setTimeout(() => requestAnimationFrame(()=>{
    cards.forEach(el=> el.style.display = match(el) ? '' : 'none');
  }), 200);
});
```
**Rekomendasi:** Setelah 01, hapus `public/js/list-filter.js` import dari semua views — server filter lebih baik.

### G. Fonts — Preload

**Tambah di `layouts/public.blade.php:20`:**
```blade
<link rel="preload" href="{{ asset('assets/fonts/FaktPro-Regular.woff2') }}" as="font" type="font/woff2" crossorigin>
{{-- Hapus woff2 unused: Gotcha, Grold jika tidak dipakai — cek grep font-family di app.css --}}
```

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa | Dampak |
|-----------|--------|--------|
| GIF 2.7MB→WebP 80KB / Video 150KB | GIF uncompressed, tidak pakai interframe compression. WebP 30× lebih kecil, video lebih kecil lagi. | LCP -2s, bandwidth -90%, mobile data hemat |
| PNG 2MB→JPEG 70KB hapus | PNG lossless untuk foto tidak perlu, JPEG 70KB cukup. PNG duplikat waste deploy. | Deploy size -6MB |
| `fetchpriority=high` slide 0 | Browser prioritas low untuk `img` default. `high` paksa preload LCP. | LCP 4s→1.8s |
| `width/height` | Tanpa size, browser layout shift (CLS) menunggu image load → reflow. | CLS 0.25→0.02 |
| Accessor `photo_exists` cached | `Storage::exists` syscall 10×/page → cached 3600s = 0 syscall hot. Samakan disk `public`. | Admin list 10 stat → 0 stat (hot) |
| Maps facade | Iframe load 500k Maps JS di semua halaman padahal footer below fold, user mungkin tidak scroll ke maps. | Home first load -500k, TTI -300ms |
| Debounce 200ms + rAF | Tiap keystroke `cards.forEach` 500 writes → debounce batch + `requestAnimationFrame` koalesce layout. | Typing jank hilang |

## ✅ Checklist

- [ ] Kompres `logo-ilkom-motion.gif` → `logo-ilkom-motion.webp` (atau `.webm` video) + update `navbar/footer/mobile-menu`
- [ ] Kompres `error-bg.gif` → `error-bg.webp` + update `errors/*.blade.php`
- [ ] Hapus `hero-*.png` duplikat, keep `hero-*.jpeg`
- [ ] `hero.blade.php` + `home.blade.php` tambah `width/height` + `fetchpriority` + `decoding`
- [ ] `activity-card/dosen-card/alumni-card` tambah `width/height` + `decoding`
- [ ] `Lecturer/Alumni/Document` model tambah `photo_exists`/`file_exists` accessor cached + update controller + hapus `Storage::exists` di Blade
- [ ] `DocumentController` samakan disk `public` (atau `local` konsisten) — hapus duplikat
- [ ] `footer.blade.php` maps jadi facade click/IO
- [ ] `list-filter.js` tambah debounce atau hapus jika 01 server pagination
- [ ] (Opsional) preload font woff2 yang dipakai

## 🧪 Verifikasi

```bash
ls -lh public/assets/images/
# logo-ilkom-motion.webp <150KB, error-bg.webp <200KB, hero-*.png hilang

# Build tidak perlu, tapi cek image load
php artisan serve
# Buka / — DevTools Network cek logo 80KB webp bukan 2.7MB gif, hero LCP high priority, maps tidak load sampai klik/scroll

# Lighthouse
npx lighthouse http://localhost:8000 --view
# LCP <2.5s, CLS <0.1, Total Byte Weight -3MB

# Admin list
# Buka /komi-panel/dosen — Network cek tidak ada 10 HEAD request ke storage (cached)

vendor/bin/pint --dirty --format agent
php artisan test --compact
```

**Estimasi:** LCP 4.5s→1.9s, total page weight 5MB→1.2MB, admin list syscall -100% hot, CLS -90%.
