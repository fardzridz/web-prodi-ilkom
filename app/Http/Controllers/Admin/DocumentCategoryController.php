<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexDocumentCategoryRequest;
use App\Http\Requests\Admin\StoreDocumentCategoryRequest;
use App\Http\Requests\Admin\UpdateDocumentCategoryRequest;
use App\Models\DocumentCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class DocumentCategoryController extends Controller
{
    public function index(IndexDocumentCategoryRequest $request): View
    {
        $validated = $request->validated();
        $editingCategory = isset($validated['edit'])
            ? DocumentCategory::query()->findOrFail($validated['edit'])
            : new DocumentCategory;

        return view('admin.document-categories.index', [
            'categories' => DocumentCategory::query()
                ->withCount('documents')
                ->orderBy('name')
                ->paginate(10)
                ->withQueryString(),
            'editingCategory' => $editingCategory,
        ]);
    }

    public function store(StoreDocumentCategoryRequest $request): RedirectResponse
    {
        DocumentCategory::query()->create($request->validated());

        Cache::forget('public:document_categories');

        return redirect()
            ->route('admin.kategori-dokumen.index')
            ->with('success', 'Kategori dokumen berhasil ditambahkan.');
    }

    public function update(
        UpdateDocumentCategoryRequest $request,
        DocumentCategory $documentCategory,
    ): RedirectResponse {
        $documentCategory->update($request->validated());

        Cache::forget('public:document_categories');

        return redirect()
            ->route('admin.kategori-dokumen.index')
            ->with('success', 'Kategori dokumen berhasil diperbarui.');
    }

    public function destroy(
        DocumentCategory $documentCategory,
    ): RedirectResponse {
        if ($documentCategory->documents()->exists()) {
            return redirect()
                ->route('admin.kategori-dokumen.index')
                ->with('warning', 'Kategori masih digunakan dokumen dan tidak dapat dihapus.');
        }

        $documentCategory->delete();

        Cache::forget('public:document_categories');

        return redirect()
            ->route('admin.kategori-dokumen.index')
            ->with('success', 'Kategori dokumen berhasil dihapus.');
    }
}
