@extends('layouts.admin')

@section('title', 'Kegiatan - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Daftar Kegiatan')
@section('page-helper', 'Kelola berita kegiatan, jadwal tayang, lokasi, dan status publikasi.')

@section('content')
    <div class="activity-toolbar">
        <form class="activity-filter-form" action="{{ route('admin.kegiatan.index') }}" method="get">
            <label class="activity-filter-search">
                <span class="sr-only">Cari judul atau lokasi</span>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    maxlength="100"
                    placeholder="Cari judul atau lokasi"
                >
            </label>

            <div class="activity-filter-select">
                <label class="sr-only" for="activity-filter-status">Filter status</label>
                <select id="activity-filter-status" name="status">
                    <option value="">Semua status</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draf</option>
                    <option value="scheduled" @selected(($filters['status'] ?? '') === 'scheduled')>Terjadwal</option>
                    <option value="published" @selected(($filters['status'] ?? '') === 'published')>Terbit</option>
                </select>
            </div>

            <div class="activity-filter-date">
                <label class="sr-only" for="activity-filter-date">Filter tanggal kegiatan</label>
                <input
                    id="activity-filter-date"
                    type="date"
                    name="activity_date"
                    value="{{ $filters['activity_date'] ?? '' }}"
                    data-admin-date-picker
                >
            </div>

            <button class="admin-button admin-button-secondary" type="submit">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Terapkan
            </button>

            @if (array_filter($filters, fn ($value) => filled($value)))
                <a class="activity-reset-filter" href="{{ route('admin.kegiatan.index') }}">Reset</a>
            @endif
        </form>

        <a class="admin-button admin-button-primary" href="{{ route('admin.kegiatan.create') }}">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Tambah Kegiatan
        </a>
    </div>

    <section class="admin-panel activity-list-panel">
        @if ($activities->isEmpty())
            <x-admin.empty-state
                title="Kegiatan tidak ditemukan"
                message="Belum ada kegiatan yang cocok dengan filter. Tambahkan kegiatan baru atau ubah filter pencarian."
                icon="fa-calendar-xmark"
                action-label="Tambah Kegiatan"
                :action-url="route('admin.kegiatan.create')"
            />
        @else
            <div class="admin-table-wrap">
                <table class="admin-table activity-table">
                    <thead>
                        <tr>
                            <th scope="col">Gambar</th>
                            <th scope="col">Judul</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col" class="col-location">Lokasi</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="col-schedule">Jadwal Tayang</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($activities as $activity)
                            <tr>
                                <td>
                                    @if ($activity->image)
                                        <img class="activity-thumbnail" src="{{ asset('storage/'.$activity->image) }}" alt="Gambar {{ $activity->title }}" loading="lazy">
                                    @else
                                        <span class="activity-thumbnail" title="Belum ada gambar">
                                            <i class="fa-regular fa-image" aria-hidden="true"></i>
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <a class="activity-title-link" href="{{ route('admin.kegiatan.edit', $activity) }}">
                                        {{ $activity->title }}
                                    </a>
                                    <small>{{ $activity->slug }}</small>
                                </td>
                                <td>{{ $activity->activity_date?->translatedFormat('d M Y') ?? '-' }}</td>
                                <td class="col-location">{{ $activity->location ?: '-' }}</td>
                                <td>
                                    <span @class([
                                        'admin-content-badge',
                                        'admin-content-badge-draft' => $activity->status === 'draft',
                                        'admin-content-badge-scheduled' => $activity->status === 'scheduled',
                                        'admin-content-badge-published' => $activity->status === 'published',
                                    ])>
                                        {{ $activity->statusLabel() }}
                                    </span>
                                </td>
                                <td class="col-schedule">
                                    {{ $activity->published_at?->locale('id')->translatedFormat('d M Y, H.i') ?? '-' }}
                                </td>
                                <td>
                                    <div class="activity-row-actions">
                                        <a href="{{ route('admin.kegiatan.edit', $activity) }}" aria-label="Edit {{ $activity->title }}">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                        </a>
                                        <button
                                            type="button"
                                            class="activity-delete-button"
                                            data-delete-trigger
                                            data-delete-form="delete-activity-{{ $activity->id }}"
                                            data-delete-name="{{ $activity->title }}"
                                            aria-label="Hapus {{ $activity->title }}"
                                        >
                                            <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <form
                                        id="delete-activity-{{ $activity->id }}"
                                        action="{{ route('admin.kegiatan.destroy', $activity) }}"
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

    @if ($activities->total() > 0)
        <nav class="activity-pagination" aria-label="Navigasi halaman kegiatan">
            <p>
                Menampilkan {{ $activities->firstItem() }}-{{ $activities->lastItem() }}
                dari {{ $activities->total() }} kegiatan
            </p>
            <div>
                @if ($activities->onFirstPage())
                    <span aria-disabled="true">Sebelumnya</span>
                @else
                    <a href="{{ $activities->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                @endif

                <strong>Halaman {{ $activities->currentPage() }} dari {{ $activities->lastPage() }}</strong>

                @if ($activities->hasMorePages())
                    <a href="{{ $activities->nextPageUrl() }}" rel="next">Berikutnya</a>
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
            <h2 id="delete-dialog-title">Hapus kegiatan?</h2>
            <p>
                Kegiatan <strong data-delete-name-output></strong> akan dihapus permanen dari database.
            </p>
            <div>
                <button class="admin-button admin-button-secondary" type="submit" value="cancel" data-delete-cancel>Batal</button>
                <button class="admin-button activity-danger-button" type="button" data-delete-confirm>Hapus</button>
            </div>
        </form>
    </dialog>
@endsection
