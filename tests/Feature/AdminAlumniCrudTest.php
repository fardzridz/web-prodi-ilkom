<?php

use App\Models\Alumni;
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

$alumniPayload = fn (array $overrides = []): array => array_merge([
    'name' => 'Raka Maulana',
    'batch_year' => 2018,
    'graduation_year' => 2022,
    'job_position' => 'Web Developer',
    'company' => 'Studio Teknologi Pasuruan',
    'testimonial' => 'Pembelajaran proyek membantu membangun portofolio industri.',
    'status' => Alumni::STATUS_ACTIVE,
], $overrides);

test('admin can open alumni index and both real forms', function () use ($alumniPayload) {
    $alumni = Alumni::query()->create($alumniPayload());

    $this->get(route('admin.alumni.index'))
        ->assertOk()
        ->assertSee('Daftar Alumni')
        ->assertSee('Raka Maulana')
        ->assertSee(route('admin.alumni.create'), escape: false)
        ->assertSee(route('admin.alumni.status', $alumni), escape: false)
        ->assertSee(route('admin.alumni.destroy', $alumni), escape: false);

    $this->get(route('admin.alumni.create'))
        ->assertOk()
        ->assertSee('Tambah Alumni')
        ->assertSee('name="batch_year"', escape: false)
        ->assertSee('name="testimonial"', escape: false)
        ->assertSee('enctype="multipart/form-data"', escape: false)
        ->assertSee('name="photo"', escape: false);

    $this->get(route('admin.alumni.edit', $alumni))
        ->assertOk()
        ->assertSee('Edit Alumni')
        ->assertSee('value="Raka Maulana"', escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false)
        ->assertSee('enctype="multipart/form-data"', escape: false)
        ->assertSee('name="photo"', escape: false);
});

test('admin can create alumni with normalized optional values', function () use ($alumniPayload) {
    $this->post(route('admin.alumni.store'), $alumniPayload([
        'name' => '  Nadia Putri  ',
        'batch_year' => 2019,
        'graduation_year' => '',
        'job_position' => '  Intelligent System Specialist  ',
        'company' => '',
        'testimonial' => '  Pengalaman riset membantu pekerjaan saya.  ',
    ]) + ['photo' => UploadedFile::fake()->image('nadia.jpg', 600, 600)])
        ->assertRedirect(route('admin.alumni.index'))
        ->assertSessionHas('success', 'Data alumni berhasil ditambahkan.');

    $stored = Alumni::query()->where('name', 'Nadia Putri')->first();

    expect($stored)->not->toBeNull()
        ->and($stored->photo)->toStartWith('uploads/alumni/')
        ->and(Storage::disk('public')->exists($stored->photo))->toBeTrue();

    $this->assertDatabaseHas('alumni', [
        'name' => 'Nadia Putri',
        'batch_year' => 2019,
        'graduation_year' => null,
        'job_position' => 'Intelligent System Specialist',
        'company' => null,
        'testimonial' => 'Pengalaman riset membantu pekerjaan saya.',
        'status' => Alumni::STATUS_ACTIVE,
    ]);
});

test('alumni validation rejects invalid year relationships and status', function () use ($alumniPayload) {
    $this->post(route('admin.alumni.store'), $alumniPayload([
        'name' => '',
        'batch_year' => 1949,
        'graduation_year' => 1948,
        'status' => 'archived',
    ]))->assertSessionHasErrors(['name', 'batch_year', 'graduation_year', 'status']);

    $this->post(route('admin.alumni.store'), $alumniPayload([
        'batch_year' => 2024,
        'graduation_year' => 2023,
    ]))->assertSessionHasErrors('graduation_year');

    $this->post(route('admin.alumni.store'), $alumniPayload([
        'batch_year' => now()->addYears(2)->year,
    ]))->assertSessionHasErrors('batch_year');

    $this->assertDatabaseCount('alumni', 0);
});

test('admin can update alumni without losing existing photo path', function () use ($alumniPayload) {
    $alumni = Alumni::query()->create([
        ...$alumniPayload(),
        'photo' => 'uploads/alumni/raka.webp',
    ]);

    $this->put(route('admin.alumni.update', $alumni), $alumniPayload([
        'name' => 'Raka Maulana, S.Kom.',
        'job_position' => 'Senior Web Developer',
        'status' => Alumni::STATUS_INACTIVE,
    ]))
        ->assertRedirect(route('admin.alumni.index'))
        ->assertSessionHas('success', 'Data alumni berhasil diperbarui.');

    $alumni->refresh();

    expect($alumni->name)->toBe('Raka Maulana, S.Kom.')
        ->and($alumni->job_position)->toBe('Senior Web Developer')
        ->and($alumni->status)->toBe(Alumni::STATUS_INACTIVE)
        ->and($alumni->photo)->toBe('uploads/alumni/raka.webp');
});

test('alumni index filters search status and batch year with bounded pagination', function () {
    for ($index = 1; $index <= 11; $index++) {
        Alumni::query()->create([
            'name' => $index === 7 ? 'Aulia Safira Khusus' : 'Alumni Reguler '.$index,
            'batch_year' => $index === 7 ? 2020 : 2019,
            'graduation_year' => $index === 7 ? 2024 : 2023,
            'job_position' => $index === 7 ? 'Big Data Specialist' : 'Software Engineer',
            'company' => $index === 7 ? 'Laboratorium Data Nusantara' : 'Perusahaan Reguler',
            'testimonial' => 'Testimoni alumni.',
            'status' => $index === 7 ? Alumni::STATUS_INACTIVE : Alumni::STATUS_ACTIVE,
        ]);
    }

    $this->get(route('admin.alumni.index'))
        ->assertOk()
        ->assertViewHas('alumni', fn ($alumni): bool => $alumni->count() === 10 && $alumni->total() === 11)
        ->assertViewHas('batchYears', fn ($batchYears): bool => $batchYears->all() === [2020, 2019]);

    $this->get(route('admin.alumni.index', [
        'search' => 'Laboratorium Data',
        'status' => Alumni::STATUS_INACTIVE,
        'batch_year' => 2020,
    ]))
        ->assertOk()
        ->assertSee('Aulia Safira Khusus')
        ->assertDontSee('Alumni Reguler 1')
        ->assertViewHas('filters', fn (array $filters): bool => $filters['status'] === Alumni::STATUS_INACTIVE
            && (int) $filters['batch_year'] === 2020)
        ->assertViewHas('alumni', fn ($alumni): bool => $alumni->total() === 1);
});

test('admin can toggle alumni visibility', function () use ($alumniPayload) {
    $alumni = Alumni::query()->create($alumniPayload());

    $this->patch(route('admin.alumni.status', $alumni))
        ->assertRedirect(route('admin.alumni.index'))
        ->assertSessionHas('success', 'Status Raka Maulana berhasil diubah menjadi Nonaktif.');

    expect($alumni->fresh()->status)->toBe(Alumni::STATUS_INACTIVE);

    $this->patch(route('admin.alumni.status', $alumni))
        ->assertRedirect(route('admin.alumni.index'));

    expect($alumni->fresh()->status)->toBe(Alumni::STATUS_ACTIVE);
});

test('admin can permanently delete alumni', function () use ($alumniPayload) {
    $alumni = Alumni::query()->create([
        ...$alumniPayload(),
        'photo' => 'uploads/alumni/raka.webp',
    ]);

    Storage::disk('public')->put($alumni->photo, 'fake');

    $this->delete(route('admin.alumni.destroy', $alumni))
        ->assertRedirect(route('admin.alumni.index'))
        ->assertSessionHas('success', 'Data alumni berhasil dihapus.');

    $this->assertDatabaseMissing('alumni', ['id' => $alumni->id]);
    Storage::disk('public')->assertMissing($alumni->photo);
});

test('authenticated users without admin role cannot manage alumni', function () use ($alumniPayload) {
    $alumni = Alumni::query()->create($alumniPayload());

    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.alumni.index'))->assertForbidden();
    $this->get(route('admin.alumni.create'))->assertForbidden();
    $this->post(route('admin.alumni.store'), $alumniPayload())->assertForbidden();
    $this->get(route('admin.alumni.edit', $alumni))->assertForbidden();
    $this->put(route('admin.alumni.update', $alumni), $alumniPayload())->assertForbidden();
    $this->patch(route('admin.alumni.status', $alumni))->assertForbidden();
    $this->delete(route('admin.alumni.destroy', $alumni))->assertForbidden();

    $this->assertDatabaseHas('alumni', ['id' => $alumni->id]);
});
