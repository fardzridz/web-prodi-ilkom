@extends('layouts.admin')

@section('title', 'Kategori Dokumen | Pengelola Situs Prodi')
@section('page-heading', 'Kategori Dokumen')

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">
        <div class="lg:col-span-2">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                    <h4 class="text-base font-medium text-gray-800 dark:text-white/90">
                        {{ $editingCategory->exists ? 'Edit Kategori' : 'Tambah Kategori' }}
                    </h4>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Slug dapat dibuat otomatis dari nama kategori.</p>
                </div>
                <div class="p-6">
                    <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ $editingCategory->exists ? route('admin.kategori-dokumen.update', $editingCategory) : route('admin.kategori-dokumen.store') }}" method="post">
                        @csrf
                        @if ($editingCategory->exists) @method('PUT') @endif
                        <div class="space-y-5">
                            <div>
                                <label for="category-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama kategori<span class="text-error-500">*</span>
                                </label>
                                <input id="category-name" name="name" type="text" maxlength="255"
                                    value="{{ old('name', $editingCategory->name) }}" required autofocus
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('name')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="category-slug" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Slug</label>
                                <input id="category-slug" name="slug" type="text" maxlength="255"
                                    value="{{ old('slug', $editingCategory->slug) }}" placeholder="Otomatis dari nama"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('slug')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($editingCategory->exists)
                                    <a href="{{ route('admin.kategori-dokumen.index') }}"
                                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                                        Batal
                                    </a>
                                @endif
                                <button type="submit" :disabled="submitting"
                                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/>
                                    </svg>
                                    <x-admin.spinner x-show="submitting" />
                                    {{ $editingCategory->exists ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Daftar Kategori</h4>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $categories->total() }} kategori tersimpan</p>
                    </div>
                    <a href="{{ route('admin.dokumen.index') }}"
                        class="inline-flex items-center gap-1.5 text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                        Lihat Dokumen
                        <svg class="stroke-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M5 12h14M12 5l7 7-7 7" stroke="" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>

                @if ($categories->isEmpty())
                    <div class="p-6 py-12 text-center">
                        <div class="mb-3 flex justify-center text-gray-400 dark:text-gray-500">
                            <svg class="fill-current" width="48" height="48" viewBox="0 0 24 24" fill="none">
                                <path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z" fill=""/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum ada kategori</p>
                        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tambahkan kategori pertama melalui formulir di samping.</p>
                    </div>
                @else
                    <div class="overflow-x-auto custom-scrollbar">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <th class="py-3 pr-4 pl-6 font-medium text-gray-500 dark:text-gray-400">Nama</th>
                                    <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Slug</th>
                                    <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Dokumen</th>
                                    <th class="py-3 pr-6 font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($categories as $category)
                                    <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                        <td class="py-3 pr-4 pl-6 font-medium text-gray-800 dark:text-white/90">{{ $category->name }}</td>
                                        <td class="py-3 pr-4">
                                            <code class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-white/5 dark:text-gray-400">{{ $category->slug }}</code>
                                        </td>
                                        <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">{{ $category->documents_count }}</td>
                                        <td class="py-3 pr-6">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.kategori-dokumen.index', ['edit' => $category->id]) }}"
                                                    class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition">
                                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                        <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill=""/>
                                                    </svg>
                                                </a>
                                                <form id="delete-category-{{ $category->id }}" action="{{ route('admin.kategori-dokumen.destroy', $category) }}" method="post" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                        onclick="return confirm('Hapus kategori {{ $category->name }}?')"
                                                        @disabled($category->documents_count > 0)
                                                        title="{{ $category->documents_count > 0 ? 'Kategori masih digunakan dokumen' : 'Hapus kategori' }}"
                                                        class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 dark:hover:text-error-500 transition disabled:opacity-30 disabled:cursor-not-allowed">
                                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                            <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if ($categories->lastPage() > 1)
                    <div class="border-t border-gray-100 px-6 py-4 dark:border-gray-800">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
