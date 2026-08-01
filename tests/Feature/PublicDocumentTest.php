<?php

use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        Storage::fake('local');
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

$createStoredDocument = function (string $slug, string $fileType, string $file, string $status): Document {
    $category = DocumentCategory::query()->create([
        'name' => 'Dokumen Publik',
        'slug' => 'dokumen-publik',
    ]);

    Storage::disk('local')->put($file, $fileType === 'pdf' ? "%PDF-1.4\n% test" : 'file-content');

    $document = Document::forceCreate([
        'document_category_id' => $category->id,
        'title' => 'Dokumen '.$slug,
        'slug' => $slug,
        'description' => null,
        'file' => $file,
        'file_type' => $fileType,
        'file_size' => 1024,
        'status' => $status,
        'uploaded_at' => now(),
    ]);

    return $document;
};

test('view serves a published pdf inline with the correct content type', function () use ($createStoredDocument) {
    $document = $createStoredDocument('contoh-pdf', 'pdf', 'documents/contoh-pdf.pdf', Document::STATUS_PUBLISHED);

    $this->get(route('documents.view', $document))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeaderContains('Content-Disposition', 'inline')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('view serves a published docx inline', function () use ($createStoredDocument) {
    $document = $createStoredDocument('contoh-docx', 'docx', 'documents/contoh-docx.docx', Document::STATUS_PUBLISHED);

    $this->get(route('documents.view', $document))
        ->assertOk()
        ->assertHeaderContains('Content-Disposition', 'inline')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('view returns 404 for a draft document', function () use ($createStoredDocument) {
    $document = $createStoredDocument('draft-doc', 'pdf', 'documents/draft-doc.pdf', Document::STATUS_DRAFT);

    $this->get(route('documents.view', $document))->assertNotFound();
});

test('view returns 404 when the stored file is missing', function () use ($createStoredDocument) {
    $document = $createStoredDocument('hilang', 'pdf', 'documents/tidak-ada.pdf', Document::STATUS_PUBLISHED);

    Storage::disk('local')->delete('documents/tidak-ada.pdf');

    $this->get(route('documents.view', $document))->assertNotFound();
});

test('download serves a published document as an attachment', function () use ($createStoredDocument) {
    $document = $createStoredDocument('contoh-unduh', 'pdf', 'documents/contoh-unduh.pdf', Document::STATUS_PUBLISHED);

    $this->get(route('documents.download', $document))
        ->assertOk()
        ->assertDownload('contoh-unduh.pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('download returns 404 for a draft document', function () use ($createStoredDocument) {
    $document = $createStoredDocument('draft-unduh', 'pdf', 'documents/draft-unduh.pdf', Document::STATUS_DRAFT);

    $this->get(route('documents.download', $document))->assertNotFound();
});
