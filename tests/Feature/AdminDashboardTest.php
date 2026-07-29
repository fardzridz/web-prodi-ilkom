<?php

use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\Lecturer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->actingAs(User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]));
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

test('dashboard summarizes content and status counts from the database', function () {
    $admin = User::query()->firstOrFail();
    $category = DocumentCategory::query()->create([
        'name' => 'Akademik',
        'slug' => 'akademik',
    ]);

    foreach ([Activity::STATUS_DRAFT, Activity::STATUS_SCHEDULED, Activity::STATUS_PUBLISHED] as $index => $status) {
        Activity::query()->create([
            'user_id' => $admin->id,
            'title' => 'Kegiatan '.($index + 1),
            'slug' => 'kegiatan-'.($index + 1),
            'status' => $status,
        ]);
    }

    foreach ([Lecturer::STATUS_ACTIVE, Lecturer::STATUS_ACTIVE, Lecturer::STATUS_INACTIVE] as $index => $status) {
        Lecturer::query()->create([
            'name' => 'Dosen '.($index + 1),
            'nidn' => '000000000'.($index + 1),
            'status' => $status,
        ]);
    }

    foreach ([Document::STATUS_DRAFT, Document::STATUS_PUBLISHED] as $index => $status) {
        Document::query()->create([
            'document_category_id' => $category->id,
            'title' => 'Dokumen '.($index + 1),
            'slug' => 'dokumen-'.($index + 1),
            'file' => 'documents/test-'.$index.'.pdf',
            'file_type' => 'PDF',
            'file_size' => 1024,
            'status' => $status,
        ]);
    }

    foreach ([Alumni::STATUS_ACTIVE, Alumni::STATUS_INACTIVE] as $index => $status) {
        Alumni::query()->create([
            'name' => 'Alumni '.($index + 1),
            'batch_year' => 2020 + $index,
            'status' => $status,
        ]);
    }

    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('summaryCards', function (array $cards): bool {
            return collect($cards)->pluck('count', 'key')->all() === [
                'activities' => 3,
                'lecturers' => 3,
                'documents' => 2,
                'alumni' => 2,
            ];
        })
        ->assertViewHas('statusCards', function (array $cards): bool {
            return collect($cards)->pluck('count', 'key')->all() === [
                'draft' => 2,
                'scheduled' => 1,
                'published' => 2,
                'active' => 3,
            ];
        })
        ->assertSee('Aktivitas Terbaru')
        ->assertSee('Kegiatan 3')
        ->assertSee('Tambah Kegiatan')
        ->assertSee('href="'.route('admin.kegiatan.create').'"', escape: false)
        ->assertSee('href="'.route('admin.dosen.create').'"', escape: false)
        ->assertSee('href="'.route('admin.dokumen.create').'"', escape: false);
});

test('dashboard renders a useful empty state when content does not exist', function () {
    $this->get(route('admin.dashboard'))
        ->assertOk()
        ->assertViewHas('summaryCards', fn (array $cards): bool => collect($cards)->sum('count') === 0)
        ->assertViewHas('statusCards', fn (array $cards): bool => collect($cards)->sum('count') === 0)
        ->assertSee('Belum ada aktivitas konten')
        ->assertSee('Kesiapan Halaman Publik')
        ->assertSee('Perlu isi');
});
