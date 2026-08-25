<?php

use App\Models\Contact;
use App\Models\HomeSection;
use App\Models\SiteSetting;
use App\Services\Public\SiteService;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();

    HomeSection::factory()->create();
});

/**
 * Regression: `/jurnal` used to write the `site_setting` cache key with only
 * the `journal_url` column, starving every other consumer of the same key.
 */
test('mengakses jurnal lebih dulu tidak merusak cache pengaturan site', function (): void {
    SiteSetting::factory()->create(['site_name' => 'Prodi Ilmu Komputer UNIWARA']);
    Contact::factory()->create();

    $this->get('/jurnal')->assertRedirect();

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('Prodi Ilmu Komputer UNIWARA');
});

test('cache site_setting berisi seluruh kolom yang dibutuhkan layout', function (): void {
    SiteSetting::factory()->create();
    Contact::factory()->create();

    $this->get('/jurnal')->assertRedirect();

    expect(Cache::get('site_setting'))
        ->toHaveKeys(['site_name', 'university_name', 'faculty_name', 'logo', 'favicon', 'journal_url', 'registration_url', 'footer_text', 'footer_academic_links']);
});

test('SiteService memakai nilai default ketika tabel kosong', function (): void {
    $service = app(SiteService::class);

    expect($service->getSiteSetting()->site_name)->toBe('Program Studi Ilmu Komputer')
        ->and($service->journalUrl())->toBe(SiteService::DEFAULT_JOURNAL_URL)
        ->and($service->getContact()->email)->not->toBeEmpty();
});

test('journal_url kosong jatuh ke default', function (): void {
    SiteSetting::factory()->create(['journal_url' => '  ']);

    expect(app(SiteService::class)->journalUrl())->toBe(SiteService::DEFAULT_JOURNAL_URL);
});

test('SiteService memoize hasil dalam satu request', function (): void {
    SiteSetting::factory()->create();
    $service = app(SiteService::class);

    expect($service->getSiteSetting())->toBe($service->getSiteSetting())
        ->and($service->getContact())->toBe($service->getContact());
});

test('SiteService di-scope sehingga composer dan controller berbagi instance', function (): void {
    expect(app(SiteService::class))->toBe(app(SiteService::class));
});

test('membersihkan cache membuat request berikutnya melihat nilai baru', function (): void {
    SiteSetting::factory()->create(['site_name' => 'Nama Lama']);
    Contact::factory()->create();

    $this->get('/')->assertSuccessful()->assertSee('Nama Lama');
    expect(Cache::get('site_setting'))->not->toBeNull();

    SiteSetting::query()->update(['site_name' => 'Nama Baru']);
    Cache::forget('site_setting');

    // SiteService is request-scoped; a real request boundary drops the memo.
    $this->app->forgetScopedInstances();

    $this->get('/')->assertSuccessful()->assertSee('Nama Baru');
});
