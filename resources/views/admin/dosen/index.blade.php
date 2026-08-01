@extends('layouts.admin')

@section('title', 'Dosen - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Daftar Dosen')
@section('page-helper', 'Kelola data dosen Program Studi Ilmu Komputer.')

@section('content')
    <section class="admin-panel activity-list-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Photo</th>
                        <th scope="col">Nama</th>
                        <th scope="col">NIDN</th>
                        <th scope="col">Jabatan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Urutan</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lecturers as $lecturer)
                    <tr>
                        <td>
                            @if($lecturer->photo)
                                <img src="{{ asset('storage/' . $lecturer->photo) }}" alt="{{ $lecturer->name }}" width="40" class="rounded-circle">
                            @else
                                <i class="fa-solid fa-user"></i>
                            @endif
                        </td>
                        <td>{{ $lecturer->name }}</td>
                        <td>{{ $lecturer->nidn }}</td>
                        <td>{{ $lecturer->position }}</td>
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