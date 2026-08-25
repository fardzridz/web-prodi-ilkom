<?php

use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Contact;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\HomeSection;
use App\Models\Lecturer;
use App\Models\Page;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();

    SiteSetting::factory()->create();
    Contact::factory()->create();
    HomeSection::factory()->create();
    ProgramProfile::factory()->create();
});

// ─── Smoke: setiap halaman public merender dengan data nyata ──────────────

test('halaman public merender sukses ketika konten tersedia', function (string $route): void {
    Lecturer::factory()->count(3)->create();
    Activity::factory()->count(3)->create();
    Alumni::factory()->count(3)->create();
    Document::factory()->count(3)->create();

    $this->get($route)->assertSuccessful();
})->with([
    '/',
    '/profil',
    '/dosen',
    '/kegiatan',
    '/dokumen',
    '/alumni',
    '/kontak',
    '/kebijakan-privasi',
    '/aksesibilitas',
]);

// ─── Dosen ────────────────────────────────────────────────────────────────

test('halaman dosen menampilkan nama, keahlian, dan bio dosen aktif', function (): void {
    $lecturer = Lecturer::factory()->create([
        'name' => 'Dr. Ada Lovelace',
        'expertise' => 'Kecerdasan Artifisial',
        'bio' => 'Peneliti algoritma komputasi analitik sejak 1843.',
    ]);

    $this->get('/dosen')
        ->assertSuccessful()
        ->assertSee($lecturer->name)
        ->assertSee('Kecerdasan Artifisial')
        ->assertSee('Peneliti algoritma komputasi analitik sejak 1843.');
});

test('halaman dosen menyembunyikan dosen nonaktif', function (): void {
    Lecturer::factory()->create(['name' => 'Dosen Aktif']);
    Lecturer::factory()->inactive()->create(['name' => 'Dosen Nonaktif']);

    $this->get('/dosen')
        ->assertSuccessful()
        ->assertSee('Dosen Aktif')
        ->assertDontSee('Dosen Nonaktif');
});

test('halaman dosen membatasi 12 item per halaman', function (): void {
    Lecturer::factory()->count(15)->create();

    $response = $this->get('/dosen')->assertSuccessful();

    expect($response->viewData('lecturers')->count())->toBe(12)
        ->and($response->viewData('lecturers')->total())->toBe(15);

    expect($this->get('/dosen?page=2')->viewData('lecturers')->count())->toBe(3);
});

test('pencarian dosen menyaring berdasarkan nama', function (): void {
    Lecturer::factory()->create(['name' => 'Grace Hopper']);
    Lecturer::factory()->create(['name' => 'Alan Turing']);

    $response = $this->get('/dosen?q=Hopper')->assertSuccessful();

    expect($response->viewData('lecturers')->total())->toBe(1);
    $response->assertSee('Grace Hopper')->assertDontSee('Alan Turing');
});

test('filter keahlian dosen yang tidak dikenal diabaikan', function (): void {
    Lecturer::factory()->count(2)->create(['expertise' => 'Sains Data']);

    $response = $this->get('/dosen?expertise=Bidang+Palsu')->assertSuccessful();

    expect($response->viewData('filters')['expertise'])->toBeNull()
        ->and($response->viewData('lecturers')->total())->toBe(2);
});

test('wildcard LIKE pada pencarian dosen diescape', function (): void {
    Lecturer::factory()->create(['name' => 'Barbara Liskov']);

    expect($this->get('/dosen?q=%')->assertSuccessful()->viewData('lecturers')->total())->toBe(0);
});

// ─── Kegiatan ─────────────────────────────────────────────────────────────

test('halaman kegiatan hanya menampilkan kegiatan terbit', function (): void {
    Activity::factory()->create(['title' => 'Seminar Nasional AI']);
    Activity::factory()->draft()->create(['title' => 'Draf Kegiatan Rahasia']);
    Activity::factory()->scheduled()->create(['title' => 'Kegiatan Terjadwal']);

    $this->get('/kegiatan')
        ->assertSuccessful()
        ->assertSee('Seminar Nasional AI')
        ->assertDontSee('Draf Kegiatan Rahasia')
        ->assertDontSee('Kegiatan Terjadwal');
});

test('detail kegiatan menampilkan konten dan kegiatan lainnya', function (): void {
    $activity = Activity::factory()->create([
        'title' => 'Workshop Laravel Lanjut',
        'slug' => 'workshop-laravel-lanjut',
        'content' => '<p>Materi mencakup queue dan caching.</p>',
    ]);
    Activity::factory()->count(2)->create();

    $response = $this->get('/kegiatan/'.$activity->slug)->assertSuccessful();

    $response->assertSee('Workshop Laravel Lanjut')
        ->assertSee('Materi mencakup queue dan caching.', escape: false);

    expect($response->viewData('activity')['content_blocks'])->not->toBeEmpty()
        ->and($response->viewData('otherActivities'))->toHaveCount(2);
});

test('detail kegiatan draf mengembalikan 404', function (): void {
    $activity = Activity::factory()->draft()->create(['slug' => 'draf-tersembunyi']);

    $this->get('/kegiatan/'.$activity->slug)->assertNotFound();
});

test('kartu kegiatan tidak memuat content_blocks', function (): void {
    Activity::factory()->create(['content' => '<p>Konten panjang tidak perlu di kartu.</p>']);

    $activities = $this->get('/kegiatan')->assertSuccessful()->viewData('activities');

    expect($activities->first()['content_blocks'])->toBe([]);
});

// ─── Alumni ───────────────────────────────────────────────────────────────

test('halaman alumni menampilkan alumni aktif beserta testimoni', function (): void {
    Alumni::factory()->create([
        'name' => 'Linus Torvalds',
        'job_position' => 'Software Engineer',
        'testimonial' => 'Kuliah di sini membentuk cara saya berpikir.',
    ]);
    Alumni::factory()->inactive()->create(['name' => 'Alumni Nonaktif']);

    $this->get('/alumni')
        ->assertSuccessful()
        ->assertSee('Linus Torvalds')
        ->assertSee('Kuliah di sini membentuk cara saya berpikir.')
        ->assertDontSee('Alumni Nonaktif');
});

test('filter posisi pekerjaan alumni bekerja', function (): void {
    Alumni::factory()->create(['name' => 'Alumni Data', 'job_position' => 'Data Analyst']);
    Alumni::factory()->create(['name' => 'Alumni Guru', 'job_position' => 'Guru Informatika']);

    $response = $this->get('/alumni?job=Data+Analyst')->assertSuccessful();

    expect($response->viewData('alumni')->total())->toBe(1);
    $response->assertSee('Alumni Data')->assertDontSee('Alumni Guru');
});

// ─── Dokumen ──────────────────────────────────────────────────────────────

test('halaman dokumen menampilkan dokumen terbit beserta kategori', function (): void {
    $category = DocumentCategory::factory()->create(['name' => 'Kurikulum Inti']);
    Document::factory()->for($category, 'documentCategory')->create(['title' => 'Kurikulum 2026']);
    Document::factory()->draft()->create(['title' => 'Dokumen Draf']);

    $this->get('/dokumen')
        ->assertSuccessful()
        ->assertSee('Kurikulum 2026')
        ->assertSee('Kurikulum Inti')
        ->assertDontSee('Dokumen Draf');
});

test('payload dokumen tidak membocorkan path penyimpanan privat', function (): void {
    $document = Document::factory()->create();

    $documents = $this->get('/dokumen')->assertSuccessful()->viewData('documents');

    expect($documents->first())->not->toHaveKey('file');
    $this->get('/dokumen')->assertDontSee($document->file);
});

test('filter kategori dokumen menyaring hasil', function (): void {
    $kurikulum = DocumentCategory::factory()->create(['name' => 'Kategori Kurikulum']);
    $panduan = DocumentCategory::factory()->create(['name' => 'Kategori Panduan']);

    Document::factory()->for($kurikulum, 'documentCategory')->create(['title' => 'Berkas Kurikulum']);
    Document::factory()->for($panduan, 'documentCategory')->create(['title' => 'Berkas Panduan']);

    $response = $this->get('/dokumen?category=Kategori+Kurikulum')->assertSuccessful();

    expect($response->viewData('documents')->total())->toBe(1);
    $response->assertSee('Berkas Kurikulum')->assertDontSee('Berkas Panduan');
});

// ─── Redirect ─────────────────────────────────────────────────────────────

test('visi-misi dan jurnal melakukan redirect', function (string $route): void {
    $this->get($route)->assertRedirect();
})->with(['/visi-misi', '/jurnal']);

test('redirect jurnal mengikuti pengaturan site', function (): void {
    SiteSetting::query()->delete();
    SiteSetting::factory()->create(['journal_url' => 'https://jurnal.contoh.test']);

    $this->get('/jurnal')->assertRedirect('https://jurnal.contoh.test');
});

// ─── Halaman statis dari tabel pages ──────────────────────────────────────

test('halaman statis merender konten tersanitasi dari database', function (string $slug, string $uri): void {
    Page::query()->where('slug', $slug)->update([
        'content' => '<p>Isi resmi halaman.</p><script>alert(1)</script>',
    ]);

    $this->get($uri)
        ->assertSuccessful()
        ->assertSee('Isi resmi halaman.', escape: false)
        ->assertDontSee('<script>alert(1)</script>', escape: false);
})->with([
    ['kebijakan-privasi', '/kebijakan-privasi'],
    ['aksesibilitas', '/aksesibilitas'],
]);
