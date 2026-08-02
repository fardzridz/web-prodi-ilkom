<?php

use App\Models\HomeSection;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

test('public home renders home section content from the database', function () {
    HomeSection::query()->create([
        'hero_title' => "Logika diasah.\nKreativitas dikembangkan.",
        'hero_subtitle' => 'Subtitle beranda dari database.',
        'hero_slides' => [[
            'path' => 'uploads/home/hero-public.jpg',
            'alt' => 'Mahasiswa di laboratorium',
        ]],
        'cta_text' => 'Kenali Prodi',
        'cta_link' => '/profil',
        'welcome_title' => 'Sambutan dari Database',
        'welcome_description' => 'Deskripsi sambutan beranda dari database.',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Logika diasah.', escape: false)
        ->assertSee('<br', escape: false)
        ->assertSee('Kreativitas dikembangkan.', escape: false)
        ->assertSee('Subtitle beranda dari database.')
        ->assertSee('Kenali Prodi')
        ->assertSee(url('/profil'), escape: false)
        ->assertSee('Sambutan dari Database')
        ->assertSee('Deskripsi sambutan beranda dari database.')
        ->assertSee(asset('storage/uploads/home/hero-public.jpg'), escape: false);
});

test('public home falls back gracefully when home section is missing', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Logika diasah.', escape: false)
        ->assertSee('Tentang Ilmu Komputer')
        ->assertSee('Lihat Profil');
});

test('public home renders uploaded site logo and favicon from settings', function () {
    SiteSetting::query()->create([
        'site_name' => 'Program Studi Ilmu Komputer',
        'university_name' => 'Universitas PGRI Wiranegara',
        'faculty_name' => 'Fakultas Teknologi dan Sains',
        'logo' => 'uploads/settings/logo-upload.png',
        'favicon' => 'uploads/settings/favicon-upload.ico',
    ]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee(asset('storage/uploads/settings/logo-upload.png'), escape: false)
        ->assertSee(asset('storage/uploads/settings/favicon-upload.ico'), escape: false);
});

test('public home falls back to bundled logo and favicon when settings are empty', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee(asset('assets/images/logo/logo.png'), escape: false)
        ->assertSee(asset('assets/images/logo/logo-prodi.svg'), escape: false);
});
