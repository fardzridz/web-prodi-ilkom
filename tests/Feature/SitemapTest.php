<?php

use App\Http\Controllers\SitemapController;
use App\Models\Activity;
use App\Models\Contact;
use App\Models\HomeSection;
use App\Models\SiteSetting;
use App\Services\DashboardCache;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();

    SiteSetting::factory()->create();
    Contact::factory()->create();
    HomeSection::factory()->create();
});

test('sitemap disajikan sebagai xml dengan seluruh halaman statis', function (): void {
    $response = $this->get('/sitemap.xml')->assertSuccessful();

    expect($response->headers->get('Content-Type'))->toContain('text/xml');

    $response->assertSee('<urlset', escape: false)
        ->assertSee(route('home'), escape: false)
        ->assertSee(route('profile'), escape: false)
        ->assertSee(route('lecturers'), escape: false)
        ->assertSee(route('activities.index'), escape: false)
        ->assertSee(route('documents'), escape: false)
        ->assertSee(route('alumni'), escape: false)
        ->assertSee(route('contact'), escape: false);
});

test('sitemap memuat kegiatan terbit dan mengabaikan draf', function (): void {
    Activity::factory()->create(['slug' => 'seminar-terbit']);
    Activity::factory()->draft()->create(['slug' => 'draf-sembunyi']);

    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertSee(route('activities.show', 'seminar-terbit'), escape: false)
        ->assertDontSee(route('activities.show', 'draf-sembunyi'), escape: false);
});

test('sitemap tidak memuat entri duplikat', function (): void {
    Activity::factory()->count(3)->create();

    $urls = $this->get('/sitemap.xml')->assertSuccessful()->viewData('urls');

    expect($urls->pluck('loc')->duplicates())->toBeEmpty();
});

/**
 * Regression: the sitemap cache had no invalidation path, so a newly published
 * activity stayed invisible for up to an hour.
 */
test('cache sitemap dibersihkan saat cache kegiatan di-invalidasi', function (): void {
    Activity::factory()->create(['slug' => 'kegiatan-pertama']);

    $this->get('/sitemap.xml')->assertSuccessful();
    expect(Cache::get(SitemapController::CACHE_KEY))->not->toBeNull();

    DashboardCache::forgetActivity();

    expect(Cache::get(SitemapController::CACHE_KEY))->toBeNull();
});

test('kegiatan baru muncul di sitemap setelah cache kegiatan dibersihkan', function (): void {
    Activity::factory()->create(['slug' => 'kegiatan-lama']);

    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertDontSee(route('activities.show', 'kegiatan-baru'), escape: false);

    Activity::factory()->create(['slug' => 'kegiatan-baru']);
    DashboardCache::forgetActivity();

    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertSee(route('activities.show', 'kegiatan-baru'), escape: false);
});

test('perintah publish terjadwal membersihkan cache sitemap', function (): void {
    Activity::factory()->scheduled(now()->subHour()->toDateTimeString())->create(['slug' => 'kegiatan-terjadwal']);

    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertDontSee(route('activities.show', 'kegiatan-terjadwal'), escape: false);

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    expect(Cache::get(SitemapController::CACHE_KEY))->toBeNull();

    $this->get('/sitemap.xml')
        ->assertSuccessful()
        ->assertSee(route('activities.show', 'kegiatan-terjadwal'), escape: false);
});

test('sitemap dibatasi 100 kegiatan terbaru', function (): void {
    Activity::factory()->count(105)->create();

    $urls = $this->get('/sitemap.xml')->assertSuccessful()->viewData('urls');

    // 10 halaman statis + maksimal 100 kegiatan
    expect($urls)->toHaveCount(110);
});
