@extends('layouts.admin')

@section('title', 'Halaman | Pengelola Situs Prodi')
@section('page-heading', 'Halaman Situs')

@section('content')
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
        <h3 class="mb-5 text-lg font-semibold text-gray-800 dark:text-white/90 lg:mb-7">Daftar Halaman</h3>

        @if ($pages->isEmpty())
            <div class="py-12 text-center">
                <div class="mb-3 flex justify-center text-gray-400 dark:text-gray-500">
                    <svg class="fill-current" width="48" height="48" viewBox="0 0 24 24" fill="none">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm-3.06 16L7.4 14.46l1.41-1.41 2.12 2.12 4.24-4.24 1.41 1.41L10.94 18zM13 9V3.5L18.5 9H13z" fill=""/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Halaman tidak ditemukan</p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Belum ada halaman statis pada situs ini.</p>
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Halaman</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Slug</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="py-3 font-medium text-gray-500 dark:text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                <td class="py-3 pr-4">
                                    <a href="{{ route('admin.halaman.edit', ['slug' => $page->slug]) }}"
                                        class="font-medium text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400">
                                        {{ $page->title }}
                                    </a>
                                </td>
                                <td class="py-3 pr-4">
                                    <code class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-white/5 dark:text-gray-400">{{ $page->slug }}</code>
                                </td>
                                <td class="py-3 pr-4">
                                    @if (filled($page->content))
                                        <span class="inline-flex items-center px-2.5 py-0.5 justify-center gap-1 rounded-full font-medium capitalize text-sm bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                            Konten tersedia
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 justify-center gap-1 rounded-full font-medium capitalize text-sm bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400">
                                            Belum diisi
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3">
                                    <a href="{{ route('admin.halaman.edit', ['slug' => $page->slug]) }}"
                                        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400 dark:hover:text-brand-400">
                                        <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                            <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill=""/>
                                        </svg>
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
