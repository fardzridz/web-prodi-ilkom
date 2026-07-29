<?php

use App\Models\Contact;
use App\Models\DocumentCategory;
use App\Models\HomeSection;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->seed(DatabaseSeeder::class);
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

test('database seeder creates the complete documented initial data', function () {
    $admin = User::query()->sole();
    $siteSetting = SiteSetting::query()->sole();
    $homeSection = HomeSection::query()->sole();
    $programProfile = ProgramProfile::query()->sole();
    $contact = Contact::query()->sole();

    expect($admin->email)->toBe('admin@uniwara.ac.id')
        ->and($admin->role)->toBe(User::ROLE_ADMIN)
        ->and($admin->email_verified_at)->not->toBeNull()
        ->and(Hash::check((string) config('initial-data.admin.password'), $admin->password))->toBeTrue()
        ->and($siteSetting->site_name)->toBe('Program Studi Ilmu Komputer')
        ->and($siteSetting->university_name)->toBe('Universitas PGRI Wiranegara')
        ->and($siteSetting->footer_academic_links)->toHaveCount(5)
        ->and($homeSection->hero_title)->toBe("Logika diasah.\nKreativitas dikembangkan.\nMasa depan diciptakan.")
        ->and($homeSection->hero_slides)->toBeArray()
        ->and($homeSection->cta_link)->toBe('/profil')
        ->and($programProfile->accreditation)->toBe('Baik Sekali')
        ->and($programProfile->mission)->toContain('Menyelenggarakan pendidikan Ilmu Komputer')
        ->and($contact->address)->toContain('Pasuruan')
        ->and($contact->email)->toBe('univ.pgriwiranegara@gmail.com')
        ->and(DocumentCategory::query()->orderBy('id')->pluck('slug')->all())->toBe([
            'kurikulum',
            'panduan',
            'akreditasi',
            'sop',
        ]);
});

test('database seeder can be rerun without duplicates or overwriting cms edits', function () {
    $admin = User::query()->sole();
    $siteSetting = SiteSetting::query()->sole();
    $category = DocumentCategory::query()->where('slug', 'kurikulum')->sole();

    $admin->update([
        'name' => 'Admin Disunting',
        'password' => 'rahasia-baru',
    ]);
    $siteSetting->update(['site_name' => 'Nama Situs Disunting']);
    $category->update(['name' => 'Kurikulum Disunting']);

    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())->toBe(1)
        ->and(SiteSetting::query()->count())->toBe(1)
        ->and(HomeSection::query()->count())->toBe(1)
        ->and(ProgramProfile::query()->count())->toBe(1)
        ->and(Contact::query()->count())->toBe(1)
        ->and(DocumentCategory::query()->count())->toBe(4)
        ->and($admin->fresh()->name)->toBe('Admin Disunting')
        ->and(Hash::check('rahasia-baru', $admin->fresh()->password))->toBeTrue()
        ->and($siteSetting->fresh()->site_name)->toBe('Nama Situs Disunting')
        ->and($category->fresh()->name)->toBe('Kurikulum Disunting');
});

test('admin seeder rejects an empty initial password', function () {
    config()->set('initial-data.admin.password', '');

    expect(fn () => $this->seed(AdminUserSeeder::class))
        ->toThrow(RuntimeException::class, 'INITIAL_ADMIN_PASSWORD must not be empty.');
});
