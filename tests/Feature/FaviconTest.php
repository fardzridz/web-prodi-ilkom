<?php

use App\Models\Contact;
use App\Models\HomeSection;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();

    SiteSetting::factory()->create(['favicon' => null]);
    Contact::factory()->create();
    HomeSection::factory()->create();
    ProgramProfile::factory()->create();
});

/**
 * Regression: error-bg.webp was referenced by six views while only the .gif
 * existed on disk, so the asset silently 404'd. Icons are just as easy to
 * reference without shipping, hence this guard.
 */
test('seluruh berkas ikon yang dirujuk tersedia di direktori public', function (string $file): void {
    expect(public_path($file))->toBeFile();
})->with([
    'favicon.ico',
    'favicon.svg',
    'favicon-96x96.png',
    'apple-touch-icon.png',
    'web-app-manifest-192x192.png',
    'web-app-manifest-512x512.png',
    'site.webmanifest',
]);

test('halaman publik memuat rangkaian ikon lengkap dan manifest', function (): void {
    $this->get('/')
        ->assertSuccessful()
        ->assertSee('href="'.asset('favicon-96x96.png').'"', escape: false)
        ->assertSee('href="'.asset('favicon.svg').'"', escape: false)
        ->assertSee('href="'.asset('favicon.ico').'"', escape: false)
        ->assertSee('href="'.asset('apple-touch-icon.png').'"', escape: false)
        ->assertSee('href="'.asset('site.webmanifest').'"', escape: false)
        ->assertSee('name="theme-color"', escape: false);
});

test('favicon unggahan pengelola menggantikan ikon bawaan', function (): void {
    SiteSetting::query()->delete();
    Cache::flush();
    SiteSetting::factory()->create(['favicon' => 'settings/favicon-kustom.png']);

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('href="'.asset('storage/settings/favicon-kustom.png').'"', escape: false)
        ->assertDontSee('href="'.asset('favicon-96x96.png').'"', escape: false);
});

test('site.webmanifest berisi identitas prodi dan dua ikon maskable', function (): void {
    $manifest = json_decode((string) file_get_contents(public_path('site.webmanifest')), true);

    expect($manifest['name'])->toContain('Ilmu Komputer')
        ->and($manifest['lang'])->toBe('id-ID')
        ->and($manifest['start_url'])->toBe('/')
        ->and($manifest['icons'])->toHaveCount(2);

    foreach ($manifest['icons'] as $icon) {
        expect(public_path(ltrim((string) $icon['src'], '/')))->toBeFile();
    }
});

test('favicon svg tetap ringan agar tidak membebani permintaan pertama', function (): void {
    expect(filesize(public_path('favicon.svg')))->toBeLessThan(100 * 1024);
});
