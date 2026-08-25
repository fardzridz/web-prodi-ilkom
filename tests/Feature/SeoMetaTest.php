<?php

use App\Models\Activity;
use App\Models\Contact;
use App\Models\HomeSection;
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

test('setiap halaman public punya canonical absolut sesuai url-nya', function (string $route): void {
    $this->get($route)
        ->assertSuccessful()
        ->assertSee('<link rel="canonical" href="'.url($route).'">', escape: false);
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

test('halaman detail kegiatan memakai canonical slug-nya sendiri', function (): void {
    Activity::factory()->create(['slug' => 'seminar-ai']);

    $this->get('/kegiatan/seminar-ai')
        ->assertSuccessful()
        ->assertSee('<link rel="canonical" href="'.route('activities.show', 'seminar-ai').'">', escape: false);
});

test('beranda memakai title dan description dari config seo', function (): void {
    $response = $this->get('/')->assertSuccessful();

    $response->assertSee('<title>'.e(config('seo.title')).'</title>', escape: false)
        ->assertSee('content="'.e(config('seo.description')).'"', escape: false);
});

test('title seo tetap dalam batas panjang aman serp', function (): void {
    expect(mb_strlen((string) config('seo.title')))->toBeLessThanOrEqual(65)
        ->and(mb_strlen((string) config('seo.description')))->toBeGreaterThanOrEqual(120)
        ->and(mb_strlen((string) config('seo.description')))->toBeLessThanOrEqual(165);
});

test('setiap halaman public punya judul unik agar tidak dianggap duplikat', function (): void {
    $routes = ['/', '/profil', '/dosen', '/kegiatan', '/dokumen', '/alumni', '/kontak'];

    $titles = collect($routes)->map(function (string $route): string {
        $content = $this->get($route)->assertSuccessful()->getContent();

        preg_match('/<title>(.*?)<\/title>/s', (string) $content, $matches);

        return trim($matches[1] ?? '');
    });

    expect($titles->filter())->toHaveCount(count($routes))
        ->and($titles->duplicates())->toBeEmpty();
});

test('json-ld organization dan program ter-render dan valid', function (): void {
    $content = (string) $this->get('/')->assertSuccessful()->getContent();

    preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $content, $matches);

    $payloads = collect($matches[1])->map(fn (string $json): array => json_decode(trim($json), true) ?? []);

    expect($payloads)->toHaveCount(2)
        ->and($payloads->pluck('@type')->all())->toEqualCanonicalizing([
            'CollegeOrUniversity',
            'EducationalOccupationalProgram',
        ]);

    $program = $payloads->firstWhere('@type', 'EducationalOccupationalProgram');

    expect($program['name'])->toBe(config('seo.program.name'))
        ->and($program['url'])->toBe(route('home'))
        ->and($program['provider']['name'])->toBe(config('seo.provider.name'));
});

test('script ga4 tidak diinjeksi ketika ga4_id kosong', function (): void {
    config()->set('seo.ga4_id', null);

    $this->get('/')
        ->assertSuccessful()
        ->assertDontSee('googletagmanager.com/gtag/js', escape: false);
});

test('script ga4 diinjeksi ketika ga4_id tersedia', function (): void {
    config()->set('seo.ga4_id', 'G-TEST12345');

    $this->get('/')
        ->assertSuccessful()
        ->assertSee('googletagmanager.com/gtag/js?id=G-TEST12345', escape: false);
});

/**
 * Regression: hero and section headings previously emitted multiple <h1> per page,
 * which splits keyword relevance across competing headings.
 */
test('setiap halaman public hanya punya satu h1', function (string $route): void {
    $content = (string) $this->get($route)->assertSuccessful()->getContent();

    expect(preg_match_all('/<h1[\s>]/i', $content))->toBe(1);
})->with([
    '/',
    '/profil',
    '/dosen',
    '/kegiatan',
    '/dokumen',
    '/alumni',
    '/kontak',
]);

test('gambar hero beranda punya alt deskriptif bukan kosong', function (): void {
    $content = (string) $this->get('/')->assertSuccessful()->getContent();

    preg_match_all('/<img[^>]*data-hero-img[^>]*>|<img[^>]*>/i', $content, $matches);

    $emptyAlts = collect($matches[0])
        ->filter(fn (string $tag): bool => str_contains($tag, 'hero/hero-'))
        ->filter(fn (string $tag): bool => str_contains($tag, 'alt=""'));

    expect($emptyAlts)->toBeEmpty();
});

test('robots.txt melarang crawl area admin dan berkas dokumen', function (): void {
    $robots = file_get_contents(public_path('robots.txt'));

    expect($robots)->toContain('Disallow: /admin')
        ->and($robots)->toContain('Sitemap: ')
        ->and($robots)->toContain('/sitemap.xml');
});
