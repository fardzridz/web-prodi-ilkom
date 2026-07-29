<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexDocumentRequest;
use App\Http\Requests\Admin\StoreDocumentRequest;
use App\Http\Requests\Admin\UpdateDocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class DocumentController extends Controller
{
    public function index(IndexDocumentRequest $request): View
    {
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

    public function create(IndexDocumentRequest $request): View
    {
        return view('admin.documents.create', [
            'document' => new Document(['status' => Document::STATUS_DRAFT]),
            'categories' => DocumentCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('document_file');
        $storedFile = $this->storeUploadedFile($request->file('document_file'));

        try {
            Document::query()->create([
                ...$data,
                'file' => $storedFile['path'],
                'file_type' => $storedFile['type'],
                'file_size' => $storedFile['size'],
                'uploaded_at' => now(),
            ]);
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedFile['path']);

            throw $exception;
        }

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', 'Dokumen berhasil ditambahkan dan berkas tersimpan dengan aman.');
    }

    public function edit(IndexDocumentRequest $request, Document $document): View
    {
        return view('admin.documents.edit', [
            'document' => $document->load('documentCategory'),
            'categories' => DocumentCategory::query()->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
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
            $document->update($data);
        } catch (Throwable $exception) {
            if ($storedFile !== null) {
                Storage::disk('public')->delete($storedFile['path']);
            }

            throw $exception;
        }

        if ($storedFile !== null && $oldFile !== $storedFile['path']) {
            Storage::disk('public')->delete($oldFile);
        }

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', 'Dokumen berhasil diperbarui.');
    }

    public function toggleStatus(IndexDocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update([
            'status' => $document->status === Document::STATUS_PUBLISHED
                ? Document::STATUS_DRAFT
                : Document::STATUS_PUBLISHED,
        ]);

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', "Status {$document->title} berhasil diubah menjadi {$document->statusLabel()}.");
    }

    public function download(IndexDocumentRequest $request, Document $document): StreamedResponse
    {
        abort_unless(Storage::disk('public')->exists($document->file), 404);

        return Storage::disk('public')->download(
            $document->file,
            $document->slug.'.'.$document->file_type,
        );
    }

    public function destroy(IndexDocumentRequest $request, Document $document): RedirectResponse
    {
        $file = $document->file;
        $document->delete();
        Storage::disk('public')->delete($file);

        return redirect()
            ->route('admin.dokumen.index')
            ->with('success', 'Dokumen dan berkasnya berhasil dihapus.');
    }

    /** @return array{path: string, type: string, size: int} */
    private function storeUploadedFile(UploadedFile $file): array
    {
        $type = strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs(
            'uploads/documents',
            Str::uuid().'.'.$type,
            'public',
        );

        return [
            'path' => $path,
            'type' => $type,
            'size' => (int) $file->getSize(),
        ];
    }
}
