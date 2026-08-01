<?php

use App\Models\DocumentCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

test('task 07 creates every core table with the documented columns', function () {
    $schema = [
        'users' => [
            'id', 'name', 'email', 'password', 'role', 'created_at', 'updated_at',
        ],
        'site_settings' => [
            'id', 'site_name', 'university_name', 'faculty_name', 'logo', 'favicon',
            'journal_url', 'footer_text', 'footer_academic_links', 'created_at', 'updated_at',
        ],
        'home_sections' => [
            'id', 'hero_title', 'hero_subtitle', 'hero_slides', 'cta_text', 'cta_link',
            'welcome_title', 'welcome_description', 'created_at', 'updated_at',
        ],
        'program_profiles' => [
            'id', 'history', 'description', 'vision', 'mission', 'goals', 'accreditation',
            'advantages', 'created_at', 'updated_at',
        ],
        'lecturers' => [
            'id', 'name', 'nidn', 'position', 'expertise', 'education', 'email', 'photo',
            'bio', 'status', 'sort_order', 'created_at', 'updated_at',
        ],
        'activities' => [
            'id', 'user_id', 'title', 'slug', 'excerpt', 'content', 'image', 'activity_date',
            'location', 'category', 'status', 'published_at', 'created_at', 'updated_at',
        ],
        'document_categories' => [
            'id', 'name', 'slug', 'created_at', 'updated_at',
        ],
        'documents' => [
            'id', 'document_category_id', 'title', 'slug', 'description', 'file', 'file_type',
            'file_size', 'status', 'uploaded_at', 'created_at', 'updated_at',
        ],
        'alumni' => [
            'id', 'name', 'batch_year', 'graduation_year', 'job_position', 'company',
            'testimonial', 'photo', 'status', 'created_at', 'updated_at',
        ],
        'contacts' => [
            'id', 'address', 'email', 'phone', 'instagram', 'youtube', 'facebook',
            'map_embed', 'created_at', 'updated_at',
        ],
    ];

    foreach ($schema as $table => $columns) {
        expect(Schema::hasTable($table))->toBeTrue("Table {$table} was not created.")
            ->and(Schema::hasColumns($table, $columns))->toBeTrue("Table {$table} has missing columns.");
    }
});

test('task 07 creates the documented core relationships', function () {
    $activityForeignKey = collect(Schema::getForeignKeys('activities'))
        ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['user_id']);
    $documentForeignKey = collect(Schema::getForeignKeys('documents'))
        ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['document_category_id']);

    expect($activityForeignKey)->not->toBeNull()
        ->and($activityForeignKey['foreign_table'])->toBe('users')
        ->and($activityForeignKey['foreign_columns'])->toBe(['id'])
        ->and($documentForeignKey)->not->toBeNull()
        ->and($documentForeignKey['foreign_table'])->toBe('document_categories')
        ->and($documentForeignKey['foreign_columns'])->toBe(['id']);
});

test('publication and visibility fields use safe defaults', function () {
    $userId = DB::table('users')->insertGetId([
        'name' => 'Administrator',
        'email' => 'admin@example.test',
        'password' => 'secret',
    ]);
    $categoryId = DB::table('document_categories')->insertGetId([
        'name' => 'Panduan',
        'slug' => 'panduan',
    ]);

    DB::table('lecturers')->insert(['name' => 'Dosen', 'nidn' => '001']);
    DB::table('alumni')->insert(['name' => 'Alumni', 'batch_year' => 2020]);
    DB::table('activities')->insert([
        'user_id' => $userId,
        'title' => 'Kegiatan',
        'slug' => 'kegiatan',
    ]);
    DB::table('documents')->insert([
        'document_category_id' => $categoryId,
        'title' => 'Dokumen',
        'slug' => 'dokumen',
        'file' => 'documents/dokumen.pdf',
        'file_type' => 'pdf',
        'file_size' => 1024,
    ]);

    expect(DB::table('users')->value('role'))->toBe('admin')
        ->and(DB::table('lecturers')->value('status'))->toBe('active')
        ->and(DB::table('lecturers')->value('sort_order'))->toBe(0)
        ->and(DB::table('alumni')->value('status'))->toBe('active')
        ->and(DB::table('activities')->value('status'))->toBe('draft')
        ->and(DB::table('documents')->value('status'))->toBe('draft');
});

test('eloquent models persist and resolve the documented relationships', function () {
    $user = User::create([
        'name' => 'Administrator',
        'email' => 'eloquent@example.test',
        'password' => 'secret',
        'role' => User::ROLE_ADMIN,
    ]);
    $activity = $user->activities()->create([
        'title' => 'Kegiatan Eloquent',
        'slug' => 'kegiatan-eloquent',
    ]);

    $category = DocumentCategory::create([
        'name' => 'Pedoman Eloquent',
        'slug' => 'pedoman-eloquent',
    ]);
    $document = $category->documents()->forceCreate([
        'title' => 'Dokumen Eloquent',
        'slug' => 'dokumen-eloquent',
        'file' => 'documents/eloquent.pdf',
        'file_type' => 'pdf',
        'file_size' => 2048,
    ]);

    expect($activity->user->is($user))->toBeTrue()
        ->and($user->fresh()->activities->contains($activity))->toBeTrue()
        ->and($document->documentCategory->is($category))->toBeTrue()
        ->and($category->fresh()->documents->contains($document))->toBeTrue();
});
