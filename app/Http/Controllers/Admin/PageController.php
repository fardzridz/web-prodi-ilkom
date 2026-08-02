<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePageRequest;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', [
            'pages' => Page::query()->orderBy('id')->get(),
        ]);
    }

    public function edit(string $slug): View
    {
        $page = Page::query()->where('slug', $slug)->firstOrFail();

        return view('admin.pages.edit', ['page' => $page]);
    }

    public function update(string $slug, UpdatePageRequest $request): RedirectResponse
    {
        $page = Page::query()->where('slug', $slug)->firstOrFail();
        $page->fill($request->validated())->save();

        return redirect()
            ->route('admin.halaman.edit', ['slug' => $page->slug])
            ->with('success', 'Konten halaman berhasil diperbarui.');
    }
}
