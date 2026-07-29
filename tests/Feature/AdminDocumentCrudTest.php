<?php

use App\Models\Document;
use App\Models\DocumentCategory;
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

$categoryPayload = fn (array $overrides = []): array => array_merge([
    'name' => 'Panduan Akademik',
    'slug' => 'panduan-akademik',
], $overrides);

$documentPayload = fn (DocumentCategory $category, array $overrides = []): array => array_merge([
    'document_category_id' => $category->id,
    'title' => 'Panduan Akademik 2026',
    'slug' => 'panduan-akademik-2026',
    'description' => 'Panduan akademik untuk mahasiswa Ilmu Komputer.',
    'status' => Document::STATUS_DRAFT,
    'document_file' => UploadedFile::fake()->create('panduan.pdf', 120, 'application/pdf'),
], $overrides);

$createStoredDocument = function (DocumentCategory $category, array $overrides = []): Document {
    $attributes = array_merge([
        'document_category_id' => $category->id,
        'title' => 'Panduan Akademik 2026',
        'slug' => 'panduan-akademik-2026',
        'description' => 'Panduan akademik untuk mahasiswa.',
        'file' => 'uploads/documents/panduan.pdf',
        'file_type' => 'pdf',
        'file_size' => 122880,
        'status' => Document::STATUS_DRAFT,
        'uploaded_at' => now(),
    ], $overrides);

    Storage::disk('public')->put($attributes['file'], 'file-content');

    return Document::query()->create($attributes);
};

test('admin can manage document categories with automatic slugs', function () use ($categoryPayload) {
    $this->get(route('admin.kategori-dokumen.index'))
        ->assertOk()
        ->assertSee('Kategori Dokumen')
        ->assertSee('name="name"', escape: false);

    $this->post(route('admin.kategori-dokumen.store'), $categoryPayload([
        'name' => '  Surat Keputusan  ',
        'slug' => '',
    ]))
        ->assertRedirect(route('admin.kategori-dokumen.index'))
        ->assertSessionHas('success', 'Kategori dokumen berhasil ditambahkan.');

    $category = DocumentCategory::query()->where('slug', 'surat-keputusan')->firstOrFail();

    $this->put(route('admin.kategori-dokumen.update', $category), [
        'name' => 'Surat dan Keputusan',
        'slug' => '',
    ])->assertRedirect(route('admin.kategori-dokumen.index'));

    expect($category->fresh()->slug)->toBe('surat-dan-keputusan');
});

test('category slugs are unique and used categories cannot be deleted', function () use ($categoryPayload, $createStoredDocument) {
    $usedCategory = DocumentCategory::query()->create($categoryPayload());
    $emptyCategory = DocumentCategory::query()->create([
        'name' => 'Formulir',
        'slug' => 'formulir',
    ]);
    $createStoredDocument($usedCategory);

    $this->post(route('admin.kategori-dokumen.store'), [
        'name' => 'Kategori Duplikat',
        'slug' => 'panduan-akademik',
    ])->assertSessionHasErrors('slug');

    $this->delete(route('admin.kategori-dokumen.destroy', $usedCategory))
        ->assertRedirect(route('admin.kategori-dokumen.index'))
        ->assertSessionHas('warning', 'Kategori masih digunakan dokumen dan tidak dapat dihapus.');

    $this->delete(route('admin.kategori-dokumen.destroy', $emptyCategory))
        ->assertRedirect(route('admin.kategori-dokumen.index'));

    $this->assertDatabaseHas('document_categories', ['id' => $usedCategory->id]);
    $this->assertDatabaseMissing('document_categories', ['id' => $emptyCategory->id]);
});

test('admin can open the document index and real upload forms', function () use ($categoryPayload, $createStoredDocument) {
    $category = DocumentCategory::query()->create($categoryPayload());
    $document = $createStoredDocument($category);

    $this->get(route('admin.dokumen.index'))
        ->assertOk()
        ->assertSee('Daftar Dokumen')
        ->assertSee('Panduan Akademik 2026')
        ->assertSee(route('admin.dokumen.download', $document), escape: false)
        ->assertSee(route('admin.dokumen.status', $document), escape: false);

    $this->get(route('admin.dokumen.create'))
        ->assertOk()
        ->assertSee('Tambah Dokumen')
        ->assertSee('enctype="multipart/form-data"', escape: false)
        ->assertSee('accept=".pdf,.doc,.docx', escape: false);

    $this->get(route('admin.dokumen.edit', $document))
        ->assertOk()
        ->assertSee('Edit Dokumen')
        ->assertSee('Berkas saat ini')
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('admin can upload a validated document with stored metadata', function () use ($categoryPayload, $documentPayload) {
    $category = DocumentCategory::query()->create($categoryPayload());

    $this->post(route('admin.dokumen.store'), $documentPayload($category, [
        'title' => '  Kalender Akademik 2026  ',
        'slug' => '',
        'description' => '  Jadwal akademik satu tahun.  ',
        'status' => Document::STATUS_PUBLISHED,
    ]))
        ->assertRedirect(route('admin.dokumen.index'))
        ->assertSessionHas('success', 'Dokumen berhasil ditambahkan dan berkas tersimpan dengan aman.');

    $document = Document::query()->where('slug', 'kalender-akademik-2026')->firstOrFail();

    expect($document->title)->toBe('Kalender Akademik 2026')
        ->and($document->description)->toBe('Jadwal akademik satu tahun.')
        ->and($document->file_type)->toBe('pdf')
        ->and($document->file_size)->toBeGreaterThan(0)
        ->and($document->uploaded_at)->not->toBeNull()
        ->and($document->status)->toBe(Document::STATUS_PUBLISHED);

    expect($document->file)->toStartWith('uploads/documents/')
        ->and(basename($document->file))->not->toBe('panduan.pdf');
    Storage::disk('public')->assertExists($document->file);
});

test('document validation rejects unsafe files invalid references and duplicate slugs', function () use ($categoryPayload, $documentPayload, $createStoredDocument) {
    $category = DocumentCategory::query()->create($categoryPayload());
    $createStoredDocument($category);

    $this->post(route('admin.dokumen.store'), $documentPayload($category, [
        'document_category_id' => 999999,
        'slug' => 'panduan-akademik-2026',
        'status' => 'archived',
        'document_file' => UploadedFile::fake()->create('script.php', 2, 'application/x-php'),
    ]))->assertSessionHasErrors([
        'document_category_id',
        'slug',
        'status',
        'document_file',
    ]);

    $this->assertDatabaseCount('documents', 1);
});

test('updating document metadata without a file keeps the old file', function () use ($categoryPayload, $createStoredDocument) {
    $category = DocumentCategory::query()->create($categoryPayload());
    $document = $createStoredDocument($category);
    $oldUploadedAt = $document->uploaded_at;

    $this->put(route('admin.dokumen.update', $document), [
        'document_category_id' => $category->id,
        'title' => 'Panduan Akademik Diperbarui',
        'slug' => 'panduan-akademik-2026',
        'description' => 'Deskripsi diperbarui.',
        'status' => Document::STATUS_PUBLISHED,
    ])->assertRedirect(route('admin.dokumen.index'));

    $document->refresh();

    expect($document->file)->toBe('uploads/documents/panduan.pdf')
        ->and($document->file_type)->toBe('pdf')
        ->and($document->file_size)->toBe(122880)
        ->and($document->uploaded_at?->equalTo($oldUploadedAt))->toBeTrue();
    Storage::disk('public')->assertExists('uploads/documents/panduan.pdf');
});

test('uploading a replacement updates metadata and deletes the old file', function () use ($categoryPayload, $createStoredDocument) {
    $category = DocumentCategory::query()->create($categoryPayload());
    $document = $createStoredDocument($category);

    $this->put(route('admin.dokumen.update', $document), [
        'document_category_id' => $category->id,
        'title' => $document->title,
        'slug' => $document->slug,
        'description' => $document->description,
        'status' => Document::STATUS_DRAFT,
        'document_file' => UploadedFile::fake()->create(
            'panduan-baru.docx',
            200,
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ),
    ])->assertRedirect(route('admin.dokumen.index'));

    $document->refresh();

    expect($document->file)->not->toBe('uploads/documents/panduan.pdf')
        ->and($document->file_type)->toBe('docx')
        ->and($document->file_size)->toBeGreaterThan(0);
    Storage::disk('public')->assertMissing('uploads/documents/panduan.pdf');
    Storage::disk('public')->assertExists($document->file);
});

test('document index filters category status and search with bounded pagination', function () use ($createStoredDocument) {
    $guides = DocumentCategory::query()->create(['name' => 'Panduan', 'slug' => 'panduan']);
    $forms = DocumentCategory::query()->create(['name' => 'Formulir', 'slug' => 'formulir']);

    for ($index = 1; $index <= 11; $index++) {
        $createStoredDocument($index === 7 ? $forms : $guides, [
            'title' => $index === 7 ? 'Formulir Cuti Khusus' : 'Dokumen Reguler '.$index,
            'slug' => 'dokumen-filter-'.$index,
            'file' => "uploads/documents/filter-{$index}.pdf",
            'status' => $index === 7 ? Document::STATUS_PUBLISHED : Document::STATUS_DRAFT,
            'uploaded_at' => now()->subMinutes($index),
        ]);
    }

    $this->get(route('admin.dokumen.index'))
        ->assertOk()
        ->assertViewHas('documents', fn ($documents): bool => $documents->count() === 10 && $documents->total() === 11);

    $this->get(route('admin.dokumen.index', [
        'search' => 'Cuti Khusus',
        'document_category_id' => $forms->id,
        'status' => Document::STATUS_PUBLISHED,
    ]))
        ->assertOk()
        ->assertSee('Formulir Cuti Khusus')
        ->assertDontSee('Dokumen Reguler 1')
        ->assertViewHas('filters', fn (array $filters): bool => (int) $filters['document_category_id'] === $forms->id
            && $filters['status'] === Document::STATUS_PUBLISHED)
        ->assertViewHas('documents', fn ($documents): bool => $documents->total() === 1);
});

test('admin can toggle download and permanently delete a document file', function () use ($categoryPayload, $createStoredDocument) {
    $category = DocumentCategory::query()->create($categoryPayload());
    $document = $createStoredDocument($category);

    $this->patch(route('admin.dokumen.status', $document))
        ->assertRedirect(route('admin.dokumen.index'));
    expect($document->fresh()->status)->toBe(Document::STATUS_PUBLISHED);

    $this->get(route('admin.dokumen.download', $document))
        ->assertOk()
        ->assertDownload('panduan-akademik-2026.pdf');

    $this->delete(route('admin.dokumen.destroy', $document))
        ->assertRedirect(route('admin.dokumen.index'))
        ->assertSessionHas('success', 'Dokumen dan berkasnya berhasil dihapus.');

    $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    Storage::disk('public')->assertMissing('uploads/documents/panduan.pdf');
});

test('authenticated users without admin role cannot manage documents or categories', function () use ($categoryPayload, $documentPayload, $createStoredDocument) {
    $category = DocumentCategory::query()->create($categoryPayload());
    $document = $createStoredDocument($category);

    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.dokumen.index'))->assertForbidden();
    $this->get(route('admin.dokumen.create'))->assertForbidden();
    $this->post(route('admin.dokumen.store'), $documentPayload($category))->assertForbidden();
    $this->get(route('admin.dokumen.edit', $document))->assertForbidden();
    $this->put(route('admin.dokumen.update', $document), $documentPayload($category))->assertForbidden();
    $this->patch(route('admin.dokumen.status', $document))->assertForbidden();
    $this->get(route('admin.dokumen.download', $document))->assertForbidden();
    $this->delete(route('admin.dokumen.destroy', $document))->assertForbidden();
    $this->get(route('admin.kategori-dokumen.index'))->assertForbidden();
    $this->post(route('admin.kategori-dokumen.store'), ['name' => 'Ditolak', 'slug' => 'ditolak'])->assertForbidden();
    $this->put(route('admin.kategori-dokumen.update', $category), $categoryPayload())->assertForbidden();
    $this->delete(route('admin.kategori-dokumen.destroy', $category))->assertForbidden();

    $this->assertDatabaseHas('documents', ['id' => $document->id]);
    $this->assertDatabaseHas('document_categories', ['id' => $category->id]);
});
