<?php

use App\Http\Controllers\SitemapController;
use App\Models\Activity;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
});

test('kegiatan terjadwal yang waktunya tiba menjadi terbit', function (): void {
    $activity = Activity::factory()->scheduled(now()->subMinute()->toDateTimeString())->create();

    $this->artisan('activities:publish-scheduled')
        ->expectsOutputToContain('1 kegiatan terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    expect($activity->refresh()->status)->toBe(Activity::STATUS_PUBLISHED);
});

test('kegiatan terjadwal yang belum waktunya tetap terjadwal', function (): void {
    $activity = Activity::factory()->scheduled(now()->addDay()->toDateTimeString())->create();

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    expect($activity->refresh()->status)->toBe(Activity::STATUS_SCHEDULED);
});

test('kegiatan tepat pada detik jadwalnya ikut diterbitkan', function (): void {
    $activity = Activity::factory()->scheduled(now()->toDateTimeString())->create();

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    expect($activity->refresh()->status)->toBe(Activity::STATUS_PUBLISHED);
});

test('draf tidak ikut terbit meski punya jadwal tayang', function (): void {
    $activity = Activity::factory()->draft()->create(['published_at' => now()->subDay()]);

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    expect($activity->refresh()->status)->toBe(Activity::STATUS_DRAFT);
});

/**
 * Regression guard: a scheduled row without published_at must never be picked
 * up, otherwise a half-filled record would publish itself immediately.
 */
test('kegiatan terjadwal tanpa jadwal tayang tidak diterbitkan', function (): void {
    $activity = Activity::factory()->scheduled()->create(['published_at' => null]);

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    expect($activity->refresh()->status)->toBe(Activity::STATUS_SCHEDULED);
});

test('perintah tetap sukses ketika tidak ada yang perlu diterbitkan', function (): void {
    $this->artisan('activities:publish-scheduled')
        ->expectsOutputToContain('0 kegiatan terjadwal berhasil diterbitkan.')
        ->assertSuccessful();
});

test('kegiatan yang baru terbit langsung tampil di halaman publik', function (): void {
    $activity = Activity::factory()->scheduled(now()->subMinute()->toDateTimeString())
        ->create(['title' => 'Seminar Terjadwal Otomatis']);

    $this->get('/kegiatan')->assertSuccessful()->assertDontSee('Seminar Terjadwal Otomatis');

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    $this->get('/kegiatan')->assertSuccessful()->assertSee('Seminar Terjadwal Otomatis');
    $this->get('/kegiatan/'.$activity->slug)->assertSuccessful();
});

test('cache kegiatan dibersihkan hanya bila ada yang terbit', function (): void {
    Activity::factory()->scheduled(now()->addDay()->toDateTimeString())->create();

    Cache::put('public:activity_categories', ['stale'], 3600);
    $this->artisan('activities:publish-scheduled')->assertSuccessful();
    expect(Cache::get('public:activity_categories'))->toBe(['stale']);

    Activity::factory()->scheduled(now()->subMinute()->toDateTimeString())->create();
    $this->artisan('activities:publish-scheduled')->assertSuccessful();
    expect(Cache::get('public:activity_categories'))->toBeNull();
});

test('beberapa kegiatan terjadwal diterbitkan sekaligus dalam satu jalannya', function (): void {
    Activity::factory()->count(3)->scheduled(now()->subMinute()->toDateTimeString())->create();
    Activity::factory()->count(2)->scheduled(now()->addDay()->toDateTimeString())->create();

    $this->artisan('activities:publish-scheduled')
        ->expectsOutputToContain('3 kegiatan terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    expect(Activity::query()->where('status', Activity::STATUS_PUBLISHED)->count())->toBe(3)
        ->and(Activity::query()->where('status', Activity::STATUS_SCHEDULED)->count())->toBe(2);
});

test('menjalankan perintah dua kali tidak mengubah kegiatan yang sudah terbit', function (): void {
    $activity = Activity::factory()->scheduled(now()->subMinute()->toDateTimeString())->create();

    $this->artisan('activities:publish-scheduled')->assertSuccessful();
    $publishedAt = $activity->refresh()->published_at;

    $this->artisan('activities:publish-scheduled')
        ->expectsOutputToContain('0 kegiatan terjadwal berhasil diterbitkan.')
        ->assertSuccessful();

    expect($activity->refresh()->published_at->eq($publishedAt))->toBeTrue();
});

test('perintah terdaftar pada penjadwal untuk berjalan tiap menit', function (): void {
    $schedule = app(Schedule::class);

    $event = collect($schedule->events())
        ->first(fn ($event): bool => str_contains((string) $event->command, 'activities:publish-scheduled'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('* * * * *');
});

test('cache sitemap ikut dibersihkan agar kegiatan baru terindeks', function (): void {
    Activity::factory()->scheduled(now()->subMinute()->toDateTimeString())->create();

    Cache::put(SitemapController::CACHE_KEY, collect(), 3600);

    $this->artisan('activities:publish-scheduled')->assertSuccessful();

    expect(Cache::get(SitemapController::CACHE_KEY))->toBeNull();
});
