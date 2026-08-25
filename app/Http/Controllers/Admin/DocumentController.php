<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexDocumentRequest;
use App\Http\Requests\Admin\StoreDocumentRequest;
use App\Http\Requests\Admin\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Services\DashboardCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DocumentController extends Controller
{
    public function index(IndexDocumentRequest $request): View
    {
        Gate::authorize('viewAny', Document::class);
        $filters = $request->validated();

        $documents = Document::query()
            ->with('documentCategory')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when(
                $filters['document_category_id'] ?? null,
                fn ($query, int $categoryId) => $query->where('document_category_id', $categoryId),
            )
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderByDesc('uploaded_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.documents.index', [
            'documents' => $documents,
            'categories' => DocumentCategory::query()->orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Document::class);

        return view('admin.documents.create', [
            'document' => new Document(['status' => Document::STATUS_DRAFT]),
            'categories' => DocumentCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        Gate::authorize('create', Document::class);

        $data = $request->safe()->except('document_file');
        $storedFile = $this->storeUploadedFile($request->file('document_file'));

        try {
            // Kolom berkas (file/file_type/file_size) sengaja di luar $fillable dan
            // hanya boleh diisi dari hasil upload, bukan dari input request.
            $document = Document::forceCreate([
                ...$data,
                'file' => $storedFile['path'],
                'file_type' => $storedFile['type'],
                'file_size' => $storedFile['size'],
                'uploaded_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($storedFile['path']);

            throw $exception;
        }

        Cache::forget('public:document_categories');
        DashboardCache::forgetDocument();
        $this->forgetFileExistsCache($document);

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', 'Dokumen berhasil ditambahkan dan berkas tersimpan dengan aman.');
    }

    private function forgetFileExistsCache(Document $document, ?string $extraFile = null): void
    {
        Cache::forget('document:file_exists:'.$document->id.':'.md5($document->file));
        if ($extraFile !== null && $extraFile !== $document->file) {
            Cache::forget('document:file_exists:'.$document->id.':'.md5($extraFile));
        }
    }

    public function edit(Document $document): View
    {
        Gate::authorize('update', $document);

        return view('admin.documents.edit', [
            'document' => $document->load('documentCategory'),
            'categories' => DocumentCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        Gate::authorize('update', $document);

        $data = $request->safe()->except('document_file');
        $oldFile = $document->file;
        $storedFile = null;

        if ($request->hasFile('document_file')) {
            $storedFile = $this->storeUploadedFile($request->file('document_file'));
            $data = [
                ...$data,
                'file' => $storedFile['path'],
                'file_type' => $storedFile['type'],
                'file_size' => $storedFile['size'],
                'uploaded_at' => now(),
            ];
        }

        try {
            // forceFill: kolom berkas hanya berasal dari hasil upload (bukan input request).
            $document->forceFill($data)->save();
        } catch (Throwable $exception) {
            if ($storedFile !== null) {
                Storage::disk('local')->delete($storedFile['path']);
            }

            throw $exception;
        }

        if ($storedFile !== null && $oldFile !== $storedFile['path']) {
            Storage::disk('local')->delete($oldFile);
        }

        Cache::forget('public:document_categories');
        DashboardCache::forgetDocument();
        $this->forgetFileExistsCache($document, $oldFile);

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function toggleStatus(Document $document): RedirectResponse
    {
        Gate::authorize('update', $document);

        $document->update([
            'status' => $document->status === Document::STATUS_PUBLISHED
                ? Document::STATUS_DRAFT
                : Document::STATUS_PUBLISHED,
        ]);

        Cache::forget('public:document_categories');
        DashboardCache::forgetDocument();

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', "Status {$document->title} berhasil diubah menjadi {$document->statusLabel()}.");
    }

    public function download(Document $document): StreamedResponse
    {
        Gate::authorize('view', $document);
        abort_unless(Storage::disk('local')->exists($document->file), 404);

        return Storage::disk('local')->download(
            $document->file,
            $document->slug.'.'.$document->file_type,
            ['X-Content-Type-Options' => 'nosniff'],
        );
    }

    public function destroy(Document $document): RedirectResponse
    {
        Gate::authorize('delete', $document);
        $file = $document->file;
        $document->delete();
        Storage::disk('local')->delete($file);

        Cache::forget('public:document_categories');
        DashboardCache::forgetDocument();
        Cache::forget('document:file_exists:'.$document->id.':'.md5($file));

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', 'Dokumen dan berkasnya berhasil dihapus.');
    }

    /** @return array{path: string, type: string, size: int} */
    private function storeUploadedFile(UploadedFile $file): array
    {
        $type = strtolower($file->extension() ?: $file->getClientOriginalExtension());
        if (! in_array($type, ['pdf', 'doc', 'docx'], true)) {
            throw ValidationException::withMessages(['document_file' => 'Tipe berkas harus PDF, DOC, atau DOCX.']);
        }
        $path = $file->storeAs(
            'documents',
            Str::uuid().'.'.$type,
            'local',
        );

        return [
            'path' => $path,
            'type' => $type,
            'size' => (int) $file->getSize(),
        ];
    }
}
