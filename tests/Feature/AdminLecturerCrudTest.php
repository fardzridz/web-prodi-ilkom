<?php

use App\Models\Lecturer;
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

$lecturerPayload = fn (array $overrides = []): array => array_merge([
    'name' => 'Dr. Rina Kartika, M.Kom.',
    'nidn' => '0712048501',
    'position' => 'Ketua Program Studi',
    'expertise' => 'Sistem Cerdas, Data Mining',
    'education' => 'S3 Ilmu Komputer',
    'email' => 'rina.kartika@uniwara.ac.id',
    'bio' => 'Berfokus pada pengembangan sistem cerdas dan riset terapan.',
    'status' => Lecturer::STATUS_ACTIVE,
    'sort_order' => 1,
], $overrides);

test('admin can open the lecturer index and both real forms', function () use ($lecturerPayload) {
    $lecturer = Lecturer::query()->create($lecturerPayload());

    $this->get(route('admin.dosen.index'))
        ->assertOk()
        ->assertSee('Daftar Dosen')
        ->assertSee('Dr. Rina Kartika, M.Kom.')
        ->assertSee(route('admin.dosen.create'), escape: false)
        ->assertSee(route('admin.dosen.status', $lecturer), escape: false)
        ->assertSee(route('admin.dosen.destroy', $lecturer), escape: false);

    $this->get(route('admin.dosen.create'))
        ->assertOk()
        ->assertSee('Tambah Dosen')
        ->assertSee('name="nidn"', escape: false)
        ->assertSee('name="sort_order"', escape: false)
        ->assertSee('Task 22')
        ->assertSee('Task 23');

    $this->get(route('admin.dosen.edit', $lecturer))
        ->assertOk()
        ->assertSee('Edit Dosen')
        ->assertSee('value="0712048501"', escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('admin can create a lecturer with normalized optional values', function () use ($lecturerPayload) {
    $this->post(route('admin.dosen.store'), $lecturerPayload([
        'name' => '  Arif Wibisono, M.Kom.  ',
        'nidn' => ' 0709088802 ',
        'position' => '  Dosen Tetap  ',
        'expertise' => '',
        'education' => '',
        'email' => ' ARIF.WIBISONO@UNIWARA.AC.ID ',
        'bio' => '',
        'sort_order' => 2,
    ]) + ['photo' => UploadedFile::fake()->image('arif.jpg', 600, 600)])
        ->assertRedirect(route('admin.dosen.index'))
        ->assertSessionHas('success', 'Data dosen berhasil ditambahkan.');

    $stored = Lecturer::query()->where('nidn', '0709088802')->first();

    expect($stored)->not->toBeNull()
        ->and($stored->photo)->toStartWith('uploads/lecturers/')
        ->and(Storage::disk('public')->exists($stored->photo))->toBeTrue();

    $this->assertDatabaseHas('lecturers', [
        'name' => 'Arif Wibisono, M.Kom.',
        'nidn' => '0709088802',
        'position' => 'Dosen Tetap',
        'expertise' => null,
        'education' => null,
        'email' => 'arif.wibisono@uniwara.ac.id',
        'bio' => null,
        'status' => Lecturer::STATUS_ACTIVE,
        'sort_order' => 2,
    ]);
});

test('lecturer validation rejects malformed and duplicate identity data', function () use ($lecturerPayload) {
    Lecturer::query()->create($lecturerPayload());

    $this->post(route('admin.dosen.store'), $lecturerPayload([
        'nidn' => 'ABC-123',
        'email' => 'bukan-email',
        'status' => 'archived',
        'sort_order' => -1,
    ]))->assertSessionHasErrors(['nidn', 'email', 'status', 'sort_order']);

    $this->post(route('admin.dosen.store'), $lecturerPayload([
        'name' => 'Dosen Duplikat',
    ]))->assertSessionHasErrors(['nidn', 'email']);

    $this->assertDatabaseCount('lecturers', 1);
});

test('admin can update a lecturer without losing an existing photo path', function () use ($lecturerPayload) {
    $lecturer = Lecturer::query()->create([
        ...$lecturerPayload(),
        'photo' => 'uploads/lecturers/rina.webp',
    ]);

    $this->put(route('admin.dosen.update', $lecturer), $lecturerPayload([
        'name' => 'Dr. Rina Kartika, M.Cs.',
        'status' => Lecturer::STATUS_INACTIVE,
        'sort_order' => 9,
    ]))
        ->assertRedirect(route('admin.dosen.index'))
        ->assertSessionHas('success', 'Data dosen berhasil diperbarui.');

    $lecturer->refresh();

    expect($lecturer->name)->toBe('Dr. Rina Kartika, M.Cs.')
        ->and($lecturer->nidn)->toBe('0712048501')
        ->and($lecturer->email)->toBe('rina.kartika@uniwara.ac.id')
        ->and($lecturer->status)->toBe(Lecturer::STATUS_INACTIVE)
        ->and($lecturer->sort_order)->toBe(9)
        ->and($lecturer->photo)->toBe('uploads/lecturers/rina.webp');
});

test('lecturer index filters content and orders the lowest sort order first', function () {
    for ($index = 1; $index <= 11; $index++) {
        Lecturer::query()->create([
            'name' => $index === 7 ? 'Dosen Analitik Khusus' : 'Dosen Reguler '.$index,
            'nidn' => str_pad((string) $index, 10, '0', STR_PAD_LEFT),
            'position' => 'Dosen Tetap',
            'expertise' => $index === 7 ? 'Analitik Data' : 'Ilmu Komputer',
            'email' => "dosen{$index}@example.test",
            'status' => $index === 7 ? Lecturer::STATUS_INACTIVE : Lecturer::STATUS_ACTIVE,
            'sort_order' => 12 - $index,
        ]);
    }

    $this->get(route('admin.dosen.index'))
        ->assertOk()
        ->assertViewHas('lecturers', function ($lecturers): bool {
            return $lecturers->count() === 10
                && $lecturers->total() === 11
                && $lecturers->first()->sort_order === 1;
        });

    $this->get(route('admin.dosen.index', [
        'search' => 'Analitik Data',
        'status' => Lecturer::STATUS_INACTIVE,
    ]))
        ->assertOk()
        ->assertSee('Dosen Analitik Khusus')
        ->assertDontSee('Dosen Reguler 1')
        ->assertViewHas('filters', fn (array $filters): bool => $filters['status'] === Lecturer::STATUS_INACTIVE)
        ->assertViewHas('lecturers', fn ($lecturers): bool => $lecturers->total() === 1);
});

test('admin can toggle lecturer visibility from the index', function () use ($lecturerPayload) {
    $lecturer = Lecturer::query()->create($lecturerPayload());

    $this->patch(route('admin.dosen.status', $lecturer))
        ->assertRedirect(route('admin.dosen.index'))
        ->assertSessionHas('success', 'Status Dr. Rina Kartika, M.Kom. berhasil diubah menjadi Nonaktif.');

    expect($lecturer->fresh()->status)->toBe(Lecturer::STATUS_INACTIVE);

    $this->patch(route('admin.dosen.status', $lecturer))
        ->assertRedirect(route('admin.dosen.index'));

    expect($lecturer->fresh()->status)->toBe(Lecturer::STATUS_ACTIVE);
});

test('admin can permanently delete a lecturer', function () use ($lecturerPayload) {
    $lecturer = Lecturer::query()->create($lecturerPayload());

    $this->delete(route('admin.dosen.destroy', $lecturer))
        ->assertRedirect(route('admin.dosen.index'))
        ->assertSessionHas('success', 'Data dosen berhasil dihapus.');

    $this->assertDatabaseMissing('lecturers', ['id' => $lecturer->id]);
});

test('authenticated users without the admin role cannot access lecturer management', function () use ($lecturerPayload) {
    $lecturer = Lecturer::query()->create($lecturerPayload());

    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.dosen.index'))->assertForbidden();
    $this->get(route('admin.dosen.create'))->assertForbidden();
    $this->post(route('admin.dosen.store'), $lecturerPayload(['nidn' => '12345678']))->assertForbidden();
    $this->get(route('admin.dosen.edit', $lecturer))->assertForbidden();
    $this->put(route('admin.dosen.update', $lecturer), $lecturerPayload())->assertForbidden();
    $this->patch(route('admin.dosen.status', $lecturer))->assertForbidden();
    $this->delete(route('admin.dosen.destroy', $lecturer))->assertForbidden();

    $this->assertDatabaseHas('lecturers', ['id' => $lecturer->id]);
});
