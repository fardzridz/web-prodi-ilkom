# 08 — SEO Audit Kompetitif & Rencana Strategis S1 Ilmu Komputer UNIWARA

> **Sumber:** Laporan Audit Kompetitif & Rencana Strategis Optimasi Mesin Pencari Program Studi S1 Ilmu Komputer Universitas PGRI Wiranegara — di-modular-kan agar tidak lupa, siap eksekusi per Fase.

> **📌 Keputusan 2026-08-23 (Lock Fase 1 — belum eksekusi kode, review dulu):**
> - **Domain prod:** `https://compscience.uniwara.ac.id` (belum live, hosting secepatnya). Dev tetap `http://localhost` via `APP_URL`. Kode pakai `config('app.url')` jadi sitemap/canonical auto ikut domain tanpa hardcode.
> - **Fase 1 doang sekarang** — title/meta + canonical + sitemap.xml dinamis + JSON-LD Organization/EducationalOccupationalProgram. **Fase 2 & 3 tunda** tunggu konten asli (penulis kegiatan bukan user; butuh 4 artikel 600-1000 kata + FAQ Biaya/RPL + `GA4_ID`).
> - **Sitemap:** `SitemapController` + `config/seo.php` (bukan closure), `take(100)` = **batas atas dinamis** — kalau baru 5 kegiatan ya isi 5, tidak wajib 100. Cache 3600s.
> - **GA4/GSC opsional:** `config/seo.php: ga4_id => env('GA4_ID')` placeholder, kalau kosong tidak inject script. GSC = Search Console untuk submit sitemap, GA4 = Analytics `G-XXXX` untuk track klik Daftar.
> - **Title rekom:** `S1 Ilmu Komputer UNIWARA Pasuruan | Biaya Kuliah & Pendaftaran` (58c) + meta 154c doc. Alternatif 2 opsi lain di catatan review.

## 📌 Ringkasan Kode & Fungsi

| Lapisan | File Utama | Fungsi SEO Saat Ini | Status |
|---------|------------|---------------------|--------|
| **Metadata** | `resources/views/layouts/public.blade.php:6` `app/Http/Controllers/PublicController.php:77` | `title` generik `Program Studi Ilmu Komputer`, `description` dari `welcome_description`, `og:*` dasar | Generik, belum keyword lokal/transaksional |
| **Hero / Heading** | `resources/views/public/home.blade.php:72` `components/hero.blade.php:20` | `h1` dobel (Sambutan + Kegiatan + Alumni), `hero` alt kosong, `logone.png` alt kosong | Hierarchy salah, kehilangan keyword |
| **Routing / Sitemap** | `routes/web.php:48` `public/.htaccess` `public/robots.txt` | Tidak ada `sitemap.xml` dinamis, robots default, tidak ada `canonical` | Crawlability lemah, duplikat `?page=2` |
| **Data Terstruktur** | `layouts/public.blade.php:23` | Belum ada JSON-LD `Organization`/`EducationalOccupationalProgram`/`Course`/`FAQPage` | Tidak dapat rich result |
| **Konten** | `resources/views/public/activities/show.blade.php` `components/activity-card.blade.php:10` | Artikel kegiatan galeri + 1 kalimat, tidak ada narasi 600-1000 kata, alt deskriptif minim | E-E-A-T lemah |
| **Analytics** | `config/app.php` `layouts/public.blade.php` | Belum ada GSC verifikasi + GA4 event `click_pendaftaran` | Guesswork, tidak ukur konversi |
| **Infra** | `public/build/` `vite.config.js` | Sudah vite + cache 1 tahun (modul 07), LCP oke | Siap untuk SEO teknis |

**Identitas Prodi:** S1 Ilmu Komputer UNIWARA Pasal 071100 / SINTA ID 6780934 — celah nomenklatur vs kompetitor `Teknik Informatika`.

---

## 🚨 Isu / Celah (Kompetitor & Pasar)

### Pasar & Keyword Gap — HIGH
- **Celah nomenklatur:** UNIWARA `Ilmu Komputer`, pesaing Pasuruan dominan `Teknik Informatika` → peluang monopoli kueri `Ilmu Komputer Pasuruan` (persaingan rendah) + butuh adaptasi semantik `Informatika` untuk kueri umum.
- **Judul generik:** `Program Studi Ilmu Komputer` tanpa `UNIWARA Pasuruan` & `Biaya/Pendaftaran` → hilang relevansi geografis & komersial di algoritma.

### Kompetitor Langsung — HIGH

| Institusi | URL Target | Akreditasi & Otoritas | Metadata & On-Page | Celah & Peluang |
|-----------|------------|------------------------|--------------------|-----------------|
| **Univ. Merdeka Pasuruan (FTI)** | `unmerpas.ac.id` / `pmb.unmerpas.ac.id` | S1 Teknik Informatika **Baik Sekali** LAM INFOKOM No.025/SK/LAM-INFOKOM/Ak.P/S/VIII/2024; BAN-PT No.117/SK/BAN-PT/Ak/PT/IV/2026 | Judul fokus institusi, H1 profil tunggal, H2/H3 berita | Portal PMB terpisah, **tidak ada JSON-LD** `Course`/`EducationalOccupationalProgram` |
| **Univ. Yudharta Pasuruan (FT)** | `if.yudharta.ac.id` / `teknik.yudharta.ac.id` | Terdaftar LAM-INFOKOM; Top 10 SINTA dosen | Subdomain fakultas/prodi, aktif coding camp & hibah | **Subdomain downtime**, tidak ada structured data, internal link fragmentasi |
| **FILKOM UB** | `filkom.ub.ac.id` | S1 Teknik Informatika **Unggul**, ABET | Judul `Fakultas Ilmu Komputer | Universitas Brawijaya`, taksonomi profil/pendidikan/penelitian/mutu | Arsitektur masif fokus terpecah, jadi **gold standard** taksonomi |

**Insight:** UNMERPAS rank atas PMB karena spam `Baik Sekali` di homepage tapi metadata prodi generik. Yudharta update konten tapi crawlability jelek. UB taksonomi rapi untuk benchmark.

### Arsitektur & Teknis — MEDIUM-HIGH
- Tidak ada `sitemap.xml` dinamis + `robots.txt` proper → crawl error, indeks lambat.
- Tidak ada `canonical`, tidak ada `JSON-LD` → duplikat & tidak rich snippet.
- `h1` dobel, `alt` kosong, tidak ada `FAq`/`Article` schema.

---

## 🛠️ Rekomendasi Refactoring (Before → After)

### 1. Hierarki Keyword & Formulasi Title/Meta

**Before:**
```blade
// layouts/public.blade.php:6
<title>Program Studi Ilmu Komputer</title>
<meta name="description" content="Website resmi Program Studi Ilmu Komputer">
```

**After (50-60 char title, 150-160 meta):**
```php
// PublicController.php:77 home()
$seoTitle = 'S1 Ilmu Komputer UNIWARA Pasuruan | Biaya Kuliah & Pendaftaran'; // 58 char
$seoDesc  = 'Daftar kuliah S1 Ilmu Komputer di Universitas PGRI Wiranegara Pasuruan. Kurikulum AI, rekayasa perangkat lunak, biaya terjangkau & prospek karier global.'; // 154 char
return view('public.home', ['seoTitle'=>$seoTitle,'seoDesc'=>$seoDesc]);
```
```blade
// layouts/public.blade.php
<title>@yield('title', $seoTitle ?? $site?->site_name)</title>
<meta name="description" content="@yield('description', $seoDesc ?? '')">
<link rel="canonical" href="{{ url()->current() }}">
```

| Klasifikasi | Frasa | Intensi | Nilai | Penempatan |
|-------------|-------|---------|-------|------------|
| **Prioritas 1 Transaksional Lokal (C)** | `Kuliah Ilmu Komputer Pasuruan` / `Pendaftaran S1 Ilmu Komputer Pasuruan` | Transaksional tinggi, konversi daftar | Tertinggi | Title 50-60, H1, Meta 150, slug `/daftar` |
| **Prioritas 2 Entitas Geografis (A)** | `Ilmu Komputer Uniwara Pasuruan` / `S1 Ilmu Komputer Universitas PGRI Wiranegara` | Navigasional, Knowledge Graph | Mudah rank 1 | Title alt, H2, anchor, JSON-LD `EducationalOrganization` |
| **Prioritas 3 Otoritas (B)** | `Prodi Ilmu Komputer Akreditasi Resmi Pasuruan` | Komparatif kualitas | Sedang | Above the fold, H2/H3 profil akreditasi |

### 2. Sitemap + Robots + Canonical — UPDATE 2026-08-23 (Fase 1)

**After (SitemapController + config/seo.php, bukan closure):**
```php
// routes/web.php — Fase 1
Route::get('/sitemap.xml', \App\Http\Controllers\SitemapController::class)->name('sitemap');

// app/Http/Controllers/SitemapController.php — dinamis, max 100 tapi isi sesuai data (5→5, 100→100)
$urls = Cache::remember('sitemap', 3600, fn () => collect([
    route('home'), route('profile'), route('vision-mission'), route('lecturers'), route('activities.index'), route('alumni'), route('documents'), route('contact'),
    ...Activity::where('status','published')->latest('activity_date')->limit(100)->pluck('slug')->map(fn($s)=>route('activities.show',$s)),
    ...Document::where('status','published')->latest('uploaded_at')->limit(50)->pluck('slug')->map(fn($s)=>route('documents')), // jika dokumen per slug
])->filter());
return response()->view('sitemap', compact('urls'))->header('Content-Type','text/xml');
```
```
# public/robots.txt — prod domain
User-agent: *
Allow: /
Sitemap: https://compscience.uniwara.ac.id/sitemap.xml
# dev: APP_URL=http://localhost tetap jalan, prod ganti .env saja
```
> **Catatan:** `SitemapController` dipilih per review 2026-08-23 (cacheable, testable) bukan closure. Limit 100 = batas atas, tidak wajib penuh.

### 3. JSON-LD Terstruktur

**After `layouts/public.blade.php:23` stack head:**
```blade
<script type="application/ld+json">
{
  "@context":"https://schema.org",
  "@type":"EducationalOccupationalProgram",
  "name":"S1 Ilmu Komputer",
  "provider":{"@type":"CollegeOrUniversity","name":"Universitas PGRI Wiranegara","sameAs":"https://uniwara.ac.id"},
  "credentialCategory":"Bachelor Degree",
  "timeToComplete":"P8Y",
  "inLanguage":"id-ID",
  "programType":"Ilmu Komputer",
  "offers":{"@type":"Offer","category":"Biaya Kuliah"}
}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Course","name":"Machine Learning","provider":{"@type":"CollegeOrUniversity","name":"UNIWARA"}}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"Bagaimana mekanisme RPL?","acceptedAnswer":{"@type":"Answer","text":"..."}}]}
</script>
```

### 4. Heading & Alt

**Before:** `home.blade.php` 3× `h1` (Sambutan, Kegiatan, Alumni) + `hero` `alt=""`
**After:** 1× `h1` di hero (`Logika diasah...`), lainnya `h2`/`h3` klaster semantik, `alt="Mahasiswa Ilmu Komputer UNIWARA Praktikum AI"` + `logone.png` alt `Logo UNIWARA`.

---

## 💡 Penjelasan Perubahan

**Kenapa title 58 char gabung Prioritas 1+2?** Menyasar `Kuliah Ilmu Komputer Pasuruan` (volume transaksional) + `UNIWARA Pasuruan` (entitas lokal rendah kompetisi) sekaligus, mengungguli UNMERPAS yang judulnya kaku `Universitas Merdeka Pasuruan`.

**Kenapa sitemap dinamis?** Tanpa sitemap, Google tebak crawl `?page=2` duplikat. Dengan sitemap 20 kegiatan terbaru + `canonical`, indeks 100% dalam 7-14 hari.

**Kenapa JSON-LD 3 tipe?** `EducationalOccupationalProgram` kasih tau Google ini S1 8 semester, `Course` petakan MK unggulan (ML, Citra Digital, Web Programming) untuk rich result, `FAQPage` langsung muncul di SERP.

## 📊 Rencana Aksi Eksekusi & Metrik — UPDATE 2026-08-23

| Fase | Rincian Teknis & Editorial | Luaran | Metrik | Status 2026-08-23 |
|------|----------------------------|--------|--------|-------------------|
| **Fase 1 Quick Wins Teknis & Metadata (7-14 hari) — DONE 2026-08-25** | 1. Update title/meta semua halaman prodi (`S1 Ilmu Komputer UNIWARA Pasuruan | Biaya Kuliah & Pendaftaran` 58c) 2. Pasang `canonical` per-route dari controller 3. `SitemapController` + `robots.txt` `https://compscience.uniwara.ac.id/sitemap.xml` (take 100 dinamis) 4. Injeksi `Organization` + `EducationalOccupationalProgram` via `config/seo.php` + `components/seo-jsonld.blade.php` 5. Single-`h1` + alt hero deskriptif | Source teroptimasi, URL terdaftar tanpa error | Indeks 100% halaman utama, Top 3 `Ilmu Komputer Uniwara Pasuruan` | **DONE — kode + 25 tes SEO/sitemap hijau** |
| **Fase 2 Interlinking & Klaster Konten (40% impressions) — BACKLOG** | 1. Internal link `ejurnal.uniwara.ac.id` → `/profil` 2. **Tunggu konten asli** 4 artikel kegiatan 600-1000 kata + `Article` schema + alt deskriptif (penulis bukan user) 3. H1-H3 semantik | 4 artikel otoritas, jaringan link jurnal-prodi | +40% tayangan organik non-brand di GSC | **TUNDA — koding tunggu artikel asli** |
| **Fase 3 Konversi & Otoritas Lanjutan (PageSpeed >85) — BACKLOG** | 1. Landing `/kuliah-ilmu-komputer-pasuruan` (butuh copy Biaya/Beasiswa) 2. FAQ interaktif + `FAQPage` 3. GA4 event `click_pendaftaran` di `navbar.blade.php:37` (butuh `GA4_ID=G-XXXX`) | Landing konversi siap, skor mobile >85 | Top 3 `Kuliah Ilmu Komputer Pasuruan`, CTR PMB naik | **TUNDA — koding tunggu copy FAQ + GA4_ID** |

**E-E-A-T Penguatan:**
- Artikel kegiatan 600-1000 kata: relevansi teknologi + peran mahasiswa + bimbingan dosen + luaran aplikatif, foto HD alt semantik.
- Riset dosen SINTA (AI, visi komputer, web) jadi artikel populer.
- Tracer alumni (Person + Review) foto + posisi + testimoni.

## ✅ Checklist Verifikasi — UPDATE 2026-08-25 (Fase 1 DONE)

- [x] Route `sitemap.xml` terdaftar (`php artisan route:list --path=sitemap`) + `Content-Type: text/xml` terverifikasi via `tests/Feature/SitemapTest.php`
- [x] `<link rel="canonical">` per halaman (bukan `url()->current()` mentah, pakai `canonical` dari controller supaya `?page=2` tidak jadi duplikat) + JSON-LD `CollegeOrUniversity` + `EducationalOccupationalProgram` — divalidasi struktur di `tests/Feature/SeoMetaTest.php`
- [x] `robots.txt`: `Disallow: /admin`, `/dokumen/*/download`, `/dokumen/*/view` + `Sitemap:`
- [x] Satu `<h1>` per halaman (hero overlay dipindah keluar loop slider; heading section jadi `h2`)
- [x] Alt hero deskriptif via `SiteService::defaultHeroAlt()` (fallback berkeyword, bukan `alt=""`)
- [x] Title unik per halaman (8 controller `App\Http\Controllers\Public\*`) — diuji anti-duplikat
- [x] GA4 conditional: `config('seo.ga4_id')` kosong → tidak inject script (diuji dua arah)
- [x] `vendor/bin/pint --dirty` pass + `php artisan test --compact` **151 pass / 358 assertions** + `npm run build` pass
- [ ] GSC → submit sitemap setelah domain live (`https://compscience.uniwara.ac.id`)
- [ ] Lighthouse SEO 100 / Performance >85 mobile — ukur di staging setelah hosting aktif

**Dependensi UPDATE:** GSC + GA4 `G-XXXX` **tidak wajib** untuk Fase 1 (code pakai `if(config('seo.ga4_id'))`). Bisa submit sitemap GSC nanti pas domain live.

**Catatan eksekusi 2026-08-25:** referensi gambar mati ikut dibersihkan (`hero-1.jpeg` → `hero-1.webp`, `hero-2.png` → `hero/hero-2.webp`) karena aset lama sudah dikonversi WebP di modul 06 tapi path-nya belum diikutkan — ini bikin hero halaman dalam tidak render sama sekali.

**Next:** Fase 2/3 stay backlog tunggu 4 artikel asli + copy Biaya/FAQ + `GA4_ID`.
