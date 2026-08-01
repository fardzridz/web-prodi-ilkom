@extends('layouts.admin')

@section('title', 'Alumni - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Daftar Alumni')
@section('page-helper', 'Kelola profil alumni yang dapat ditampilkan pada halaman publik.')

@section('content')
    <div class="activity-toolbar">
        <form class="activity-filter-form alumni-filter-form" action="{{ route('admin.alumni.index') }}" method="get">
            <label class="activity-filter-search">
                <span class="sr-only">Cari nama, pekerjaan, atau perusahaan alumni</span>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    maxlength="100"
                    placeholder="Cari nama atau perusahaan"
                >
            </label>

            <div class="activity-filter-select">
                <label class="sr-only" for="alumni-filter-status">Filter status alumni</label>
                <select id="alumni-filter-status" name="status">
                    <option value="">Semua status</option>
                    <option value="active" @selected(($filters['status'] ?? '') === 'active')>Aktif</option>
                    <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Nonaktif</option>
                </select>
            </div>

            <div class="activity-filter-select">
                <label class="sr-only" for="alumni-filter-batch">Filter tahun angkatan alumni</label>
                <select id="alumni-filter-batch" name="batch_year">
                    <option value="">Semua angkatan</option>
                    @foreach ($batchYears as $batchYear)
                        <option value="{{ $batchYear }}" @selected((string) ($filters['batch_year'] ?? '') === (string) $batchYear)>
                            {{ $batchYear }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button class="admin-button admin-button-secondary" type="submit">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Terapkan
            </button>

            @if (array_filter($filters, fn ($value) => filled($value)))
                <a class="activity-reset-filter" href="{{ route('admin.alumni.index') }}">Reset</a>
            @endif
        </form>

        <a class="admin-button admin-button-primary" href="{{ route('admin.alumni.create') }}">
            <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
            Tambah Alumni
        </a>
    </div>

    <section class="admin-panel activity-list-panel">
        @if ($alumni->isEmpty())
            <x-admin.empty-state
                title="Alumni tidak ditemukan"
                message="Belum ada alumni yang cocok dengan filter. Tambahkan alumni baru atau ubah filter pencarian."
                icon="fa-user-graduate"
                action-label="Tambah Alumni"
                :action-url="route('admin.alumni.create')"
            />
        @else
            <div class="admin-table-wrap">
                <table class="admin-table alumni-table">
                    <thead>
                        <tr>
                            <th scope="col">Foto</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Angkatan</th>
                            <th scope="col" class="col-graduation">Lulus</th>
                            <th scope="col" class="col-position">Posisi</th>
                            <th scope="col">Perusahaan</th>
                            <th scope="col">Status</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($alumni as $alumnus)
                            @php($hasStoredPhoto = filled($alumnus->photo) && Storage::disk('public')->exists($alumnus->photo))
                            <tr>
                                <td>
                                    @if ($hasStoredPhoto)
                                        <img
                                            class="lecturer-avatar"
                                            src="{{ Storage::disk('public')->url($alumnus->photo) }}"
                                            alt="Foto {{ $alumnus->name }}"
                                            width="48"
                                            height="48"
                                            loading="lazy"
                                        >
                                    @else
                                        <span class="lecturer-avatar lecturer-avatar-placeholder" aria-hidden="true">
                                            {{ Str::upper(Str::substr($alumnus->name, 0, 2)) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="alumni-identity">
                                    <a class="activity-title-link" href="{{ route('admin.alumni.edit', $alumnus) }}">
                                        {{ $alumnus->name }}
                                    </a>
                                    <small>{{ Str::limit($alumnus->testimonial ?: 'Testimoni belum diisi', 70) }}</small>
                                </td>
                                <td>{{ $alumnus->batch_year }}</td>
                                <td class="col-graduation">{{ $alumnus->graduation_year ?: '-' }}</td>
                                <td class="col-position">{{ $alumnus->job_position ?: '-' }}</td>
                                <td>{{ $alumnus->company ?: '-' }}</td>
                                <td>
                                    <span @class([
                                        'admin-content-badge',
                                        'admin-content-badge-active' => $alumnus->status === 'active',
                                        'admin-content-badge-inactive' => $alumnus->status === 'inactive',
                                    ])>
                                        {{ $alumnus->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="activity-row-actions">
                                        <a href="{{ route('admin.alumni.edit', $alumnus) }}" aria-label="Edit {{ $alumnus->name }}">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                        </a>

                                        <form action="{{ route('admin.alumni.status', $alumnus) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="lecturer-toggle-button"
                                                aria-label="{{ $alumnus->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }} {{ $alumnus->name }}"
                                            >
                                                <i @class([
                                                    'fa-solid',
                                                    'fa-toggle-on' => $alumnus->status === 'active',
                                                    'fa-toggle-off' => $alumnus->status === 'inactive',
                                                ]) aria-hidden="true"></i>
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="activity-delete-button"
                                            data-delete-trigger
                                            data-delete-form="delete-alumni-{{ $alumnus->id }}"
                                            data-delete-name="{{ $alumnus->name }}"
                                            aria-label="Hapus {{ $alumnus->name }}"
                                        >
                                            <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <form
                                        id="delete-alumni-{{ $alumnus->id }}"
                                        action="{{ route('admin.alumni.destroy', $alumnus) }}"
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

    @if ($alumni->total() > 0)
        <nav class="activity-pagination" aria-label="Navigasi halaman alumni">
            <p>
                Menampilkan {{ $alumni->firstItem() }}-{{ $alumni->lastItem() }}
                dari {{ $alumni->total() }} alumni
            </p>
            <div>
                @if ($alumni->onFirstPage())
                    <span aria-disabled="true">Sebelumnya</span>
                @else
                    <a href="{{ $alumni->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                @endif

                <strong>Halaman {{ $alumni->currentPage() }} dari {{ $alumni->lastPage() }}</strong>

                @if ($alumni->hasMorePages())
                    <a href="{{ $alumni->nextPageUrl() }}" rel="next">Berikutnya</a>
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
            <h2 id="delete-dialog-title">Hapus data alumni?</h2>
            <p>Data <strong data-delete-name-output></strong> akan dihapus permanen dari database.</p>
            <div>
                <button class="admin-button admin-button-secondary" type="submit" value="cancel">Batal</button>
                <button class="admin-button activity-danger-button" type="button" data-delete-confirm>Hapus</button>
            </div>
        </form>
    </dialog>
@endsection
