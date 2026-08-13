@extends('layouts.admin')

@section('title', $page->title.' | Pengelola Situs Prodi')
@section('page-heading', $page->title)

@section('content')
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Editor Konten Halaman</h3>

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill=""/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Gunakan toolbar untuk memformat konten. Konten ditampilkan di halaman publik {{ $page->title }}.</p>
                </div>
            </div>
            @if ($page->updated_at)
                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                    Terakhir diperbarui {{ $page->updated_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
            @endif
        </div>

        <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('admin.halaman.update', ['slug' => $page->slug]) }}" method="post">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <label for="page-content" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Konten halaman</label>
                <div id="page-content" class="quill-editor"></div>
                <input id="page-content-hidden" type="hidden" name="content" value="{{ old('content', $page->content) }}">
                @error('content')
                    <p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.halaman') }}"
                    class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                    Batal
                </a>
                <button type="submit" :disabled="submitting"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/>
                    </svg>
                    <x-admin.spinner x-show="submitting" />
                    Simpan Halaman
                </button>
            </div>
        </form>
    </div>
@endsection
