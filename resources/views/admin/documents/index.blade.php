@extends('layouts.admin')

@section('title', 'Dokumen | Pengelola Situs Prodi')
@section('page-heading', 'Daftar Dokumen')

@section('content')
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.dokumen.index') }}" method="get" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill=""/>
                        </svg>
                    </span>
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" maxlength="100" placeholder="Cari dokumen"
                        class="dark:bg-dark-900 h-11 w-48 rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>
                <x-admin.select name="document_category_id" placeholder="Semua kategori" :selected="(string) ($filters['document_category_id'] ?? '')" :options="$categories->pluck('name', 'id')->mapWithKeys(fn ($name, $id) => [(string) $id => $name])->all()" />
                <x-admin.select name="status" placeholder="Semua status" :selected="$filters['status'] ?? ''" :options="['draft' => 'Draf', 'published' => 'Terbit']" />
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" fill=""/></svg> Terapkan
                </button>
                @if (array_filter($filters, fn ($v) => filled($v)))
                    <a href="{{ route('admin.dokumen.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.dokumen.create') }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm4 18H6V4h7v5h5v11z" fill=""/><path d="M14 2v5h5" stroke="white" stroke-width="1.5"/></svg> Tambah Dokumen
            </a>
        </div>

        <h4 class="mb-4 text-base font-medium text-gray-800 dark:text-white/90">{{ $documents->total() }} dokumen</h4>

        @if ($documents->isEmpty())
            <div class="py-12 text-center">
                <div class="mb-3 flex justify-center text-gray-400 dark:text-gray-500">
                    <svg class="fill-current" width="48" height="48" viewBox="0 0 24 24" fill="none"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill=""/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Dokumen tidak ditemukan</p>
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Dokumen</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Kategori</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Tipe</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Ukuran</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="py-3 font-medium text-gray-500 dark:text-gray-400 w-44">Diunggah/Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                <td class="py-3 pr-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                                            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill=""/></svg>
                                        </span>
                                        <div>
                                            <a href="{{ route('admin.dokumen.edit', $document) }}" class="font-medium text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400">{{ $document->title }}</a>
                                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ Str::limit($document->description ?: 'Tanpa deskripsi', 50) }}</p>
                                            @unless ($document->file_exists)<p class="text-xs text-error-500">Berkas tidak ditemukan</p>@endunless
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400">{{ $document->documentCategory->name }}</td>
                                <td class="py-3 pr-4"><span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-xs font-medium text-gray-700 dark:bg-white/5 dark:text-white/80">{{ $document->fileTypeLabel() }}</span></td>
                                <td class="py-3 pr-4 text-gray-500 dark:text-gray-400">{{ $document->formattedFileSize() }}</td>
                                <td class="py-3 pr-4">
                                    <x-admin.badge variant="light" :color="$document->status === 'published' ? 'success' : 'light'" size="sm">{{ $document->status === 'published' ? 'Terbit' : 'Draf' }}</x-admin.badge>
                                </td>
                                <td class="py-3">
                                    <div class="flex flex-col gap-1 text-xs text-gray-500 dark:text-gray-400">
                                        @if ($document->uploaded_at)
                                            <time datetime="{{ $document->uploaded_at->toAtomString() }}">{{ $document->uploaded_at->translatedFormat('d M Y, H:i') }}</time>
                                        @endif
                                        <div class="flex items-center gap-1 mt-1">
                                            @if ($document->file_exists)
                                                <a href="{{ route('admin.dokumen.download', $document) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition" title="Unduh">
                                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" fill=""/></svg>
                                                </a>
                                            @endif
                                            <a href="{{ route('admin.dokumen.edit', $document) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition" title="Edit">
                                                <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill=""/></svg>
                                            </a>
                                            <form action="{{ route('admin.dokumen.status', $document) }}" method="post" class="inline">
                                                @csrf @method('PATCH')
                                                <x-admin.toggle :active="$document->status === 'published'" variant="switch" :labelActive="'Terbitkan ' . $document->title" :labelInactive="'Draftkan ' . $document->title" />
                                            </form>
                                            <button type="button" onclick="if(confirm('Hapus {{ $document->title }}?')) document.getElementById('delete-doc-{{ $document->id }}').submit()" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition" title="Hapus">
                                                <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/></svg>
                                            </button>
                                            <form id="delete-doc-{{ $document->id }}" action="{{ route('admin.dokumen.destroy', $document) }}" method="post" hidden>@csrf @method('DELETE')</form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($documents->total() > 0)
            <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <p>Menampilkan {{ $documents->firstItem() }}-{{ $documents->lastItem() }} dari {{ $documents->total() }} dokumen</p>
                    <div class="flex items-center gap-4">
                        @if ($documents->onFirstPage())<span class="text-gray-300 dark:text-gray-600">Sebelumnya</span>@else<a href="{{ $documents->previousPageUrl() }}" class="hover:text-gray-700 dark:hover:text-gray-300">Sebelumnya</a>@endif
                        <span class="font-medium text-gray-700 dark:text-gray-300">Hlm {{ $documents->currentPage() }}/{{ $documents->lastPage() }}</span>
                        @if ($documents->hasMorePages())<a href="{{ $documents->nextPageUrl() }}" class="hover:text-gray-700 dark:hover:text-gray-300">Berikutnya</a>@else<span class="text-gray-300 dark:text-gray-600">Berikutnya</span>@endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
