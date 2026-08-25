# 04 — Database MySQL InnoDB: Index, Tipe Data & Migrasi

> **Target:** `database/migrations/*` (22 file), `config/database.php:20`, `app/Models/*.php`, `database/seeders/*`

## 📌 Ringkasan Kode & Fungsi

**Engine diharapkan:** MySQL InnoDB `utf8mb4_unicode_ci` (via `AGENTS.md:12`, `config/database.php:56-57`). Tapi:

- `config/database.php:20` default masih `'sqlite'` → local dev bisa pakai SQLite, menyembunyikan bug MySQL (FK, charset, `FULLTEXT`).
- 12 `Schema::create` tidak set `engine`/`charset`/`collation` eksplisit → fallback ke DB default (jika server latin1 → data korup).
- Tipe data: `$table->id()` = `BIGINT` untuk tabel 1-10 rows (`site_settings`, `contacts`, `home_sections`), `$table->string()` default 255 untuk `status` (padahal cuma `active`/`inactive`), `longText` untuk `history` yang <64KB, `VARCHAR(255)` indexed → 1020 bytes prefix.
- Index: ada `index(['status','sort_order'])` di lecturers, `index(['status','published_at'])` di activities, tapi missing komposit yang dipakai query public (`where status=published orderBy activity_date`).

## 🚨 Isu / Celah

| ID | File:Line | Tingkat | Detail |
|----|-----------|---------|--------|
| H4-1 | `activities:2026_07_13_163245:28` | **HIGH** | `index(['status','published_at'])` ada, tapi query public `PublicController.php:359` `where status=published orderBy activity_date DESC` → pakai `index(activity_date)` single, bukan komposit → `Using filesort`. Missing `[status,activity_date,id]` komposit. |
| H4-2 | `activities:2026_07_29_234246:15` `category string nullable` | **HIGH** | `category` tanpa index, query `distinct pluck('category')` di `PublicController.php:171` + filter `where category=?` scan full table. |
| H4-3 | `lecturers:2026_07_13_163223:24` `status VARCHAR255` | **HIGH** | `distinct pluck('expertise')` di `PublicController.php:157` `where status=active whereNotNull expertise` → no `[status,expertise]` index. `index(['status','sort_order'])` tidak cover `expertise` atau `name`. |
| H4-4 | `alumni:2026_07_13_163234:26` | **HIGH** | `where status=active distinct job_position` di `PublicController.php:255` → no `[status,job_position]`. Seeder `firstOrCreate(['name','batch_year'])` tapi DB tidak ada `unique([name,batch_year])` → duplikat race. |
| H4-5 | `messages:2026_08_01_152406:16` | **HIGH** | Tabel `messages` **zero index**: `name,email,subject` string 255 tanpa index, `created_at` tanpa index → admin list `orderByDesc(created_at)` filesort, contact lookup `where email=?` scan. |
| H4-6 | `config/database.php:20` `default => env('DB_CONNECTION','sqlite')` | **HIGH** | Konflik dengan aturan MySQL. CI/test pakai SQLite → FK `restrictOnDelete` di `activities.user_id` & `documents.document_category_id` tidak teruji, `FULLTEXT` tidak ada di SQLite. |
| M4-1 | `sessions:0001_01_01_000000:31` `foreignId('user_id')->nullable()->index()` | **MEDIUM** | `index()` saja tanpa `->constrained()->nullOnDelete()` → orphan session jika user dihapus, tidak ada `ON DELETE CASCADE`. `string('id')->primary` 255 padahal session id 32 char. |
| M4-2 | `document_categories:2026_07_13_163229:16` `name` tanpa index | **MEDIUM** | `orderBy('name')` di `DocumentCategory::orderBy('name')->get()` tiap admin form, `whereHas('documents', where status=published)` butuh `[document_category_id,status]` komposit. |
| M4-3 | `documents:2026_07_13_163250:23` `file_type VARCHAR255` | **MEDIUM** | `file_type` cuma `pdf/doc/docx` 3 char → `VARCHAR(20)` cukup. `file_size unsignedInteger` max 4GB, tapi cast int. |
| M4-4 | `program_profiles:2026_07_13_163218:16` 4× `longText` | **MEDIUM** | `history,description,mission,goals` `longText` (4GB pointer, off-page) padahal <64KB cukup `TEXT`. `SELECT *` di `PublicController.php:98` load 4 longText tiap home. |
| M4-5 | `2026_08_01_000000_add_updated_at_indexes.php:11` `index('updated_at')` single | **MEDIUM** | 4 tabel tambah `index(updated_at)` tapi query tidak filter `updated_at`, hanya `latest('updated_at')` occasional → index low selectivity, write overhead tiap UPDATE. `down()` pakai `dropIndexIfExists('updated_at')` salah — seharusnya `dropIndex(['updated_at'])`. |
| M4-6 | `users:2026_07_13_163202:15` `string('role')->default('admin')->index()` | **LOW-MED** | `role` 255 indexed dengan 1 value `admin` → 1020 bytes index untuk selectivity 1. Seharusnya `string('role',20)` atau `enum`. |
| LOW-1 | Global | **LOW** | Tidak ada `engine='InnoDB'`/`charset='utf8mb4'`/`collation` di migrations, `BIGINT` untuk singleton tabel (1 row) waste 4 bytes/PK+FK. |

**Verifikasi sekarang:**
```sql
SHOW INDEX FROM activities; -- cek hanya index(status,published_at) + single activity_date
SHOW CREATE TABLE activities; -- cek engine, charset
EXPLAIN SELECT DISTINCT category FROM activities WHERE status='published';
```

## 🛠️ Rekomendasi Refactoring (Before → After)

### A. Migration Baru: Index Komposit + FULLTEXT (High Value)

Buat `php artisan make:migration optimize_performance_indexes --no-interaction`

**Before (existing):**
```php
// 2026_07_13_163245_create_activities_table.php
$table->index(['status','published_at']);
$table->index('activity_date');
$table->string('category')->nullable(); // no index
```

**After `database/migrations/2026_08_22_000000_optimize_performance_indexes.php`:**
```php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
  public function up(): void
  {
    // activities — utama untuk PublicController::activitiesData
    Schema::table('activities', function (Blueprint $table) {
      // Komposit untuk where status=published orderBy activity_date DESC, id DESC
      $table->index(['status','activity_date','id'], 'activities_status_date_id_idx');
      // Untuk filter kategori + distinct
      $table->index(['status','category'], 'activities_status_category_idx');
      // FULLTEXT untuk search title+location (ganti LIKE %search%)
      // MySQL 8+ InnoDB support FULLTEXT
      if (DB::getDriverName() === 'mysql') {
        $table->fullText(['title','location'], 'activities_title_location_fulltext');
      }
    });

    Schema::table('lecturers', function (Blueprint $table) {
      $table->index(['status','expertise'], 'lecturers_status_expertise_idx');
      // Extend existing ['status','sort_order'] untuk cover name
      // Drop lalu buat baru [status,sort_order,name,id]
      // $table->dropIndex('lecturers_status_sort_order_index');
      // $table->index(['status','sort_order','name','id'], 'lecturers_status_sort_name_idx');
    });

    Schema::table('alumni', function (Blueprint $table) {
      $table->index(['status','job_position'], 'alumni_status_job_idx');
      $table->unique(['name','batch_year'], 'alumni_name_batch_unique');
    });

    Schema::table('documents', function (Blueprint $table) {
      $table->index(['document_category_id','status'], 'documents_cat_status_idx');
      $table->index(['status','uploaded_at','id'], 'documents_status_uploaded_idx');
    });

    Schema::table('messages', function (Blueprint $table) {
      $table->index('email', 'messages_email_idx');
      $table->index('subject', 'messages_subject_idx');
      $table->index(['created_at','id'], 'messages_created_idx');
    });

    Schema::table('document_categories', function (Blueprint $table) {
      $table->index('name', 'doccat_name_idx');
    });

    Schema::table('sessions', function (Blueprint $table) {
      // Fix FK jika belum ada (butuh users id BIGINT)
      // $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
    });

    Schema::table('password_reset_tokens', function (Blueprint $table) {
      $table->index('token', 'pwd_token_idx');
    });
  }

  public function down(): void
  {
    Schema::table('activities', function (Blueprint $table) {
      $table->dropIndex('activities_status_date_id_idx');
      $table->dropIndex('activities_status_category_idx');
      $table->dropFullText('activities_title_location_fulltext');
    });
    Schema::table('lecturers', fn(Blueprint $t)=> $t->dropIndex('lecturers_status_expertise_idx'));
    Schema::table('alumni', function(Blueprint $t){
      $t->dropIndex('alumni_status_job_idx');
      $t->dropUnique('alumni_name_batch_unique');
    });
    Schema::table('documents', function(Blueprint $t){
      $t->dropIndex('documents_cat_status_idx');
      $t->dropIndex('documents_status_uploaded_idx');
    });
    Schema::table('messages', function(Blueprint $t){
      $t->dropIndex('messages_email_idx');
      $t->dropIndex('messages_subject_idx');
      $t->dropIndex('messages_created_idx');
    });
    Schema::table('document_categories', fn(Blueprint $t)=> $t->dropIndex('doccat_name_idx'));
    Schema::table('password_reset_tokens', fn(Blueprint $t)=> $t->dropIndex('pwd_token_idx'));
  }
};
```

**Jalankan:**
```bash
php artisan migrate --force
php artisan migrate:rollback --step=1 # test down
php artisan migrate --force
```

### B. Fix `config/database.php:20` Default

**Before:**
```php
'default' => env('DB_CONNECTION', 'sqlite'),
'mysql' => ['engine' => null, ...],
```

**After `config/database.php:20,61`:**
```php
'default' => env('DB_CONNECTION', 'mysql'),
'mysql' => [
  'driver' => 'mysql',
  'charset' => env('DB_CHARSET', 'utf8mb4'),
  'collation' => env('DB_COLLATION', 'utf8mb4_unicode_ci'),
  'engine' => 'InnoDB',
  // ...
],
'mariadb' => ['engine' => 'InnoDB', ...],
```

**Plus `.env.example:3`:**
```
DB_CONNECTION=mysql
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

### C. Shrink Tipe Data (Opsional, butuh DBAL — low risk, high reward jika tabel besar)

Jika mau, buat migration `2026_08_22_000001_optimize_column_types.php`:
```php
Schema::table('activities', function(Blueprint $t){
  $t->string('status', 20)->change(); // dari 255
  $t->string('category', 100)->nullable()->change();
  $t->string('location', 150)->nullable()->change();
});
Schema::table('lecturers', function(Blueprint $t){
  $t->string('status', 20)->change();
  $t->string('position', 100)->nullable()->change();
  $t->string('expertise', 100)->nullable()->change();
});
// Tapi butuh doctrine/dbal dan ALTER besar — skip jika rows <10k, cukup SELECT spesifik di 01.
```

**Rekomendasi:** Jangan ubah tipe sekarang jika data <1k rows — cukup `select()` di 01. Tipe dioptimalkan saat tabel >10k atau buat fresh DB.

### D. Model — Tambah Scope & Cast Hint (Optional Clean)

```php
// app/Models/Activity.php
public function scopePublished($q){ return $q->where('status', self::STATUS_PUBLISHED); }
public function scopeSearch($q, ?string $term){
  return $term ? $q->whereFullText(['title','location'], $term) : $q;
}
// Pakai: Activity::published()->search($search)->orderByDesc('activity_date')->paginate(12)
```

### E. Fix `add_updated_at_indexes.php` Down Bug (Housekeeping)

**Before `2026_08_01_000000:19`:**
```php
$table->dropIndexIfExists('updated_at'); // salah — index name bukan column name
```

**After:**
```php
$table->dropIndex(['updated_at']);
```

## 💡 Penjelasan Perubahan

| Perubahan | Kenapa (MySQL InnoDB) | Dampak |
|-----------|------------------------|--------|
| `[status,activity_date,id]` komposit | Query `WHERE status=? ORDER BY activity_date DESC, id DESC` bisa `Using index condition` + `Backward index scan` (MySQL 8) tanpa `filesort`. Sebelumnya 2 index terpisah → index_merge atau filesort. | `EXPLAIN` `Using filesort` → `Using index condition`, latency -40% |
| `[status,category]` | `WHERE status=published AND category=?` + `SELECT DISTINCT category WHERE status=published` pakai covering index. | Distinct scan 1000 rows → index only |
| `FULLTEXT(title,location)` | Ganti `LIKE '%search%'` (full scan) jadi `MATCH(title,location) AGAINST(? IN BOOLEAN MODE)` pakai inverted index. | Search 500ms → 5ms di 10k rows |
| `unique(name,batch_year)` alumni | `firstOrCreate(['name','batch_year'])` di seeder tanpa constraint → race duplikat. InnoDB unique prevent. | Data integrity |
| `[email,created_at]` messages | Admin list `orderBy(created_at)` + lookup `where email=?` sering. | Pagination filesort hilang |
| `engine='InnoDB'` explicit | Jika server default MyISAM/latin1, table baru tetap InnoDB utf8mb4. | Consistent |
| Default `mysql` | Local dev tidak hidden bug FK/FULLTEXT/JSON. `php artisan test` pakai MySQL beneran (atau in-memory tapi aware). | Catch bug early |

**Yang sengaja TIDAK diubah sekarang:**
- `BIGINT` → `INT` untuk singleton tabel (1 row, waste 4 bytes tapi ALTER rebuild besar, tidak urgent).
- `longText` → `text` (butuh `change()` + DBAL, downtime). Cukup `select()` di 01.

## ✅ Checklist

- [ ] `php artisan make:migration optimize_performance_indexes`
- [ ] Isi up/down seperti di atas (7 tabel)
- [ ] `config/database.php:20` ganti default `mysql`, `engine=>InnoDB`
- [ ] `.env.example` tambah `DB_CHARSET`/`DB_COLLATION`
- [ ] `php artisan migrate` + `SHOW INDEX FROM activities` cek index baru ada
- [ ] `EXPLAIN` untuk 3 query public (kegiatan, dosen distinct, alumni distinct) — harus `Using index`
- [ ] `php artisan migrate:rollback --step=1` test down success lalu `migrate` lagi
- [ ] (Opsional) `scopePublished` di Models

## 🧪 Verifikasi

```sql
-- Setelah migrate
SHOW INDEX FROM activities; -- harus ada activities_status_date_id_idx, activities_status_category_idx, activities_title_location_fulltext
SHOW CREATE TABLE activities\G -- engine=InnoDB DEFAULT CHARSET=utf8mb4

-- EXPLAIN harus pakai index
EXPLAIN SELECT id, title FROM activities WHERE status='published' ORDER BY activity_date DESC, id DESC LIMIT 12;
-- Extra: Using where; Using index condition (good), bukan Using filesort

EXPLAIN SELECT DISTINCT category FROM activities WHERE status='published' ORDER BY category;
-- Extra: Using where; Using index (covering)

EXPLAIN SELECT * FROM lecturers WHERE status='active' AND expertise='AI' ORDER BY sort_order;
-- key: lecturers_status_expertise_idx

-- FULLTEXT test (jika sudah migrate)
SELECT * FROM activities WHERE MATCH(title,location) AGAINST('Seminar' IN BOOLEAN MODE) AND status='published' LIMIT 12;
```

```bash
php artisan migrate --force
php artisan tinker --execute 'echo DB::getDriverName();'
vendor/bin/pint --dirty --format agent
php artisan test --compact
# Benchmark: buat 500 dummy via factory lalu ukur
php artisan tinker --execute 'App\Models\Activity::factory()->count(500)->create(["status"=>App\Models\Activity::STATUS_PUBLISHED]);'
# lalu hit /kegiatan?category=Seminar — ukur TTFB
```

**Estimasi:** Full scan → index seek, search -99%, distinct -80%, TTFB list -30% tambahan di atas 01.
