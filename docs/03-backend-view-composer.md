# 03 — Backend View Composer & Constructor Duplikasi

> **Target:** `app/Providers/AppServiceProvider.php:26` `View::composer('*')`, `app/Http/Controllers/PublicController.php:30` `__construct`, `app/Http/Middleware/SecurityHeaders.php:21` `file_exists(hot)`

## 📌 Ringkasan Kode & Fungsi

**AppServiceProvider:**
```php
// AppServiceProvider.php:26
View::composer('*', function (ViewInstance $view): void {
  $site = Cache::rememberForever('site_setting', fn()=> SiteSetting::query()->first()?->toArray());
  $v->with('site', is_array($site)? new SiteSetting($site): $this->defaultSite());
});
```
→ Dijalankan **untuk setiap view instance** yang di-render, termasuk partials (`@include('partials.admin.sidebar')`, `@include('partials.admin.flash')`, komponen `x-activity-card` di loop). Jika halaman render 10 views → 10× closure dipanggil.

**PublicController::__construct():**
```php
// PublicController.php:30
public function __construct(){
  $site = Cache::rememberForever('site_setting', fn()=> SiteSetting::query()->first()?->toArray());
  view()->share('site', $site); // duplikat logic AppServiceProvider
  $contact = Cache::rememberForever('contact_info', fn()=> Contact::query()->first()?->toArray());
  view()->share('contactInfo', $contact);
}
```
→ Dijalankan **untuk setiap request** yang masuk `PublicController` (semua routes `public.security` middleware group `routes/web.php:19`). Logic `site` duplikat dengan AppServiceProvider.

**SecurityHeaders:**
```php
// SecurityHeaders.php:21
file_exists(public_path('hot')) // cek tiap request
```

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| H3-1 | `AppServiceProvider.php:26` `View::composer('*')` | **HIGH** | `'*'` = wildcard semua view. Di `layouts/admin.blade.php:58` ada 3 `@include` + di `public/home.blade.php` ada loop 3 activities + 3 alumni + hero slides → total 15-20 view instances per request. Tiap instance hit `Cache::rememberForever` (meski hit cache tetap 20× `Cache::get`). Seharusnya cukup 1× per request. |
| H3-2 | `PublicController.php:30` `view()->share` duplikat | **HIGH** | `site` diambil 2×: sekali di `AppServiceProvider::boot()` (untuk semua view) + sekali di `__construct` (untuk PublicController). `contactInfo` juga tiap request. `SiteSetting::first()?->toArray()` = `SELECT *` tanpa `select()` (load `footer_academic_links` JSON + logo path) — overfetch untuk footer yang cuma butuh 4 kolom. |
| M3-2 | `AppServiceProvider.php:28` `SELECT *` | **MEDIUM** | `SiteSetting::first()` tanpa `select(['site_name','university_name','faculty_name','footer_text','journal_url','registration_url','logo','favicon'])` → load `footer_academic_links` JSON besar tiap composer call (meski cache hit, on miss load full). |
| M3-3 | `PublicController.php:42` `Contact::first()?->toArray()` | **MEDIUM** | Sama, `SELECT *` include `map_embed` TEXT (iframe Google Maps panjang) — hanya footer yang butuh, tapi di-share ke semua view. |
| LOW-1 | `SecurityHeaders.php:21` `file_exists(public_path('hot'))` | **LOW** | `file_exists` = syscall per request. Di production `hot` tidak ada, tapi tetap cek. Seharusnya pakai `Vite::isRunningHot()` yang sudah cache atau `app()->environment('local')`. |
| LOW-2 | `PublicController.php:35,45` `catch(\Throwable)` tanpa log | **LOW** | Jika DB down, fallback silent ke `defaultSite()` tanpa log → susah debug. |

**Verifikasi sekarang:**
```bash
# Hitung berapa kali View composer dipanggil per request
# Tambah di AppServiceProvider::boot(): \Log::info('composer called '.microtime(true));
# Buka / — cek logs ada berapa baris per request (expected 12-18)
```

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Scope View Composer + Single Source of Truth

**Before `AppServiceProvider.php:26`:**
```php
View::composer('*', function (ViewInstance $view): void {
  try { $site = Cache::rememberForever('site_setting', fn()=> SiteSetting::query()->first()?->toArray()); } catch(\Throwable){ $site = $this->defaultSite(); }
  $view->with('site', $site);
});
```

**After:**
```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Vite;

public function boot(): void
{
  // 1) Share site sekali per request, bukan per view instance
  // Pakai View::share + once() memoization, atau composer spesifik layouts
  View::composer(['layouts.public','layouts.admin','components.footer','components.navbar','components.topbar'], function (ViewInstance $view): void {
    $site = Cache::rememberForever('site_setting', function () {
      return SiteSetting::query()
        ->select(['site_name','university_name','faculty_name','footer_text','journal_url','registration_url','logo','favicon','footer_academic_links'])
        ->first()?->toArray();
    });
    $site = is_array($site) ? new SiteSetting($site) : $this->defaultSite();
    $view->with('site', $site);
  });

  // ATAU lebih efisien: sekali per request pakai View::share
  // View::share('site', Cache::rememberForever('site_setting', fn()=> ...));
  // Tapi butuh try/catch migrasi belum jalan → pakai closure above aman.
}
```

**Hapus duplikasi di PublicController:**

**Before `PublicController.php:30`:**
```php
public function __construct(){
  $site = Cache::rememberForever('site_setting', fn()=> SiteSetting::query()->first()?->toArray());
  view()->share('site', $site);
  $contact = Cache::rememberForever('contact_info', fn()=> Contact::query()->first()?->toArray());
  view()->share('contactInfo', $contact);
}
```

**After:**
```php
// app/Http/Controllers/PublicController.php
// HAPUS __construct() view()->share('site') — sudah di AppServiceProvider
// Hanya share contactInfo, tapi dengan select + cache + sekali per request

// Opsi 1: Pindah contactInfo juga ke AppServiceProvider (rekomendasi — single source)
 // Di AppServiceProvider::boot() tambah View::composer untuk contactInfo:
 // View::composer(['layouts.public','layouts.admin','components.footer'], fn($v)=> $v->with('contactInfo', Cache::rememberForever('contact_info', fn()=> Contact::query()->select(['address','email','phone','instagram','youtube','facebook','map_embed'])->first()?->toArray() ));

// Opsi 2: Jika tetap di PublicController, cache + select + memoize dengan once()
use Illuminate\Support\Onceable; // Laravel 11+ once()

public function __construct()
{
  // Hapus site, hanya contactInfo dengan select
  $contact = once(fn() => Cache::rememberForever('contact_info', fn () =>
    Contact::query()->select(['address','email','phone','instagram','youtube','facebook','map_embed'])->first()?->toArray()
  ));
  // once() pastikan cuma hit cache 1× per request meski controller di-instantiate multiple times (jika ada)
  view()->share('contactInfo', is_array($contact) ? new Contact($contact) : $this->defaultContact());
}
// Jika AppServiceProvider sudah handle contactInfo juga, HAPUS __construct sepenuhnya.
```

**Rekomendasi final: Hapus `__construct` sepenuhnya, pindah semua ke `AppServiceProvider`:**

```php
// app/Providers/AppServiceProvider.php final
public function boot(): void
{
  View::composer(['layouts.*','components.footer','components.navbar','components.topbar','components.mobile-menu'], function (ViewInstance $view): void {
    try {
      $site = Cache::rememberForever('site_setting', fn() => SiteSetting::query()
        ->select(['site_name','university_name','faculty_name','footer_text','journal_url','registration_url','logo','favicon','footer_academic_links'])
        ->first()?->toArray());
      $site = is_array($site) ? new SiteSetting($site) : $this->defaultSite();
    } catch (\Throwable $e) {
      report($e);
      $site = $this->defaultSite();
    }
    $view->with('site', $site);

    try {
      $contact = Cache::rememberForever('contact_info', fn() => Contact::query()
        ->select(['address','email','phone','instagram','youtube','facebook','map_embed'])
        ->first()?->toArray());
      $contact = is_array($contact) ? new Contact($contact) : $this->defaultContact();
    } catch (\Throwable $e) {
      report($e);
      $contact = $this->defaultContact();
    }
    $view->with('contactInfo', $contact);
  });
}
private function defaultContact(): Contact { /* pindah dari PublicController */ }
```

### B. Fix `SecurityHeaders.php:21`

**Before:**
```php
if (file_exists(public_path('hot'))) { /* vite HMR */ }
```

**After:**
```php
use Illuminate\Support\Facades\Vite;
// di handle():
if (app()->environment('local') && Vite::isRunningHot()) {
  // hot logic
}
// ATAU cache static:
private static ?bool $isHot = null;
private function isHot(): bool {
  return self::$isHot ??= file_exists(public_path('hot'));
}
```

### C. Tambah `Cache::forget` di Admin Controllers

Sudah di-handle di 01 & 02, tapi untuk site/contact:

```php
// app/Http/Controllers/Admin/SiteSettingController.php:update
Cache::forget('site_setting');
// app/Http/Controllers/Admin/ContactController.php:update
Cache::forget('contact_info');
Cache::forget('program_profile'); // untuk 01
```

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa | Dampak |
|-----------|--------|--------|
| `View::composer('*')` → `composer(['layouts.*',...])` | `'*'` jalan per view instance (15-20×). `layouts.*` + components yang butuh `site` cuma 4-5 view. | 20 cache hit → 4-5 hit, -75% |
| Hapus `__construct` duplikasi | `site` diambil 2× per request (Provider + Controller). `view()->share` di constructor legacy, seharusnya `View::composer` di Provider saja. | 1 SELECT on miss bukan 2, single source of truth |
| `select([...])` di `SiteSetting`/`Contact` | `SELECT *` load JSON `footer_academic_links` + `map_embed` TEXT panjang padahal footer cuma butuh 4 field di navbar. | On miss I/O -60%, cache payload kecil |
| `once(fn=>Cache::remember...)` | Jika controller di-resolve multiple times (misal route group), `once` memoize per request. | Guarantee 1 hit/request |
| `report($e)` di catch | Sebelumnya silent `catch(\Throwable)` tanpa log → DB error tidak terdeteksi. | Observabilitas |
| `Vite::isRunningHot()` | Laravel sudah cache `hot` file check, tidak syscall tiap request. | -1 syscall/request |

**Risiko & Mitigasi:**
- Scope `layouts.*` mungkin miss view yang butuh `$site` (misal `errors/404`) → mitgasi: tambah `'errors::*'` atau fallback `View::share` untuk error pages. Test semua halaman setelah ganti.
- Hapus `__construct` ubah lifecycle — `PublicController` child tidak lagi share `contactInfo` otomatis → pastikan `AppServiceProvider` share untuk semua public routes, test `/kontak` yang butuh `map_embed`.
- `select()` menghilangkan field → jika ada view yang butuh kolom lain (misal `logo`), pastikan kolom ada di `select`.

## ✅ Checklist

- [ ] `AppServiceProvider.php:26` ganti `'*'` jadi `['layouts.public','layouts.admin','components.footer','components.navbar','components.topbar','components.mobile-menu','errors::*']` + `select()` + `report($e)`
- [ ] Hapus `PublicController.php:30` `__construct` site duplikat, pindah `contactInfo` ke Provider atau keep dengan `once()` + `select()`
- [ ] `SecurityHeaders.php:21` ganti `file_exists` → `Vite::isRunningHot()` atau static cache
- [ ] Tambah `Cache::forget('site_setting'/'contact_info')` di `SiteSettingController`, `ContactController`
- [ ] Test manual: `/`, `/profil`, `/kontak`, `/komi-panel/dashboard` — `$site` & `$contactInfo` tetap ada

## 🧪 Verifikasi

```bash
# Cek view composer dipanggil berapa kali
php artisan tinker --execute '
  \Illuminate\Support\Facades\View::composer("*", fn($v)=>\Log::info("old wildcard"));
'
# Setelah fix, cek logs cuma 4-5 baris per request

# Benchmark
php artisan tinker --execute '
  $start=microtime(true);
  for($i=0;$i<100;$i++){ app(App\Providers\AppServiceProvider::class)->boot(); }
  echo microtime(true)-$start;
'

# Manual
php artisan serve
# Buka / — view source cari site_name, cek tidak error
# Buka /komi-panel/dashboard — cek sidebar site_name tetap muncul

vendor/bin/pint --dirty --format agent
php artisan test --compact
```

**Estimasi:** 20 cache hit → 4 hit, 2 SELECT on miss → 1 SELECT, TTFB -30-50ms per request, lebih maintainable.
