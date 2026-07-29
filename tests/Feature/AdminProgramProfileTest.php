<?php

use App\Models\ProgramProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
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

$profilePayload = fn (array $overrides = []): array => array_merge([
    'history' => 'Sejarah Program Studi Ilmu Komputer.',
    'description' => 'Program studi menyiapkan lulusan yang kompeten dan beretika.',
    'vision' => 'Menjadi program studi unggul dalam pengembangan teknologi informasi.',
    'mission' => "1. Menyelenggarakan pendidikan bermutu.\n2. Mengembangkan penelitian terapan.",
    'goals' => "1. Menghasilkan lulusan kompeten.\n2. Menghasilkan karya yang bermanfaat.",
    'accreditation' => 'Baik Sekali',
    'advantages' => 'Pembelajaran berbasis proyek dan sertifikasi kompetensi.',
], $overrides);

test('admin can open the real single record profile editor', function () use ($profilePayload) {
    ProgramProfile::query()->create($profilePayload());

    $this->get(route('admin.profil'))
        ->assertOk()
        ->assertSee('Profil Prodi')
        ->assertSee('Editor Konten Profil')
        ->assertSee('name="description"', escape: false)
        ->assertSee('name="history"', escape: false)
        ->assertSee('name="vision"', escape: false)
        ->assertSee('name="mission"', escape: false)
        ->assertSee('name="goals"', escape: false)
        ->assertSee('name="accreditation"', escape: false)
        ->assertSee('name="advantages"', escape: false)
        ->assertSee(route('admin.profil.update'), escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('admin can update the program profile with normalized values', function () use ($profilePayload) {
    ProgramProfile::query()->create($profilePayload());

    $this->put(route('admin.profil.update'), $profilePayload([
        'history' => '  Sejarah diperbarui.  ',
        'description' => '  Deskripsi diperbarui.  ',
        'vision' => '  Visi diperbarui.  ',
        'mission' => "  1. Misi pertama.\n2. Misi kedua.  ",
        'goals' => '  Tujuan diperbarui.  ',
        'accreditation' => '  Unggul  ',
        'advantages' => '  Keunggulan diperbarui.  ',
    ]))
        ->assertRedirect(route('admin.profil'))
        ->assertSessionHas('success', 'Profil program studi berhasil diperbarui.');

    $this->assertDatabaseHas('program_profiles', [
        'history' => 'Sejarah diperbarui.',
        'description' => 'Deskripsi diperbarui.',
        'vision' => 'Visi diperbarui.',
        'mission' => "1. Misi pertama.\n2. Misi kedua.",
        'goals' => 'Tujuan diperbarui.',
        'accreditation' => 'Unggul',
        'advantages' => 'Keunggulan diperbarui.',
    ]);
    $this->assertDatabaseCount('program_profiles', 1);
});

test('first and repeated updates keep only one singleton profile row', function () use ($profilePayload) {
    expect(ProgramProfile::query()->count())->toBe(0);

    $this->put(route('admin.profil.update'), $profilePayload())
        ->assertRedirect(route('admin.profil'));

    $this->put(route('admin.profil.update'), $profilePayload([
        'accreditation' => 'Unggul',
    ]))->assertRedirect(route('admin.profil'));

    $this->assertDatabaseCount('program_profiles', 1);
    $this->assertDatabaseHas('program_profiles', [
        'accreditation' => 'Unggul',
    ]);
});

test('profile validation rejects empty and oversized content without changing data', function () use ($profilePayload) {
    $profile = ProgramProfile::query()->create($profilePayload());

    $this->put(route('admin.profil.update'), $profilePayload([
        'history' => '',
        'description' => '',
        'vision' => str_repeat('v', 5001),
        'mission' => '',
        'goals' => '',
        'accreditation' => str_repeat('a', 256),
        'advantages' => '',
    ]))->assertSessionHasErrors([
        'history',
        'description',
        'vision',
        'mission',
        'goals',
        'accreditation',
        'advantages',
    ]);

    expect($profile->fresh()->vision)->toBe($profilePayload()['vision'])
        ->and($profile->fresh()->accreditation)->toBe('Baik Sekali');
});

test('authenticated users without admin role cannot edit the program profile', function () use ($profilePayload) {
    ProgramProfile::query()->create($profilePayload());
    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.profil'))->assertForbidden();
    $this->put(route('admin.profil.update'), $profilePayload([
        'accreditation' => 'Tidak boleh berubah',
    ]))->assertForbidden();

    $this->assertDatabaseHas('program_profiles', [
        'accreditation' => 'Baik Sekali',
    ]);
});
