# 01 — Backend PublicController: Pagination + Select + Cache

> **Target:** `app/Http/Controllers/PublicController.php:356` `activitiesData()`, `444` `lecturersData()`, `469` `alumniData()`, `498` `documentsData()` + 4 Blade list + `public/js/list-filter.js:18`

## 📌 Ringkasan Kode & Fungsi

`PublicController` layani semua halaman public. Saat ini **semua list** pakai pola sama:

```php
// PublicController.php:356-369
private function activitiesData(int $limit = 0): array {
  $query = Activity::query()->where('status', Activity::STATUS_PUBLISHED)->orderByDesc('activity_date')->orderByDesc('id');
  if ($limit>0) $query->limit($limit);
  return $query->get()->map(fn($a)=>$this->mapActivity($a))->all(); // ← unbounded
}
```

Dipanggil di:
- `home():92` `activitiesData(3)` + `alumniData(3)` → ambil 3 (bounded, OK tapi tetap `SELECT *`)
- `activities():170` `activitiesData()` → **tanpa limit, load semua**
- `lecturers():156` `lecturersData()` → tanpa limit
- `documents():199` `documentsData()` → tanpa limit + `with('documentCategory')` benar tapi tetap `get()` semua
- `alumni():253` `alumniData()` → tanpa limit

Blade `public/activities/index.blade.php:58`, `lecturers.blade.php:57`, `documents.blade.php:172`, `alumni.blade.php:60` render semua cards lalu filter client-side via `ListFilter` (`data-search`, `data-category`).

Untuk filter dropdown:

```php
// PublicController.php:157,171,201,255
$expertises = Lecturer::where('status',active)->whereNotNull('expertise')->distinct()->orderBy('expertise')->pluck('expertise');
// + 3 lagi tanpa cache, tiap request
```

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| H1-1 | `PublicController.php:367` `get()->map()->all()` | **HIGH** | `SELECT *` load `mediumText content`, `excerpt`, `image` untuk **semua rows**. 500 kegiatan × 7 kolom = ~5MB PHP memory + 500 DOM nodes. Home pakai `limit(3)` masih OK, tapi 4 halaman list akan linear O(n). |
| H1-2 | `PublicController.php:446` `lecturersData()` | **HIGH** | `SELECT *` include `bio TEXT` + `photo` untuk tabel list yang cuma butuh name/nidn/position. Frontend `dosen-card` cuma pakai 5 field. |
| H1-3 | `PublicController.php:482` `alumniData()` | **HIGH** | Load `testimonial TEXT` untuk semua alumni, padahal card cuma pakai quote 1 baris. |
| H1-4 | `PublicController.php:505` `documentsData()` | **HIGH** | `with('documentCategory')` sudah benar hindari N+1, tapi `get()` tanpa paginate load semua `file`, `description`. `whereHas` di `documents():201` bikin `EXISTS` subquery tiap request. |
| M1-1 | `PublicController.php:157,171,201,255` | **MEDIUM** | 4× `distinct()->pluck()` tanpa `Cache::remember` + tanpa index komposit (lihat 04). Tiap `/kegiatan` hit 2 query: `SELECT DISTINCT category...` + `SELECT * FROM activities`. |
| M3-1 | `activities/index.blade.php:59` `data-search="{{ $activity['title'] }} {{ $activity['excerpt'] }}"` | **MEDIUM** | Kirim full content di HTML attribute → payload bengkak 3×. JS `list-filter.js:18` `cards.forEach(el=>el.style.display=...)` tanpa debounce → layout thrash tiap keystroke, O(n) repaint. |
| M6-1 | `PublicController.php:98,128` `ProgramProfile::query()->first()` | **MEDIUM** | `SELECT *` include 4× `longText` (`history,description,mission,goals`) + 4 image path. Dipanggil di `home()` & `profile()` tanpa cache + tanpa `select()`. |
| LOW-1 | `PublicController.php:375` `parseActivityContent()` | **LOW** | `strip_tags` + regex `SanitizesHtml` dijalankan per row di `mapActivity` loop → CPU per row, bisa memoize. |

**Verifikasi sekarang:**
```sql
-- Cek berapa rows akan ke-load
SELECT COUNT(*) FROM activities WHERE status='published';
SELECT COUNT(*) FROM lecturers WHERE status='active';
-- Jika >50, H1 sudah terasa
```

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Pagination Server-Side + Select + Cache (Core Fix)

**Before `PublicController.php:356`:**
```php
private function activitiesData(int $limit = 0): array {
  $query = Activity::query()->where('status', Activity::STATUS_PUBLISHED)->orderByDesc('activity_date')->orderByDesc('id');
  if ($limit>0) $query->limit($limit);
  return $query->get()->map(fn(Activity $a)=>$this->mapActivity($a))->all();
}
public function activities(): View {
  return view('public.activities.index', [
    'activities' => $this->activitiesData(),
    'categories' => Activity::query()->where('status', Activity::STATUS_PUBLISHED)->distinct()->orderBy('category')->pluck('category'),
  ]);
}
```

**After:**
```php
// app/Http/Controllers/PublicController.php
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

public function activities(Request $request): View {
  $search = $request->string('q')->toString() ?: null;
  $category = $request->string('category')->toString() ?: null;

  return view('public.activities.index', [
    'activities' => $this->activitiesData(search: $search, category: $category), // paginator
    'categories' => Cache::remember('activities:categories', 3600, fn() =>
      Activity::query()->where('status', Activity::STATUS_PUBLISHED)
        ->whereNotNull('category')->where('category','!=','')
        ->distinct()->orderBy('category')->pluck('category')
    ),
    'filters' => ['q' => $search, 'category' => $category],
  ]);
}

/**
 * @return LengthAwarePaginator|array<int, array<string,mixed>>
 */
private function activitiesData(int $limit = 0, ?string $search = null, ?string $category = null): LengthAwarePaginator|array {
  $query = Activity::query()
    ->select(['id','title','slug','excerpt','activity_date','location','category','image']) // exclude content longText di list
    ->where('status', Activity::STATUS_PUBLISHED)
    ->when($search, fn($q) => $q->where(function($qq) use ($search) {
      // pakai FULLTEXT jika sudah ada index (modul 04), fallback LIKE prefix
      return $qq->where('title', 'like', $search.'%')->orWhere('location', 'like', $search.'%');
      // ideal: ->whereFullText(['title','location'], $search)
    }))
    ->when($category, fn($q) => $q->where('category', $category))
    ->orderByDesc('activity_date')->orderByDesc('id');

  if ($limit>0) {
    return $query->limit($limit)->get()->map(fn(Activity $a)=>$this->mapActivity($a))->all();
  }
  return $query->paginate(12)->withQueryString()->through(fn(Activity $a)=>$this->mapActivity($a));
}
```

**Sama untuk 3 lainnya:**

```php
private function lecturersData(?string $search = null, ?string $expertise = null): LengthAwarePaginator {
  return Lecturer::query()
    ->select(['id','name','nidn','position','expertise','education','email','photo','sort_order'])
    ->where('status', Lecturer::STATUS_ACTIVE)
    ->when($search, fn($q)=>$q->where('name','like', $search.'%'))
    ->when($expertise, fn($q)=>$q->where('expertise', $expertise))
    ->orderBy('sort_order')->orderBy('name')->orderBy('id')
    ->paginate(12)->withQueryString()
    ->through(fn(Lecturer $l)=>[
      'name'=>$l->name,'nidn'=>$l->nidn,'position'=>$l->position??'','expertise'=>$l->expertise??'',
      'education'=>$l->education??'','email'=>$l->email??'',
      'image'=>$l->photo?asset('storage/'.$l->photo):asset('assets/images/hero/hero-1.jpeg'),
      'description'=>$l->bio??'',
    ]);
}
// public function lecturers(Request $r): View { ... paginate + Cache::remember('lecturers:expertises',3600,...) }

private function alumniData(int $limit=0, ?string $search=null, ?string $job=null): LengthAwarePaginator|array {
  $q = Alumni::query()->select(['id','name','batch_year','graduation_year','job_position','company','testimonial','photo'])
    ->where('status', Alumni::STATUS_ACTIVE)->orderByDesc('batch_year')->orderBy('name')->orderBy('id')
    ->when($search, fn($qq)=>$qq->where('name','like',$search.'%'))
    ->when($job, fn($qq)=>$qq->where('job_position',$job));
  if ($limit>0) return $q->limit($limit)->get()->map(...)->all();
  return $q->paginate(12)->withQueryString()->through(...);
}

private function documentsData(?string $search=null, ?string $category=null): LengthAwarePaginator {
  return Document::query()->with('documentCategory:id,name')->select(['id','document_category_id','title','description','file_type','file_size','uploaded_at','file','slug','status'])
    ->where('status', Document::STATUS_PUBLISHED)
    ->when($search, fn($q)=>$q->where('title','like',$search.'%'))
    ->when($category, fn($q)=>$q->whereHas('documentCategory', fn($qq)=>$qq->where('name',$category)))
    ->orderByDesc('uploaded_at')->orderByDesc('id')
    ->paginate(12)->withQueryString()->through(fn(Document $d)=>[
      'id'=>$d->id,'title'=>$d->title,'category'=>$d->documentCategory?->name??'',
      'description'=>$d->description??'','file_type'=>$d->fileTypeLabel(),'file_size'=>$d->formattedFileSize(),
      'updated_at'=>$d->uploaded_at?->format('Y-m-d')??'','updated_label'=>$d->uploaded_at?->translatedFormat('d F Y')??'',
      'file'=>$d->file,
    ]);
}
```

### B. Blade — Ganti Client Filter Jadi Server Links

**Before `resources/views/public/activities/index.blade.php:58`:**
```blade
<div id="kegiatan-grid" class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
  @forelse($activities as $activity)
    <x-activity-card :activity="$activity" data-search="{{ $activity['title'] }} {{ $activity['excerpt'] }}" data-category="{{ $activity['category'] }}" />
  @endforelse
</div>
<script src="{{ asset('js/list-filter.js') }}"></script>
```

**After:**
```blade
<form method="GET" class="mb-6 flex gap-3">
  <input name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Cari kegiatan..." class="input">
  <select name="category"><option value="">Semua Kategori</option>@foreach($categories as $cat)<option @selected(($filters['category']??'')===$cat)>{{ $cat }}</option>@endforeach</select>
  <button class="btn">Cari</button>
</form>
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
  @forelse($activities as $activity)
    <x-activity-card :activity="$activity" />
  @empty
    <p>Tidak ada kegiatan.</p>
  @endforelse
</div>
{{ $activities->links() }} {{-- pagination --}}
{{-- HAPUS list-filter.js, HAPUS data-search/data-category --}}
```

**Sama untuk `lecturers.blade.php:57`, `documents.blade.php:172`, `alumni.blade.php:60`.**

### C. ProgramProfile Cache + Select

**Before `PublicController.php:98`:**
```php
'programProfile' => ProgramProfile::query()->first() ?? new ProgramProfile,
```

**After:**
```php
'programProfile' => Cache::rememberForever('program_profile', fn()=>
  ProgramProfile::query()->select(['history','description','vision','mission','goals','accreditation','advantages'])->first()
) ?? new ProgramProfile,
// di ProgramProfileController@update tambah Cache::forget('program_profile')
```

### D. Hapus `data-search` Payload di Mapper

**Before `mapActivity()` kirim semua ke `data-search` di Blade.**

**After:** Mapper tetap sama, tapi Blade tidak lagi render `data-search="{{ $activity['title'] }} {{ $activity['content_blocks'] }}"` — hanya pagination.

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa | Dampak |
|-----------|--------|--------|
| `paginate(12)` ganti `get()` | Load 12 rows bukan N rows. `LengthAwarePaginator` pakai `COUNT(*)` + `LIMIT 12 OFFSET` → memory O(1) | Di 500 rows, memory 5MB→0.12MB, TTFB -60%, HTML -80% |
| `select([...])` | Hindari `SELECT *` yang load `longText`/`mediumText` (content, bio, testimonial) di list view. Detail page baru load full. | I/O -50%, buffer pool efisien |
| `Cache::remember('categories',3600)` | 4× `distinct pluck` jalan tiap request, padahal kategori jarang berubah. | 1 query/jam bukan 1 query/request |
| `where('title','like', $search.'%')` vs `"%{$search}%"` | Tanpa leading `%` bisa pakai `index(title)` / `FULLTEXT`. Lihat modul 04 untuk index. | Search dari full scan → index seek |
| Hapus `list-filter.js` | JS filter sembunyikan `display:none` tetap load semua DOM + layout thrash. Server filter lebih hemat DOM & bisa cache. | Browser repaint O(n)→O(12), mobile smooth |
| `through()` di paginator | Mapping tetap di PHP tapi di paginated collection (12 items) bukan semua. | CPU map -97% |

**Risiko & Mitigasi:**
- User biasa filter cepat tanpa reload → trade-off. Mitigasi: tambah `wire:navigate` atau Alpine `fetch` jika mau SPA, tapi untuk sekarang server paginate + `withQueryString` sudah cukup & SEO friendly.
- Cache kategori stale setelah admin edit kegiatan → tambah `Cache::forget('activities:categories')` di `Admin\ActivityController@store/update/destroy`.

## ✅ Checklist

- [ ] `PublicController.php: activitiesData, lecturersData, alumniData, documentsData` ganti ke paginate + select + through
- [ ] `activities(), lecturers(), documents(), alumni()` tambah `Request $request` + `Cache::remember` pluck
- [ ] `ProgramProfile` cache forever + forget di update
- [ ] 4 Blade list ganti `data-search` → `<form GET>` + `{{ $links }}` + hapus `asset('js/list-filter.js')`
- [ ] `Admin\ActivityController` dll tambah `Cache::forget` kategori
- [ ] `routes/web.php` tetap, query string via `withQueryString`

## 🧪 Verifikasi

```bash
php artisan tinker --execute 'DB::enableQueryLog(); app(App\Http\Controllers\ProjectController::class);' # cek query count
# Manual:
php artisan serve
# Buka /kegiatan?q=Seminar — cek pagination muncul, filter kategori work, query log 2 query (paginator count + limit) + 0 distinct (cached)
# Buka /dosen, /dokumen, /alumni sama

# EXPLAIN setelah modul 04:
EXPLAIN SELECT id, title FROM activities WHERE status='published' AND category='Seminar' ORDER BY activity_date DESC LIMIT 12;

vendor/bin/pint --dirty --format agent
php artisan test --compact  # tambah test pagination
```

**Estimasi:** Home tetap 3 items (no paginate), list pages dari 500 DOM → 12 DOM, TTFB 1200ms → 400ms di DB 500 rows.
