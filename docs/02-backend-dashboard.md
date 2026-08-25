# 02 — Backend Dashboard: 18 Query Loop → 1 GROUP BY

> **Target:** `app/Http/Controllers/Admin/DashboardController.php:251` `chartActivityMonthly()`, `290` `chartCombinedMonthly()`, `121` `countsByStatus()`, `133` `latestContent()`, `219` `publicReadiness()`

## 📌 Ringkasan Kode & Fungsi

`DashboardController::index()` (`314 line`) render `/komi-panel/dashboard` untuk admin. Data yang ditampilkan:

- **Summary cards** (`30-74`): 4 `countsByStatus()` → `SELECT status, COUNT(*) GROUP BY status` untuk Activity, Lecturer, Document, Alumni.
- **Status cards** (`76-104`): agregasi dari counts di atas (no query).
- **Latest content** (`133-187`): 4× `latest('updated_at')->limit(5)->get(['title/status/updated_at'])` → ambil 5 terbaru per model, concat + sort PHP `take(6)`.
- **Public readiness** (`219-234`): 4× `exists()` cek `HomeSection`, `ProgramProfile`, `Contact map_embed`, `SiteSetting journal_url`.
- **Chart activity monthly** (`251-265`): `collect(range(5,0))->map(fn=>$d => Activity::whereYear/whereMonth->count())` → **6 query**.
- **Chart combined monthly** (`290-313`): sama untuk Activity + Alumni → **12 query** (6×2).

**Total sekarang:** `4 (counts) + 4 (latest) + 4 (readiness) + 6 (chartMonthly) + 12 (combined) = ~30 query` per load dashboard, **tanpa cache**.

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| H2-1 | `DashboardController.php:257-261` | **HIGH** | `whereYear('created_at', $y)->whereMonth('created_at', $m)->count()` di loop 6×. Tiap iterasi `SELECT COUNT(*) WHERE YEAR(created_at)=? AND MONTH(created_at)=?` → tidak bisa pakai index `created_at` range secara efisien (function `YEAR()` kill index). 6 roundtrip. |
| H2-2 | `290-304` | **HIGH** | Sama untuk `chartCombinedMonthly()` → 12 query (6 Activity + 6 Alumni). Total chart = 18 query. Jika dashboard dibuka 100×/hari → 1800 query sia-sia. |
| M2-1 | `121-128` `countsByStatus()` | **MEDIUM** | Sudah benar `selectRaw + groupBy`, tapi dipanggil 4× tanpa cache. Tiap load dashboard 4 query padahal status jarang berubah (hanya saat admin publish). |
| M2-2 | `133-187` `latestContent()` | **MEDIUM** | 4× `limit(5)` + `concat->sortByDesc->take(6)` di PHP bukan SQL `UNION`. Sort 20 rows di PHP OK tapi 4 query tiap load tanpa cache. `select` hanya 3 kolom sudah benar (tidak `SELECT *`). |
| M2-3 | `219-234` `publicReadiness()` | **MEDIUM** | 4× `exists()` tiap load, uncached. `whereNotNull('map_embed')` tidak pakai index, tapi tabel singleton 1 row jadi murah — tetap waste. |
| LOW-1 | `251` `Carbon::now()->subMonths($i)` di loop | **LOW** | Tiap iterasi hit `Carbon::now()` 6× berbeda millis, label bisa skew. Seharusnya snapshot `now` sekali. |

**Dampak terukur:**
- Dashboard TTFB sekarang estimasi 800-1200ms (30 query × 15ms + PHP sort).
- DB `slow_query` akan log `YEAR(created_at)` full scan jika `activities` >10k.

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Chart Monthly — 1 GROUP BY Ganti 6 Loop

**Before `DashboardController.php:251`:**
```php
private function chartActivityMonthly(): array {
  $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
  $labels = $months->map(fn (Carbon $d) => $d->locale('id')->translatedFormat('M'))->toArray();
  $counts = $months->map(function (Carbon $d) {
    return Activity::query()->whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count();
  })->toArray();
  return compact('labels', 'counts');
}
private function chartCombinedMonthly(): array {
  $months = collect(range(5, 0))->map(fn ($i) => Carbon::now()->subMonths($i));
  $labels = $months->map(fn (Carbon $d) => $d->locale('id')->translatedFormat('M'))->toArray();
  $activityData = $months->map(fn (Carbon $d) => Activity::query()->whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count())->toArray();
  $alumniData = $months->map(fn (Carbon $d) => Alumni::query()->whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count())->toArray();
  return ['series'=>[['name'=>'Kegiatan','data'=>$activityData],['name'=>'Alumni','data'=>$alumniData]],'labels'=>$labels];
}
```

**After:**
```php
// app/Http/Controllers/Admin/DashboardController.php
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

private function chartActivityMonthly(): array
{
  return Cache::remember('dashboard:chart_activity_monthly', 300, function (): array {
    $now = Carbon::now();
    $months = collect(range(5, 0))->map(fn (int $i) => $now->copy()->subMonths($i));
    $labels = $months->map(fn (Carbon $d) => $d->locale('id')->translatedFormat('M'))->toArray();

    // 1 query GROUP BY, index-friendly range
    $from = $now->copy()->subMonths(5)->startOfMonth();
    $rows = Activity::query()
      ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
      ->where('created_at', '>=', $from)
      ->groupBy('ym')
      ->pluck('c', 'ym'); // ['2026-03'=>5, ...]

    $counts = $months->map(fn (Carbon $d) => (int) ($rows[$d->format('Y-m')] ?? 0))->toArray();

    return compact('labels', 'counts');
  });
}

private function chartCombinedMonthly(): array
{
  return Cache::remember('dashboard:chart_combined_monthly', 300, function (): array {
    $now = Carbon::now();
    $months = collect(range(5, 0))->map(fn (int $i) => $now->copy()->subMonths($i));
    $labels = $months->map(fn (Carbon $d) => $d->locale('id')->translatedFormat('M'))->toArray();
    $from = $now->copy()->subMonths(5)->startOfMonth();

    $activityRows = Activity::query()
      ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
      ->where('created_at', '>=', $from)->groupBy('ym')->pluck('c', 'ym');
    $alumniRows = Alumni::query()
      ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as c")
      ->where('created_at', '>=', $from)->groupBy('ym')->pluck('c', 'ym');

    $activityData = $months->map(fn (Carbon $d) => (int) ($activityRows[$d->format('Y-m')] ?? 0))->toArray();
    $alumniData = $months->map(fn (Carbon $d) => (int) ($alumniRows[$d->format('Y-m')] ?? 0))->toArray();

    return [
      'series' => [['name' => 'Kegiatan', 'data' => $activityData], ['name' => 'Alumni', 'data' => $alumniData]],
      'labels' => $labels,
    ];
  });
}
```

**Poin:** Ganti `whereYear/whereMonth` (kill index) jadi `where('created_at','>=',$from)` + `GROUP BY ym` → pakai index `created_at` range scan, 1 query vs 6/12.

### B. Counts & Latest + Readiness — Cache

**Before `countsByStatus()` dipanggil 4× tanpa cache:**
```php
$activityCounts = $this->countsByStatus(Activity::class); // 4× tiap load
```

**After — Bungkus di `index()`:**
```php
public function index(): View
{
  $activityCounts = Cache::remember('dashboard:counts:activity', 300, fn() => $this->countsByStatus(Activity::class));
  $lecturerCounts = Cache::remember('dashboard:counts:lecturer', 300, fn() => $this->countsByStatus(Lecturer::class));
  $documentCounts = Cache::remember('dashboard:counts:document', 300, fn() => $this->countsByStatus(Document::class));
  $alumniCounts   = Cache::remember('dashboard:counts:alumni', 300, fn() => $this->countsByStatus(Alumni::class));

  return view('admin.dashboard', [
    'summaryCards' => ...,
    'latestContent' => Cache::remember('dashboard:latest_content', 300, fn() => $this->latestContent()),
    'publicReadiness' => Cache::remember('dashboard:readiness', 600, fn() => $this->publicReadiness()),
    'chartActivityMonthly' => $this->chartActivityMonthly(), // sudah cached di method
    'chartCombinedMonthly' => $this->chartCombinedMonthly(),
    'chartStatusDistribution' => $this->chartStatusDistribution($activityCounts, $lecturerCounts, $documentCounts, $alumniCounts),
  ]);
}
// Di Admin\ActivityController@store/update/destroy tambah:
// Cache::forget('dashboard:counts:activity'); Cache::forget('dashboard:latest_content'); Cache::forget('dashboard:chart_activity_monthly'); Cache::forget('dashboard:chart_combined_monthly');
// Sama untuk LecturerController, DocumentController, AlumniController
```

**Optimasi `latestContent()` — UNION (opsional, jika mau 1 query):**
```php
private function latestContent(): Collection
{
  // Tetap 4 query tapi cached 5 menit, sudah cukup. Jika mau 1 query:
  // Pakai DB::table + unionAll (lebih kompleks karena kolom name vs title).
  // Keep current 4× get(['title/status/updated_at']) sudah select spesifik, cuma tambah cache.
  $content = Activity::query()->latest('updated_at')->limit(5)->get(['title','status','updated_at'])->map(...);
  // ... concat 3 lainnya ->sortByDesc->take(6)
  return $content->concat(...)->sortByDesc('updated_at')->take(6)->values();
}
```

### C. Invalidation Terpusat (Trait atau Service)

Buat helper `app/Services/DashboardCache.php`:
```php
final class DashboardCache {
  public static function forgetAll(): void {
    Cache::forget('dashboard:counts:activity');
    Cache::forget('dashboard:counts:lecturer');
    Cache::forget('dashboard:counts:document');
    Cache::forget('dashboard:counts:alumni');
    Cache::forget('dashboard:latest_content');
    Cache::forget('dashboard:readiness');
    Cache::forget('dashboard:chart_activity_monthly');
    Cache::forget('dashboard:chart_combined_monthly');
  }
  public static function forgetActivity(): void {
    Cache::forget('dashboard:counts:activity');
    Cache::forget('dashboard:latest_content');
    Cache::forget('dashboard:chart_activity_monthly');
    Cache::forget('dashboard:chart_combined_monthly');
  }
}
```
Panggil di `ActivityController`, `AlumniController`, dll setelah `DB::transaction`.

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa | Dampak |
|-----------|--------|--------|
| `whereYear+whereMonth` → `where('created_at','>=',$from) + GROUP BY ym` | Function `YEAR()`/`MONTH()` prevent index use → full scan. Range `>=` pakai `index(created_at)` | 6/12 query → 1/2 query, `EXPLAIN` pakai `range` bukan `ALL` |
| `Cache::remember 300s` untuk semua chart/counts | Dashboard dilihat admin tiap menit, data tidak perlu real-time detik. | 30 query/load → ~2 query/5 menit (cache hit). Load 800ms→80ms |
| Snapshot `$now = Carbon::now()` sekali | `range(5,0)->map(fn=>now())` tiap iterasi beda millis, label bisa off 1 detik di boundary bulan | Konsistensi label |
| `pluck('c','ym')` + map di PHP | `pluck` sudah assoc `ym=>c`, mapping 6 bulan di PHP murah (O(6)) | No extra query per bulan |

**Risiko & Mitigasi:**
- Data chart delay 5 menit → acceptable untuk admin dashboard. Jika butuh instant setelah publish, `Cache::forget` di `store/update` sudah handle.
- `DATE_FORMAT` MySQL spesifik → OK karena DB utama MySQL (AGENTS.md). Jika test SQLite, fallback pakai `strftime`.
- Cache driver `database` lambat (lihat 07) → setelah 07 ganti ke `file`/`redis` akan lebih cepat lagi.

## ✅ Checklist

- [ ] `DashboardController.php:251` `chartActivityMonthly()` ganti ke `Cache::remember` + `where('created_at','>=')` + `groupBy`
- [ ] `DashboardController.php:290` `chartCombinedMonthly()` sama (2 Group By)
- [ ] `DashboardController.php:22` `index()` bungkus `countsByStatus`, `latestContent`, `publicReadiness` dengan `Cache::remember`
- [ ] Buat `app/Services/DashboardCache.php` helper forget
- [ ] Tambah `DashboardCache::forget*()` di `Admin\ActivityController`, `LecturerController`, `DocumentController`, `AlumniController` setelah CRUD + `HomeSectionController`, `ProgramProfileController` untuk readiness
- [ ] Import `Cache`, `Carbon` snapshot fix

## 🧪 Verifikasi

```bash
# Sebelum fix: hitung query
DB::enableQueryLog(); app(App\Http\Controllers\Admin\DashboardController::class)->index(); count(DB::getQueryLog()); // ~30

# Setelah fix (cache cold pertama): ~6-8 query (1 group + 4 counts + 1 latest + 1 readiness)
# Hit kedua (cache hit): ~0-1 query

php artisan tinker --execute '
  Cache::flush();
  $c = app(App\Http\Controllers\Admin\DashboardController::class);
  $r = (new ReflectionClass($c))->getMethod("chartActivityMonthly");
  $r->setAccessible(true);
  dump($r->invoke($c));
'

# EXPLAIN
EXPLAIN SELECT DATE_FORMAT(created_at, "%Y-%m") as ym, COUNT(*) as c FROM activities WHERE created_at >= "2026-03-01" GROUP BY ym;
# Harus Using where; Using index / range, bukan Using temporary filesort besar

vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=Dashboard
```

**Estimasi:** Dashboard 30 query → 2-4 query (cold) → 0 query (hot 5 menit), TTFB 900ms→120ms, DB load -90%.
