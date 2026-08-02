<?php

use App\Models\HomeSection;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        Storage::fake('public');

        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($this->admin);
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

$homePayload = fn (array $overrides = []): array => array_merge([
    'hero_title' => 'Masa depan dibangun bersama.',
    'hero_subtitle' => 'Program studi menghadirkan pembelajaran teknologi yang relevan.',
    'cta_text' => 'Lihat Profil',
    'cta_link' => '/profil',
    'welcome_title' => 'Tentang Ilmu Komputer',
    'welcome_description' => 'Mahasiswa belajar membangun solusi yang bermanfaat.',
    'slides' => [],
], $overrides);

$settingPayload = fn (array $overrides = []): array => array_merge([
    'site_name' => 'Program Studi Ilmu Komputer',
    'university_name' => 'Universitas PGRI Wiranegara',
    'faculty_name' => 'Fakultas Teknologi dan Sains',
    'journal_url' => 'https://ejurnal.uniwara.ac.id',
    'footer_text' => '© 2026 Program Studi Ilmu Komputer.',
    'footer_links' => [
        ['label' => 'Wiraakademik', 'url' => 'https://wiraakademik.uniwara.ac.id'],
    ],
], $overrides);

$homeRecordPayload = fn (array $overrides = []): array => array_merge([
    'hero_title' => 'Masa depan dibangun bersama.',
    'hero_subtitle' => 'Program studi menghadirkan pembelajaran teknologi yang relevan.',
    'hero_slides' => [],
    'cta_text' => 'Lihat Profil',
    'cta_link' => '/profil',
    'welcome_title' => 'Tentang Ilmu Komputer',
    'welcome_description' => 'Mahasiswa belajar membangun solusi yang bermanfaat.',
], $overrides);

$settingRecordPayload = fn (array $overrides = []): array => array_merge([
    'site_name' => 'Program Studi Ilmu Komputer',
    'university_name' => 'Universitas PGRI Wiranegara',
    'faculty_name' => 'Fakultas Teknologi dan Sains',
    'journal_url' => 'https://ejurnal.uniwara.ac.id',
    'footer_text' => '© 2026 Program Studi Ilmu Komputer.',
    'footer_academic_links' => [],
], $overrides);

test('admin can open the real home settings editor', function () use ($homeRecordPayload, $settingRecordPayload) {
    HomeSection::query()->create($homeRecordPayload());
    SiteSetting::query()->create($settingRecordPayload());

    $this->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('Editor Beranda')
        ->assertSee('name="hero_title"', escape: false)
        ->assertSee('name="welcome_description"', escape: false)
        ->assertSee(route('admin.beranda.update'), escape: false)
        ->assertSee('enctype="multipart/form-data"', escape: false);

    $this->get(route('admin.pengaturan'))
        ->assertOk()
        ->assertSee('Identitas Website')
        ->assertSee('name="site_name"', escape: false)
        ->assertSee('name="logo"', escape: false)
        ->assertSee('name="favicon"', escape: false)
        ->assertSee(route('admin.pengaturan.update'), escape: false);
});

test('admin can update singleton home content and upload hero slides', function () use ($homePayload) {
    $response = $this->put(route('admin.beranda.update'), $homePayload([
        'hero_title' => "  Logika diasah.\r\nKreativitas dikembangkan.\r\nMasa depan diciptakan.  ",
        'slides' => [[
            'alt' => '  Mahasiswa belajar di laboratorium  ',
            'file' => UploadedFile::fake()->image('hero-lab.jpg', 1200, 700),
        ]],
    ]));

    $response
        ->assertRedirect(route('admin.beranda'))
        ->assertSessionHas('success', 'Konten beranda berhasil diperbarui.');

    $homeSection = HomeSection::query()->sole();

    expect($homeSection->hero_title)->toBe("Logika diasah.\nKreativitas dikembangkan.\nMasa depan diciptakan.")
        ->and($homeSection->hero_slides)->toHaveCount(1)
        ->and($homeSection->hero_slides[0]['alt'])->toBe('Mahasiswa belajar di laboratorium');

    Storage::disk('public')->assertExists($homeSection->hero_slides[0]['path']);

    $this->put(route('admin.beranda.update'), $homePayload([
        'hero_title' => 'Pembaruan kedua',
        'slides' => [[
            'existing_path' => $homeSection->hero_slides[0]['path'],
            'alt' => 'Slide tetap tersimpan',
        ]],
    ]))->assertRedirect(route('admin.beranda'));

    $this->assertDatabaseCount('home_sections', 1);
    Storage::disk('public')->assertExists($homeSection->hero_slides[0]['path']);
});

test('admin beranda editor uses a multiline hero title field', function () use ($homeRecordPayload) {
    HomeSection::query()->create($homeRecordPayload([
        'hero_title' => "Baris satu.\nBaris dua.",
    ]));

    $this->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('name="hero_title"', escape: false)
        ->assertSee('<textarea id="home-hero-title"', escape: false)
        ->assertSee('Tekan Enter untuk baris baru')
        ->assertSee('Baris satu.', escape: false)
        ->assertSee('Baris dua.', escape: false)
        ->assertSee('<br', escape: false);
});

test('admin beranda preview card shows content summary and public link', function () use ($homeRecordPayload) {
    HomeSection::query()->create($homeRecordPayload([
        'hero_title' => 'Judul Pratinjau Beranda',
        'hero_subtitle' => 'Subtitle pratinjau beranda.',
        'cta_text' => 'Tombol Pratinjau',
        'welcome_title' => 'Sambutan Pratinjau',
        'welcome_description' => 'Isi sambutan untuk pratinjau.',
        'hero_slides' => [[
            'path' => 'uploads/home/preview.jpg',
            'alt' => 'Slide pratinjau',
        ]],
    ]));

    $this->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('Pratinjau Konten')
        ->assertSee('data-home-preview', escape: false)
        ->assertSee('Judul Pratinjau Beranda')
        ->assertSee('Subtitle pratinjau beranda.')
        ->assertSee('Tombol Pratinjau')
        ->assertSee('Sambutan Pratinjau')
        ->assertSee('Isi sambutan untuk pratinjau.')
        ->assertSee('Buka beranda')
        ->assertSee(route('home'), escape: false)
        ->assertSee(asset('storage/uploads/home/preview.jpg'), escape: false);
});

test('admin beranda uses trash icon for slides instead of remove checkbox', function () use ($homeRecordPayload) {
    HomeSection::query()->create($homeRecordPayload([
        'hero_slides' => [[
            'path' => 'uploads/home/preview.jpg',
            'alt' => 'Slide pratinjau',
        ]],
    ]));

    $this->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('data-slide-remove', escape: false)
        ->assertSee('aria-label="Hapus slide"', escape: false)
        ->assertDontSee('Hapus slide saat disimpan')
        ->assertDontSee('content-remove-check', escape: false);
});

test('admin can mark a stored hero slide for deletion when saving', function () use ($homePayload, $homeRecordPayload) {
    Storage::disk('public')->put('uploads/home/delete-me.jpg', 'delete-me');
    HomeSection::query()->create($homeRecordPayload([
        'hero_slides' => [[
            'path' => 'uploads/home/delete-me.jpg',
            'alt' => 'Slide dihapus',
        ]],
    ]));

    $this->put(route('admin.beranda.update'), $homePayload([
        'slides' => [[
            'existing_path' => 'uploads/home/delete-me.jpg',
            'alt' => 'Slide dihapus',
            'remove' => true,
        ]],
    ]))
        ->assertRedirect(route('admin.beranda'))
        ->assertSessionHas('success', 'Konten beranda berhasil diperbarui.');

    expect(HomeSection::query()->sole()->hero_slides)->toBe([]);
    Storage::disk('public')->assertMissing('uploads/home/delete-me.jpg');
});

test('replacing a hero slide removes the old file only after a successful update', function () use ($homePayload, $homeRecordPayload) {
    Storage::disk('public')->put('uploads/home/old.jpg', 'old-image');
    HomeSection::query()->create($homeRecordPayload([
        'hero_slides' => [[
            'path' => 'uploads/home/old.jpg',
            'alt' => 'Slide lama',
        ]],
    ]));

    $this->put(route('admin.beranda.update'), $homePayload([
        'slides' => [[
            'existing_path' => 'uploads/home/old.jpg',
            'alt' => 'Slide baru',
            'file' => UploadedFile::fake()->image('new-slide.png', 1200, 700),
        ]],
    ]))->assertRedirect(route('admin.beranda'));

    $newPath = HomeSection::query()->sole()->hero_slides[0]['path'];

    Storage::disk('public')->assertMissing('uploads/home/old.jpg');
    Storage::disk('public')->assertExists($newPath);
});

test('admin can update singleton site settings logo favicon and footer links', function () use ($settingPayload, $settingRecordPayload) {
    Storage::disk('public')->put('uploads/settings/old-logo.png', 'old-logo');
    Storage::disk('public')->put('uploads/settings/old-favicon.ico', 'old-favicon');
    SiteSetting::query()->create($settingRecordPayload([
        'logo' => 'uploads/settings/old-logo.png',
        'favicon' => 'uploads/settings/old-favicon.ico',
    ]));

    $this->put(route('admin.pengaturan.update'), $settingPayload([
        'site_name' => '  Website Prodi Data Uji  ',
        'footer_text' => '  Footer diperbarui.  ',
        'footer_links' => [[
            'label' => '  Portal Akademik  ',
            'url' => '  https://akademik.example.ac.id  ',
        ]],
        'logo' => UploadedFile::fake()->image('logo-baru.png', 500, 160),
        'favicon' => UploadedFile::fake()->create('favicon-baru.ico', 32, 'image/x-icon'),
    ]))
        ->assertRedirect(route('admin.pengaturan'))
        ->assertSessionHas('success', 'Pengaturan website berhasil diperbarui.');

    $siteSetting = SiteSetting::query()->sole();

    expect($siteSetting->site_name)->toBe('Website Prodi Data Uji')
        ->and($siteSetting->footer_text)->toBe('Footer diperbarui.')
        ->and($siteSetting->footer_academic_links)->toBe([[
            'url' => 'https://akademik.example.ac.id',
            'label' => 'Portal Akademik',
        ]]);

    Storage::disk('public')->assertMissing('uploads/settings/old-logo.png');
    Storage::disk('public')->assertMissing('uploads/settings/old-favicon.ico');
    Storage::disk('public')->assertExists($siteSetting->logo);
    Storage::disk('public')->assertExists($siteSetting->favicon);
    $this->assertDatabaseCount('site_settings', 1);
});

test('home and site setting validation rejects unsafe or invalid input', function () use ($homePayload, $settingPayload, $homeRecordPayload, $settingRecordPayload) {
    $homeSection = HomeSection::query()->create($homeRecordPayload());
    $siteSetting = SiteSetting::query()->create($settingRecordPayload());

    $this->put(route('admin.beranda.update'), $homePayload([
        'hero_title' => '',
        'cta_link' => 'javascript:alert(1)',
        'slides' => [[
            'alt' => 'Bukan gambar',
            'file' => UploadedFile::fake()->create('payload.txt', 20, 'text/plain'),
        ]],
    ]))->assertSessionHasErrors(['hero_title', 'cta_link', 'slides.0.file']);

    $this->put(route('admin.pengaturan.update'), $settingPayload([
        'site_name' => '',
        'journal_url' => 'ftp://unsafe.example.test',
        'logo' => UploadedFile::fake()->create('logo.svg', 20, 'image/svg+xml'),
        'favicon' => UploadedFile::fake()->create('favicon.exe', 20, 'application/octet-stream'),
    ]))->assertSessionHasErrors(['site_name', 'journal_url', 'logo', 'favicon']);

    expect($homeSection->fresh()->hero_title)->not->toBe('')
        ->and($siteSetting->fresh()->site_name)->not->toBe('');
});

test('authenticated users without admin role cannot manage home or site settings', function () use ($homePayload, $settingPayload) {
    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.beranda'))->assertForbidden();
    $this->put(route('admin.beranda.update'), $homePayload())->assertForbidden();
    $this->get(route('admin.pengaturan'))->assertForbidden();
    $this->put(route('admin.pengaturan.update'), $settingPayload())->assertForbidden();
});
