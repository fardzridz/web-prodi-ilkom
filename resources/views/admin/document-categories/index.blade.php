@extends('layouts.admin')

@section('title', 'Kategori Dokumen - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Kategori Dokumen')
@section('page-helper', 'Kelompokkan dokumen agar berkas akademik mudah ditemukan.')

@section('content')
    <div class="document-category-layout">
        <section class="admin-panel document-category-form-panel">
            <div class="document-category-heading">
                <span aria-hidden="true"><i class="fa-solid fa-folder-plus"></i></span>
                <div>
                    <h2>{{ $editingCategory->exists ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                    <p>Slug dapat dibuat otomatis dari nama kategori.</p>
                </div>
            </div>

            <form
                action="{{ $editingCategory->exists ? route('admin.kategori-dokumen.update', $editingCategory) : route('admin.kategori-dokumen.store') }}"
                method="post"
            >
                @csrf
                @if ($editingCategory->exists)
                    @method('PUT')
                @endif

                <div class="activity-field">
                    <label for="category-name">Nama kategori <span aria-hidden="true">*</span></label>
                    <input
                        id="category-name"
                        name="name"
                        type="text"
                        value="{{ old('name', $editingCategory->name) }}"
                        maxlength="255"
                        required
                        autofocus
                        @error('name') aria-invalid="true" aria-describedby="category-name-error" @enderror
                    >
                    @error('name')<small id="category-name-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>

                <div class="activity-field">
                    <label for="category-slug">Slug</label>
                    <input
                        id="category-slug"
                        name="slug"
                        type="text"
                        value="{{ old('slug', $editingCategory->slug) }}"
                        maxlength="255"
                        placeholder="Otomatis dari nama"
                        @error('slug') aria-invalid="true" aria-describedby="category-slug-error" @enderror
                    >
                    @error('slug')<small id="category-slug-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>

                <div class="document-category-form-actions">
                    @if ($editingCategory->exists)
                        <a class="admin-button admin-button-secondary" href="{{ route('admin.kategori-dokumen.index') }}">Batal</a>
                    @endif
                    <button class="admin-button admin-button-primary" type="submit">
                        <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
                        {{ $editingCategory->exists ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </form>
        </section>

        <section class="admin-panel activity-list-panel document-category-list-panel">
            <div class="document-category-list-heading">
                <div>
                    <h2>Daftar Kategori</h2>
                    <p>{{ $categories->total() }} kategori tersimpan</p>
                </div>
                <a class="document-category-link" href="{{ route('admin.dokumen.index') }}">
                    Lihat Dokumen
                    <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>

            @if ($categories->isEmpty())
                <x-admin.empty-state
                    title="Belum ada kategori"
                    message="Tambahkan kategori pertama melalui formulir di halaman ini."
                    icon="fa-folder-open"
                />
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table document-category-table">
                        <thead>
                            <tr>
                                <th scope="col">Nama</th>
                                <th scope="col">Slug</th>
                                <th scope="col">Dokumen</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td><code>{{ $category->slug }}</code></td>
                                    <td>{{ $category->documents_count }}</td>
                                    <td>
                                        <div class="activity-row-actions">
                                            <a
                                                href="{{ route('admin.kategori-dokumen.index', ['edit' => $category->id]) }}"
                                                aria-label="Edit {{ $category->name }}"
                                            >
                                                <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                            </a>

                                            <button
                                                type="button"
                                                class="activity-delete-button"
                                                data-delete-trigger
                                                data-delete-form="delete-category-{{ $category->id }}"
                                                data-delete-name="{{ $category->name }}"
                                                aria-label="Hapus {{ $category->name }}"
                                                @disabled($category->documents_count > 0)
                                                title="{{ $category->documents_count > 0 ? 'Kategori masih digunakan dokumen' : 'Hapus kategori' }}"
                                            >
                                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>

                                        <form
                                            id="delete-category-{{ $category->id }}"
                                            action="{{ route('admin.kategori-dokumen.destroy', $category) }}"
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

            @if ($categories->lastPage() > 1)
                <nav class="activity-pagination document-category-pagination" aria-label="Navigasi halaman kategori dokumen">
                    <p>Halaman {{ $categories->currentPage() }} dari {{ $categories->lastPage() }}</p>
                    <div>
                        @if ($categories->onFirstPage())
                            <span aria-disabled="true">Sebelumnya</span>
                        @else
                            <a href="{{ $categories->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                        @endif
                        @if ($categories->hasMorePages())
                            <a href="{{ $categories->nextPageUrl() }}" rel="next">Berikutnya</a>
                        @else
                            <span aria-disabled="true">Berikutnya</span>
                        @endif
                    </div>
                </nav>
            @endif
        </section>
    </div>

    <dialog class="admin-delete-dialog" data-delete-dialog aria-labelledby="delete-dialog-title">
        <form method="dialog">
            <span class="admin-delete-dialog-icon" aria-hidden="true">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>
            <h2 id="delete-dialog-title">Hapus kategori?</h2>
            <p>Kategori <strong data-delete-name-output></strong> akan dihapus permanen.</p>
            <div>
                <button class="admin-button admin-button-secondary" type="submit" value="cancel">Batal</button>
                <button class="admin-button activity-danger-button" type="button" data-delete-confirm>Hapus</button>
            </div>
        </form>
    </dialog>
@endsection
