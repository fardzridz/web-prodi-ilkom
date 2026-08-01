@extends('layouts.admin')

@section('title', 'Dosen - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Daftar Dosen')
@section('page-helper', 'Kelola profil dosen, status tampil, dan urutan pada halaman publik.')

@section('content')
    <div class="activity-toolbar">
        <form class="activity-filter-form" action="{{ route('admin.dosen.index') }}" method="get">
            <label class="activity-filter-search">
                <span class="sr-only">Cari nama, NIDN, jabatan, atau keahlian</span>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    maxlength="100"
                    placeholder="Cari nama, NIDN, atau keahlian"
                >
            </label>

            <div class="activity-filter-select">
                <label class="sr-only" for="lecturer-filter-status">Filter status dosen</label>
                <select id="lecturer-filter-status" name="status">
                    <option value="">Semua status</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Nonaktif</option>
                </select>
            </div>

            <button class="admin-button admin-button-secondary" type="submit">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Terapkan
            </button>

            @if (array_filter($filters, fn ($value) => filled($value)))
                <a class="activity-reset-filter" href="{{ route('admin.dosen.index') }}">Reset</a>
            @endif
        </form>

        <a class="admin-button admin-button-primary" href="{{ route('admin.dosen.create') }}">
            <i class="fa-solid fa-user-plus" aria-hidden="true"></i>
            Tambah Dosen
        </a>
    </div>

    <section class="admin-panel activity-list-panel">
        @if ($lecturers->isEmpty())
            <x-admin.empty-state
                title="Data dosen tidak ditemukan"
                message="Belum ada dosen yang cocok dengan filter. Tambahkan dosen baru atau ubah filter pencarian."
                icon="fa-user-slash"
                action-label="Tambah Dosen"
                :action-url="route('admin.dosen.create')"
            />
        @else
            <div class="admin-table-wrap">
                <table class="admin-table lecturer-table">
                    <thead>
                        <tr>
                            <th scope="col">Foto</th>
                            <th scope="col">Nama</th>
                            <th scope="col" class="col-nidn">NIDN</th>
                            <th scope="col">Jabatan</th>
                            <th scope="col" class="col-expertise">Keahlian</th>
                            <th scope="col">Status</th>
                            <th scope="col">Urutan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lecturers as $lecturer)
                            @php
                                $hasStoredPhoto = filled($lecturer->photo) && Storage::disk('public')->exists($lecturer->photo);
                            @endphp
                            <tr>
                                <td>
                                    @if ($hasStoredPhoto)
                                        <img
                                            class="lecturer-avatar"
                                            src="{{ Storage::disk('public')->url($lecturer->photo) }}"
                                            alt="Foto {{ $lecturer->name }}"
                                            width="48"
                                            height="48"
                                            loading="lazy"
                                        >
                                    @else
                                        <span class="lecturer-avatar lecturer-avatar-placeholder" aria-hidden="true">
                                            {{ Str::upper(Str::substr($lecturer->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="lecturer-identity">
                                    <a class="activity-title-link" href="{{ route('admin.dosen.edit', $lecturer) }}">
                                        {{ $lecturer->name }}
                                    </a>
                                    <small>{{ $lecturer->email ?: 'Surel belum diisi' }}</small>
                                </td>
                                <td class="col-nidn">{{ $lecturer->nidn }}</td>
                                <td>{{ $lecturer->position ?: '-' }}</td>
                                <td class="col-expertise">{{ $lecturer->expertise ?: '-' }}</td>
                                <td>
                                    <span @class([
                                        'admin-content-badge',
                                        'admin-content-badge-active' => $lecturer->status === 'active',
                                        'admin-content-badge-inactive' => $lecturer->status === 'inactive',
                                    ])>
                                        {{ $lecturer->statusLabel() }}
                                    </span>
                                </td>
                                <td>{{ $lecturer->sort_order }}</td>
                                <td>
                                    <div class="activity-row-actions">
                                        <a href="{{ route('admin.dosen.edit', $lecturer) }}" aria-label="Edit {{ $lecturer->name }}">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                        </a>

                                        <form action="{{ route('admin.dosen.status', $lecturer) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="lecturer-toggle-button"
                                                aria-label="{{ $lecturer->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }} {{ $lecturer->name }}"
                                            >
                                                <i @class([
                                                    'fa-solid',
                                                    'fa-toggle-on' => $lecturer->status === 'active',
                                                    'fa-toggle-off' => $lecturer->status === 'inactive',
                                                ]) aria-hidden="true"></i>
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="activity-delete-button"
                                            data-delete-trigger
                                            data-delete-form="delete-lecturer-{{ $lecturer->id }}"
                                            data-delete-name="{{ $lecturer->name }}"
                                            aria-label="Hapus {{ $lecturer->name }}"
                                        >
                                            <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <form
                                        id="delete-lecturer-{{ $lecturer->id }}"
                                        action="{{ route('admin.dosen.destroy', $lecturer) }}"
                                        method="post"
                                        hidden
                                    >
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    @if ($lecturers->total() > 0)
        <nav class="activity-pagination" aria-label="Navigasi halaman dosen">
            <p>
                Menampilkan {{ $lecturers->firstItem() }}-{{ $lecturers->lastItem() }}
                dari {{ $lecturers->total() }} dosen
            </p>
            <div>
                @if ($lecturers->onFirstPage())
                    <span aria-disabled="true">Sebelumnya</span>
                @else
                    <a href="{{ $lecturers->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                @endif

                <strong>Halaman {{ $lecturers->currentPage() }} dari {{ $lecturers->lastPage() }}</strong>

                @if ($lecturers->hasMorePages())
                    <a href="{{ $lecturers->nextPageUrl() }}" rel="next">Berikutnya</a>
                @else
                    <span aria-disabled="true">Berikutnya</span>
                @endif
            </div>
        </nav>
    @endif

    <dialog class="admin-delete-dialog" data-delete-dialog aria-labelledby="delete-dialog-title">
        <form method="dialog">
            <span class="admin-delete-dialog-icon" aria-hidden="true">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <h2 id="delete-dialog-title">Hapus data dosen?</h2>
            <p>
                Data <strong data-delete-name-output></strong> akan dihapus permanen dari database.
            </p>
            <div>
                <button class="admin-button admin-button-secondary" type="submit" value="cancel">Batal</button>
                <button class="admin-button activity-danger-button" type="button" data-delete-confirm>Hapus</button>
            </div>
        </form>
    </dialog>
@endsection
