<?php

use App\Models\Activity;
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

$activityPayload = fn (array $overrides = []): array => array_merge([
    'title' => 'Seminar Kecerdasan Buatan',
    'slug' => 'seminar-kecerdasan-buatan',
    'excerpt' => 'Seminar untuk mahasiswa Ilmu Komputer.',
    'content' => 'Isi lengkap kegiatan seminar kecerdasan buatan.',
    'activity_date' => '2026-08-20',
    'location' => 'Aula Kampus',
    'status' => Activity::STATUS_DRAFT,
    'published_at' => null,
], $overrides);

test('admin can open the activity index and both real forms', function () {
    $activity = Activity::query()->create([
        'user_id' => $this->admin->id,
        'title' => 'Kuliah Umum',
        'slug' => 'kuliah-umum',
        'content' => 'Isi kuliah umum.',
        'activity_date' => '2026-08-21',
        'location' => 'Auditorium',
        'status' => Activity::STATUS_DRAFT,
    ]);

    $this->get(route('admin.kegiatan.index'))
        ->assertOk()
        ->assertSee('Daftar Kegiatan')
        ->assertSee('Kuliah Umum')
        ->assertSee('id="activity-filter-date"', escape: false)
        ->assertSee('data-admin-date-picker', escape: false)
        ->assertSee(route('admin.kegiatan.create'), escape: false)
        ->assertSee(route('admin.kegiatan.destroy', $activity), escape: false);

    $this->get(route('admin.kegiatan.create'))
        ->assertOk()
        ->assertSee('Tambah Kegiatan')
        ->assertSee('name="content"', escape: false)
        ->assertSee('id="activity-date"', escape: false)
        ->assertSee('id="activity-published-at"', escape: false)
        ->assertSee('Task 22')
        ->assertSee('Task 23');

    $this->get(route('admin.kegiatan.edit', $activity))
        ->assertOk()
        ->assertSee('Edit Kegiatan')
        ->assertSee('value="Kuliah Umum"', escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('admin can create a draft with a normalized slug and ownership', function () use ($activityPayload) {
    $response = $this->post(route('admin.kegiatan.store'), $activityPayload([
        'title' => '  Seminar Keamanan Siber  ',
        'slug' => '',
        'excerpt' => '  Ringkasan seminar.  ',
        'location' => '  Lab Komputer  ',
    ]));

    $response
        ->assertRedirect(route('admin.kegiatan.index'))
        ->assertSessionHas('success', 'Kegiatan berhasil ditambahkan.');

    $this->assertDatabaseHas('activities', [
        'user_id' => $this->admin->id,
        'title' => 'Seminar Keamanan Siber',
        'slug' => 'seminar-keamanan-siber',
        'excerpt' => 'Ringkasan seminar.',
        'location' => 'Lab Komputer',
        'status' => Activity::STATUS_DRAFT,
        'published_at' => null,
    ]);
});

test('scheduled activities require a unique valid future publication time', function () use ($activityPayload) {
    Activity::query()->create([
        'user_id' => $this->admin->id,
        ...$activityPayload(),
    ]);

    $this->post(route('admin.kegiatan.store'), $activityPayload([
        'slug' => 'jadwal-tanpa-waktu',
        'status' => Activity::STATUS_SCHEDULED,
    ]))->assertSessionHasErrors('published_at');

    $this->post(route('admin.kegiatan.store'), $activityPayload([
        'slug' => 'jadwal-lampau',
        'status' => Activity::STATUS_SCHEDULED,
        'published_at' => now()->subMinute()->format('Y-m-d H:i:s'),
    ]))->assertSessionHasErrors('published_at');

    $this->post(route('admin.kegiatan.store'), $activityPayload())
        ->assertSessionHasErrors('slug');

    $this->post(route('admin.kegiatan.store'), $activityPayload([
        'slug' => 'status-tidak-valid',
        'status' => 'archived',
    ]))->assertSessionHasErrors('status');

    $this->assertDatabaseCount('activities', 1);
});

test('admin can schedule and directly publish activities', function () use ($activityPayload) {
    $future = now()->addDays(2)->startOfMinute();

    $this->post(route('admin.kegiatan.store'), $activityPayload([
        'slug' => 'kegiatan-terjadwal',
        'status' => Activity::STATUS_SCHEDULED,
        'published_at' => $future->format('Y-m-d H:i:s'),
    ]))->assertRedirect(route('admin.kegiatan.index'));

    $scheduled = Activity::query()->where('slug', 'kegiatan-terjadwal')->firstOrFail();
    expect($scheduled->published_at?->equalTo($future))->toBeTrue();

    $beforePublish = now()->subSecond();

    $this->post(route('admin.kegiatan.store'), $activityPayload([
        'slug' => 'kegiatan-langsung-terbit',
        'status' => Activity::STATUS_PUBLISHED,
    ]))->assertRedirect(route('admin.kegiatan.index'));

    $published = Activity::query()->where('slug', 'kegiatan-langsung-terbit')->firstOrFail();
    expect($published->published_at)->not->toBeNull()
        ->and($published->published_at?->greaterThanOrEqualTo($beforePublish))->toBeTrue();
});

test('admin can update an activity while retaining its slug and resetting draft publication', function () use ($activityPayload) {
    $activity = Activity::query()->create([
        'user_id' => $this->admin->id,
        ...$activityPayload([
            'status' => Activity::STATUS_SCHEDULED,
            'published_at' => now()->addWeek(),
        ]),
    ]);

    $this->put(route('admin.kegiatan.update', $activity), $activityPayload([
        'title' => 'Seminar AI Diperbarui',
        'status' => Activity::STATUS_DRAFT,
    ]))
        ->assertRedirect(route('admin.kegiatan.index'))
        ->assertSessionHas('success', 'Kegiatan berhasil diperbarui.');

    $activity->refresh();

    expect($activity->title)->toBe('Seminar AI Diperbarui')
        ->and($activity->slug)->toBe('seminar-kecerdasan-buatan')
        ->and($activity->status)->toBe(Activity::STATUS_DRAFT)
        ->and($activity->published_at)->toBeNull();
});

test('publishing a scheduled activity replaces its future schedule with the current time', function () use ($activityPayload) {
    $activity = Activity::query()->create([
        'user_id' => $this->admin->id,
        ...$activityPayload([
            'status' => Activity::STATUS_SCHEDULED,
            'published_at' => now()->addWeek(),
        ]),
    ]);
    $beforePublish = now()->subSecond();

    $this->put(route('admin.kegiatan.update', $activity), $activityPayload([
        'status' => Activity::STATUS_PUBLISHED,
    ]))->assertRedirect(route('admin.kegiatan.index'));

    $activity->refresh();

    expect($activity->status)->toBe(Activity::STATUS_PUBLISHED)
        ->and($activity->published_at?->greaterThanOrEqualTo($beforePublish))->toBeTrue()
        ->and($activity->published_at?->lessThan(now()->addSecond()))->toBeTrue();
});

test('activity index filters search status and date and keeps pagination bounded', function () {
    for ($index = 1; $index <= 11; $index++) {
        Activity::query()->create([
            'user_id' => $this->admin->id,
            'title' => $index === 7 ? 'Workshop Laravel Khusus' : 'Kegiatan Reguler '.$index,
            'slug' => 'kegiatan-filter-'.$index,
            'content' => 'Isi kegiatan.',
            'activity_date' => $index === 7 ? '2026-09-17' : '2026-09-01',
            'location' => $index === 7 ? 'Laboratorium Timur' : 'Kampus',
            'status' => $index === 7 ? Activity::STATUS_SCHEDULED : Activity::STATUS_DRAFT,
            'published_at' => $index === 7 ? now()->addMonth() : null,
        ]);
    }

    $this->get(route('admin.kegiatan.index'))
        ->assertOk()
        ->assertViewHas('activities', fn ($activities): bool => $activities->count() === 10 && $activities->total() === 11);

    $this->get(route('admin.kegiatan.index', [
        'search' => 'Laboratorium Timur',
        'status' => Activity::STATUS_SCHEDULED,
        'activity_date' => '2026-09-17',
    ]))
        ->assertOk()
        ->assertSee('Workshop Laravel Khusus')
        ->assertDontSee('Kegiatan Reguler 1')
        ->assertViewHas('filters', fn (array $filters): bool => $filters['status'] === Activity::STATUS_SCHEDULED
            && $filters['activity_date'] === '2026-09-17')
        ->assertViewHas('activities', fn ($activities): bool => $activities->total() === 1);
});

test('admin can permanently delete an activity', function () use ($activityPayload) {
    $activity = Activity::query()->create([
        'user_id' => $this->admin->id,
        ...$activityPayload(),
    ]);

    $this->delete(route('admin.kegiatan.destroy', $activity))
        ->assertRedirect(route('admin.kegiatan.index'))
        ->assertSessionHas('success', 'Kegiatan berhasil dihapus.');

    $this->assertDatabaseMissing('activities', ['id' => $activity->id]);
});

test('authenticated users without the admin role cannot access activity management', function () use ($activityPayload) {
    $activity = Activity::query()->create([
        'user_id' => $this->admin->id,
        ...$activityPayload(),
    ]);

    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.kegiatan.index'))->assertForbidden();
    $this->get(route('admin.kegiatan.create'))->assertForbidden();
    $this->post(route('admin.kegiatan.store'), $activityPayload(['slug' => 'ditolak']))->assertForbidden();
    $this->get(route('admin.kegiatan.edit', $activity))->assertForbidden();
    $this->put(route('admin.kegiatan.update', $activity), $activityPayload())->assertForbidden();
    $this->delete(route('admin.kegiatan.destroy', $activity))->assertForbidden();

    $this->assertDatabaseHas('activities', ['id' => $activity->id]);
});
