@extends('layouts.admin')

@section('title', 'Dosen | Pengelola Situs Prodi')
@section('page-heading', 'Daftar Dosen')

@section('content')
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900 lg:p-6">
        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.dosen.index') }}" method="get" class="flex flex-wrap items-center gap-2">
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                        <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                            <path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" fill=""/>
                        </svg>
                    </span>
                    <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" maxlength="100"
                        placeholder="Cari nama, NIDN, atau keahlian"
                        class="dark:bg-dark-900 h-11 w-48 rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                </div>
                <x-admin.select name="status" placeholder="Semua status" :selected="$filters['status'] ?? ''" :options="['active' => 'Aktif', 'inactive' => 'Nonaktif']" />
                <button type="submit"
                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                        <path d="M10 18h4v-2h-4v2zM3 6v2h18V6H3zm3 7h12v-2H6v2z" fill=""/>
                    </svg>
                    Terapkan
                </button>
                @if (array_filter($filters, fn ($v) => filled($v)))
                    <a href="{{ route('admin.dosen.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Reset</a>
                @endif
            </form>
            <a href="{{ route('admin.dosen.create') }}"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition">
                <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                    <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill=""/>
                </svg>
                Tambah Dosen
            </a>
        </div>

        <h4 class="mb-4 text-base font-medium text-gray-800 dark:text-white/90">{{ $lecturers->total() }} dosen</h4>

        @if ($lecturers->isEmpty())
            <div class="py-12 text-center">
                <div class="mb-3 flex justify-center text-gray-400 dark:text-gray-500">
                    <svg class="fill-current" width="48" height="48" viewBox="0 0 24 24" fill="none">
                        <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z" fill=""/>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Data dosen tidak ditemukan</p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tambah dosen baru atau ubah filter pencarian.</p>
            </div>
        @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-gray-100 dark:border-gray-800">
                            <th class="py-3 pr-2 font-medium text-gray-500 dark:text-gray-400 w-14"></th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Nama</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">NIDN</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Jabatan</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Keahlian</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400">Status</th>
                            <th class="py-3 pr-4 font-medium text-gray-500 dark:text-gray-400 text-center">Urutan</th>
                            <th class="py-3 font-medium text-gray-500 dark:text-gray-400 w-28">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lecturers as $lecturer)
                            @php($hasPhoto = filled($lecturer->photo) && Storage::disk('public')->exists($lecturer->photo))
                            <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0">
                                <td class="py-3 pr-2">
                                    @if ($hasPhoto)
                                        <img src="{{ Storage::disk('public')->url($lecturer->photo) }}" alt="Foto {{ $lecturer->name }}"
                                            class="h-10 w-10 rounded-full object-cover" loading="lazy" />
                                    @else
                                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-50 text-sm font-semibold text-brand-500 dark:bg-brand-500/15">
                                            {{ Str::upper(Str::substr($lecturer->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4">
                                    <a href="{{ route('admin.dosen.edit', $lecturer) }}" class="font-medium text-gray-800 hover:text-brand-500 dark:text-white/90 dark:hover:text-brand-400">
                                        {{ $lecturer->name }}
                                    </a>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $lecturer->email ?: 'Surel belum diisi' }}</p>
                                </td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400">{{ $lecturer->nidn }}</td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400">{{ $lecturer->position ?: '-' }}</td>
                                <td class="py-3 pr-4 text-gray-600 dark:text-gray-400">{{ $lecturer->expertise ?: '-' }}</td>
                                <td class="py-3 pr-4">
                                    @if ($lecturer->status === 'active')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium text-sm bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium text-sm bg-gray-100 text-gray-700 dark:bg-white/5 dark:text-white/80">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-center text-gray-600 dark:text-gray-400">{{ $lecturer->sort_order }}</td>
                                <td class="py-3">
                                    <div class="flex items-center gap-1">
                                        <a href="{{ route('admin.dosen.edit', $lecturer) }}"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10 dark:hover:text-brand-400 transition" title="Edit">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z" fill=""/>
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.dosen.status', $lecturer) }}" method="post" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-{{ $lecturer->status === 'active' ? 'success' : 'error' }}-500 hover:bg-{{ $lecturer->status === 'active' ? 'success' : 'error' }}-50 dark:hover:bg-{{ $lecturer->status === 'active' ? 'success' : 'error' }}-500/10 transition"
                                                title="{{ $lecturer->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                @if ($lecturer->status === 'active')
                                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                        <path d="M17 6H7c-3.31 0-6 2.69-6 6s2.69 6 6 6h10c3.31 0 6-2.69 6-6s-2.69-6-6-6zm0 10H7c-2.21 0-4-1.79-4-4s1.79-4 4-4h10c2.21 0 4 1.79 4 4s-1.79 4-4 4zm0-7c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill=""/>
                                                    </svg>
                                                @else
                                                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                        <path d="M17 6H7c-3.31 0-6 2.69-6 6s2.69 6 6 6h10c3.31 0 6-2.69 6-6s-2.69-6-6-6zM7 16c-2.21 0-4-1.79-4-4s1.79-4 4-4h10c2.21 0 4 1.79 4 4s-1.79 4-4 4H7z" fill=""/>
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                        <button type="button"
                                            onclick="if(confirm('Hapus data {{ $lecturer->name }}?')) document.getElementById('delete-lecturer-{{ $lecturer->id }}').submit()"
                                            class="inline-flex items-center justify-center h-8 w-8 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition" title="Hapus">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/>
                                            </svg>
                                        </button>
                                        <form id="delete-lecturer-{{ $lecturer->id }}" action="{{ route('admin.dosen.destroy', $lecturer) }}" method="post" hidden>
                                            @csrf @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if ($lecturers->total() > 0)
            <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
                <div class="flex flex-wrap items-center justify-between gap-2 text-sm text-gray-500 dark:text-gray-400">
                    <p>Menampilkan {{ $lecturers->firstItem() }}-{{ $lecturers->lastItem() }} dari {{ $lecturers->total() }} dosen</p>
                    <div class="flex items-center gap-4">
                        @if ($lecturers->onFirstPage())
                            <span class="text-gray-300 dark:text-gray-600">Sebelumnya</span>
                        @else
                            <a href="{{ $lecturers->previousPageUrl() }}" class="hover:text-gray-700 dark:hover:text-gray-300">Sebelumnya</a>
                        @endif
                        <span class="font-medium text-gray-700 dark:text-gray-300">Halaman {{ $lecturers->currentPage() }} dari {{ $lecturers->lastPage() }}</span>
                        @if ($lecturers->hasMorePages())
                            <a href="{{ $lecturers->nextPageUrl() }}" class="hover:text-gray-700 dark:hover:text-gray-300">Berikutnya</a>
                        @else
                            <span class="text-gray-300 dark:text-gray-600">Berikutnya</span>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
