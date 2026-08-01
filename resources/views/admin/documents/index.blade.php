@extends('layouts.admin')

@section('title', 'Dokumen - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Daftar Dokumen')
@section('page-helper', 'Kelola berkas akademik, kategori, dan status tayangnya.')

@section('content')
    <div class="activity-toolbar">
        <form class="activity-filter-form document-filter-form" action="{{ route('admin.dokumen.index') }}" method="get">
            <label class="activity-filter-search">
                <span class="sr-only">Cari judul atau deskripsi dokumen</span>
                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                <input
                    type="search"
                    name="search"
                    value="{{ $filters['search'] ?? '' }}"
                    maxlength="100"
                    placeholder="Cari dokumen"
                >
            </label>

            <div class="activity-filter-select">
                <label class="sr-only" for="document-filter-category">Filter kategori dokumen</label>
                <select id="document-filter-category" name="document_category_id">
                    <option value="">Semua kategori</option>
                    @foreach ($categories as $category)
                        <option
                            value="{{ $category->id }}"
                            @selected((string) ($filters['document_category_id'] ?? '') === (string) $category->id)
                        >
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="activity-filter-select">
                <label class="sr-only" for="document-filter-status">Filter status dokumen</label>
                <select id="document-filter-status" name="status">
                    <option value="">Semua status</option>
                    <option value="draft" @selected(($filters['status'] ?? '') === 'draft')>Draf</option>
                    <option value="published" @selected(($filters['status'] ?? '') === 'published')>Terbit</option>
                </select>
            </div>

            <button class="admin-button admin-button-secondary" type="submit">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Terapkan
            </button>

            @if (array_filter($filters, fn ($value) => filled($value)))
                <a class="activity-reset-filter" href="{{ route('admin.dokumen.index') }}">Reset</a>
            @endif
        </form>

        <a class="admin-button admin-button-primary" href="{{ route('admin.dokumen.create') }}">
            <i class="fa-solid fa-file-circle-plus" aria-hidden="true"></i>
            Tambah Dokumen
        </a>
    </div>

    <section class="admin-panel activity-list-panel">
        @if ($documents->isEmpty())
            <x-admin.empty-state
                title="Dokumen tidak ditemukan"
                message="Belum ada dokumen yang cocok dengan filter. Tambahkan dokumen baru atau ubah filter pencarian."
                icon="fa-file-circle-xmark"
                action-label="Tambah Dokumen"
                :action-url="route('admin.dokumen.create')"
            />
        @else
            <div class="admin-table-wrap">
                <table class="admin-table document-table">
                    <thead>
                        <tr>
                            <th scope="col">Dokumen</th>
                            <th scope="col">Kategori</th>
                            <th scope="col">Tipe</th>
                            <th scope="col">Ukuran</th>
                            <th scope="col">Status</th>
                            <th scope="col">Diunggah</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            @php($storedFileExists = Storage::disk('public')->exists($document->file))
                            <tr>
                                <td>
                                    <div class="document-identity">
                                        <span class="document-file-icon" aria-hidden="true">
                                            <i class="fa-solid fa-file-{{ $document->file_type === 'pdf' ? 'pdf' : 'word' }}"></i>
                                        </span>
                                        <div>
                                            <a class="activity-title-link" href="{{ route('admin.dokumen.edit', $document) }}">
                                                {{ $document->title }}
                                            </a>
                                            <small>{{ Str::limit($document->description ?: 'Tanpa deskripsi', 68) }}</small>
                                            @unless ($storedFileExists)
                                                <small class="document-file-missing">Berkas fisik tidak ditemukan</small>
                                            @endunless
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $document->documentCategory->name }}</td>
                                <td><span class="document-type-badge">{{ $document->fileTypeLabel() }}</span></td>
                                <td>{{ $document->formattedFileSize() }}</td>
                                <td>
                                    <span @class([
                                        'admin-content-badge',
                                        'admin-content-badge-draft' => $document->status === 'draft',
                                        'admin-content-badge-published' => $document->status === 'published',
                                    ])>
                                        {{ $document->statusLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if ($document->uploaded_at)
                                        <time datetime="{{ $document->uploaded_at->toAtomString() }}">
                                            {{ $document->uploaded_at->translatedFormat('d M Y, H:i') }}
                                        </time>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="activity-row-actions">
                                        @if ($storedFileExists)
                                            <a href="{{ route('admin.dokumen.download', $document) }}" aria-label="Unduh {{ $document->title }}">
                                                <i class="fa-solid fa-download" aria-hidden="true"></i>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.dokumen.edit', $document) }}" aria-label="Edit {{ $document->title }}">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                        </a>

                                        <form action="{{ route('admin.dokumen.status', $document) }}" method="post">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                class="lecturer-toggle-button"
                                                aria-label="{{ $document->status === 'published' ? 'Jadikan draft' : 'Publikasikan' }} {{ $document->title }}"
                                            >
                                                <i @class([
                                                    'fa-solid',
                                                    'fa-toggle-on' => $document->status === 'published',
                                                    'fa-toggle-off' => $document->status === 'draft',
                                                ]) aria-hidden="true"></i>
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            class="activity-delete-button"
                                            data-delete-trigger
                                            data-delete-form="delete-document-{{ $document->id }}"
                                            data-delete-name="{{ $document->title }}"
                                            aria-label="Hapus {{ $document->title }}"
                                        >
                                            <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <form
                                        id="delete-document-{{ $document->id }}"
                                        action="{{ route('admin.dokumen.destroy', $document) }}"
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

    @if ($documents->total() > 0)
        <nav class="activity-pagination" aria-label="Navigasi halaman dokumen">
            <p>
                Menampilkan {{ $documents->firstItem() }}-{{ $documents->lastItem() }}
                dari {{ $documents->total() }} dokumen
            </p>
            <div>
                @if ($documents->onFirstPage())
                    <span aria-disabled="true">Sebelumnya</span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                @endif

                <strong>Halaman {{ $documents->currentPage() }} dari {{ $documents->lastPage() }}</strong>

                @if ($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" rel="next">Berikutnya</a>
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
            <h2 id="delete-dialog-title">Hapus dokumen?</h2>
            <p>
                Data dan berkas <strong data-delete-name-output></strong> akan dihapus permanen.
            </p>
            <div>
                <button class="admin-button admin-button-secondary" type="submit" value="cancel">Batal</button>
                <button class="admin-button activity-danger-button" type="button" data-delete-confirm>Hapus</button>
            </div>
        </form>
    </dialog>
@endsection
