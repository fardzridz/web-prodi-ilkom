@extends('layouts.admin')

@section('title', 'Kegiatan | Pengelola Situs Prodi')
@section('page-heading', 'Daftar Kegiatan')

@section('content')
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.kegiatan.index') }}" method="get" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill=""/>
                        </svg>
                    </span>
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" maxlength="100" placeholder="Cari judul atau lokasi"
                        class="dark:bg-dark-900 h-11 w-48 rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>
                <x-admin.select name="status" placeholder="Semua status" :selected="$filters['status'] ?? ''" :options="['draft' => 'Draf', 'scheduled' => 'Terjadwal', 'published' => 'Terbit']" />
                <input type="date" name="activity_date" value="{{ $filters['activity_date'] ?? '' }}"
                    class="dark:bg-dark-900 h-11 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-700 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" fill=""/></svg> Terapkan
                </button>
                @if (array_filter($filters, fn ($v) => filled($v)))
                    <a href="{{ route('admin.kegiatan.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.kegiatan.create') }}" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill=""/></svg> Tambah Kegiatan
            </a>
        </div>

        <h4 class="mb-4 text-base font-medium text-gray-800 dark:text-white/90">{{ $activities->total() }} kegiatan</h4>

        @if ($activities->isEmpty())
            <div class="py-12 text-center">
                <div class="mb-3 flex justify-center text-gray-400 dark:text-gray-500">
                    <svg class="fill-current" width="48" height="48" viewBox="0 0 24 24" fill="none"><path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2z" fill=""/></svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Kegiatan tidak ditemukan</p>
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 pr-2 font-medium text-gray-500 dark:text-gray-400 w-14"></th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Judul</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Tanggal</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Lokasi</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Tayang</th>
                            <th class="py-3 font-medium text-gray-500 dark:text-gray-400 w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                <td class="py-3 pr-2">
                                    @if ($activity->image)
                                        <img src="{{ asset('storage/'.$activity->image) }}" alt="" class="h-10 w-16 rounded object-cover" loading="lazy" />
                                    @else
                                        <span class="flex h-10 w-16 items-center justify-center rounded bg-gray-100 dark:bg-white/5 text-gray-400">
                                            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" fill=""/></svg>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">
                                    <a href="{{ route('admin.kegiatan.edit', $activity) }}" class="font-medium text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400">{{ $activity->title }}</a>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->slug }}</p>
                                </td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400">{{ $activity->activity_date?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400">{{ $activity->location ?: '-' }}</td>
                                <td class="py-3 pr-4">
                                    @php
                                        $statusColors = ['draft' => 'warning', 'scheduled' => 'info', 'published' => 'success'];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium text-sm bg-{{ $statusColors[$activity->status] }}-50 text-{{ $statusColors[$activity->status] }}-600 dark:bg-{{ $statusColors[$activity->status] }}-500/15 dark:text-{{ $statusColors[$activity->status] }}-500">{{ $activity->statusLabel() }}</span>
                                </td>
                                <td class="py-3 pr-4 text-sm text-gray-500 dark:text-gray-400">{{ $activity->published_at?->locale('id')->translatedFormat('d M Y, H.i') ?? '-' }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.kegiatan.edit', $activity) }}" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition" title="Edit">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill=""/></svg>
                                        </a>
                                        <button type="button" onclick="if(confirm('Hapus {{ $activity->title }}?')) document.getElementById('delete-activity-{{ $activity->id }}').submit()" class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition" title="Hapus">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/></svg>
                                        </button>
                                        <form id="delete-activity-{{ $activity->id }}" action="{{ route('admin.kegiatan.destroy', $activity) }}" method="post" hidden>@csrf @method('DELETE')</form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($activities->total() > 0)
            <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <p>Menampilkan {{ $activities->firstItem() }}-{{ $activities->lastItem() }} dari {{ $activities->total() }} kegiatan</p>
                    <div class="flex items-center gap-4">
                        @if ($activities->onFirstPage())<span class="text-gray-300 dark:text-gray-600">Sebelumnya</span>@else<a href="{{ $activities->previousPageUrl() }}" class="hover:text-gray-700 dark:hover:text-gray-300">Sebelumnya</a>@endif
                        <span class="font-medium text-gray-700 dark:text-gray-300">Hlm {{ $activities->currentPage() }}/{{ $activities->lastPage() }}</span>
                        @if ($activities->hasMorePages())<a href="{{ $activities->nextPageUrl() }}" class="hover:text-gray-700 dark:hover:text-gray-300">Berikutnya</a>@else<span class="text-gray-300 dark:text-gray-600">Berikutnya</span>@endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
