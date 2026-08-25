<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\DocumentIndexRequest;
use App\Models\Document;
use App\Services\Public\PublicDataService;
use Illuminate\Contracts\View\View;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function __construct(private readonly PublicDataService $dataService) {}

    public function index(DocumentIndexRequest $request): View
    {
        $search = $request->search();
        $category = $request->category();

        if ($category !== null && ! $this->dataService->cachedDocumentCategories()->contains($category)) {
            $category = null;
        }

        return view('public.documents', [
            'documents' => $this->dataService->documentsData(search: $search, category: $category),
            'documentCategories' => $this->dataService->cachedDocumentCategories(),
            'filters' => ['q' => $search, 'category' => $category],
            'seoTitle' => 'Dokumen S1 Ilmu Komputer UNIWARA Pasuruan | Kurikulum & Pedoman',
            'seoDesc' => 'Dokumen resmi S1 Ilmu Komputer UNIWARA Pasuruan: kurikulum, panduan akademik, akreditasi, dan pedoman kemahasiswaan. Unduh PDF.',
            'canonical' => route('documents'),
        ]);
    }

    public function download(Document $document): StreamedResponse
    {
        abort_unless($document->status === Document::STATUS_PUBLISHED, 404);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('local');

        abort_unless($storage->exists($document->file), 404);

        return $storage->download(
            $document->file,
            $document->slug.'.'.$document->file_type,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }

    public function view(Document $document): StreamedResponse
    {
        abort_unless($document->status === Document::STATUS_PUBLISHED, 404);

        /** @var FilesystemAdapter $storage */
        $storage = Storage::disk('local');

        abort_unless($storage->exists($document->file), 404);

        $mimeTypes = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ];

        $type = strtolower($document->file_type);
        $mime = $mimeTypes[$type] ?? $storage->mimeType($document->file);

        $headers = [
            'Content-Type' => $mime ?: 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="'.$document->slug.'.'.$document->file_type.'"',
            'X-Content-Type-Options' => 'nosniff',
        ];

        return $storage->response($document->file, $document->slug.'.'.$document->file_type, $headers);
    }
}
