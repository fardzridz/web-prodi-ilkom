<?php

use App\Models\Contact;
use App\Models\Document;
use App\Models\HomeSection;
use App\Models\SiteSetting;
use App\Services\ImageOptimizer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Cache::flush();
    Storage::fake('local');

    SiteSetting::factory()->create();
    Contact::factory()->create();
    HomeSection::factory()->create();
});

/**
 * Persist a document whose backing file actually exists on the faked private disk.
 */
function publishedDocumentWithFile(array $attributes = []): Document
{
    $document = Document::factory()->create($attributes);

    Storage::disk('local')->put($document->file, '%PDF-1.4 dummy');

    return $document;
}

// ─── Unduh ────────────────────────────────────────────────────────────────

test('dokumen terbit dapat diunduh dengan nama berbasis slug', function (): void {
    $document = publishedDocumentWithFile(['slug' => 'kurikulum-2026', 'file_type' => 'pdf']);

    $this->get(route('documents.download', $document))
        ->assertSuccessful()
        ->assertDownload('kurikulum-2026.pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff');
});

test('dokumen draf tidak dapat diunduh', function (): void {
    $document = Document::factory()->draft()->create();
    Storage::disk('local')->put($document->file, 'dummy');

    $this->get(route('documents.download', $document))->assertNotFound();
});

test('dokumen dengan berkas hilang mengembalikan 404 alih-alih error server', function (): void {
    $document = Document::factory()->create();

    $this->get(route('documents.download', $document))->assertNotFound();
});

// ─── Pratinjau ────────────────────────────────────────────────────────────

test('pratinjau dokumen pdf disajikan inline dengan mime yang benar', function (): void {
    $document = publishedDocumentWithFile(['slug' => 'panduan-akademik', 'file_type' => 'pdf']);

    $this->get(route('documents.view', $document))
        ->assertSuccessful()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Content-Disposition', 'inline; filename="panduan-akademik.pdf"');
});

test('pratinjau dokumen draf mengembalikan 404', function (): void {
    $document = Document::factory()->draft()->create();
    Storage::disk('local')->put($document->file, 'dummy');

    $this->get(route('documents.view', $document))->assertNotFound();
});

// ─── Header cache (regression) ────────────────────────────────────────────

test('respons berkas tidak diberi ETag maupun Cache-Control publik', function (string $routeName): void {
    $document = publishedDocumentWithFile();

    $response = $this->get(route($routeName, $document))->assertSuccessful();

    expect($response->headers->get('ETag'))->toBeNull()
        ->and($response->headers->get('Cache-Control'))->not->toContain('max-age=60');
})->with(['documents.download', 'documents.view']);

test('halaman daftar dokumen tetap mendapat ETag dan Cache-Control', function (): void {
    publishedDocumentWithFile();

    $response = $this->get('/dokumen')->assertSuccessful();

    expect($response->headers->get('ETag'))->not->toBeNull()
        ->and($response->headers->get('Cache-Control'))->toContain('max-age=60');
});

test('ETag halaman berbeda ketika konten berubah', function (): void {
    publishedDocumentWithFile(['title' => 'Kurikulum Awal']);
    $first = $this->get('/dokumen')->headers->get('ETag');

    Cache::flush();
    publishedDocumentWithFile(['title' => 'Kurikulum Tambahan']);
    $second = $this->get('/dokumen')->headers->get('ETag');

    expect($first)->not->toBe($second);
});

// ─── Throttle ─────────────────────────────────────────────────────────────

test('unduhan dibatasi 30 permintaan per menit', function (): void {
    $document = publishedDocumentWithFile();
    $url = route('documents.download', $document);

    for ($i = 0; $i < 30; $i++) {
        $this->get($url)->assertSuccessful();
    }

    $this->get($url)->assertStatus(429);
});

// ─── ImageOptimizer error handling ────────────────────────────────────────

test('ImageOptimizer menolak berkas gambar yang rusak tanpa meninggalkan sisa berkas', function (): void {
    Storage::fake('public');

    $corrupt = UploadedFile::fake()->createWithContent('rusak.png', 'bukan-data-gambar');

    expect(fn () => app(ImageOptimizer::class)->optimize($corrupt, 'uploads/test'))
        ->toThrow(RuntimeException::class);

    expect(Storage::disk('public')->allFiles('uploads/test'))->toBe([]);
});
