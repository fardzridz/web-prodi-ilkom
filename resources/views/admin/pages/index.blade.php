@extends('layouts.admin')

@section('title', 'Halaman - Pengelola Situs Prodi')
@section('page-section', 'Halaman')
@section('page-heading', 'Halaman Situs')
@section('page-helper', 'Kelola halaman statis yang ditampilkan untuk pengunjung website.')

@section('content')
    <section class="admin-panel activity-list-panel">
        @if ($pages->isEmpty())
            <x-admin.empty-state
                title="Halaman tidak ditemukan"
                message="Belum ada halaman statis pada situs ini."
                icon="fa-file-lines"
            />
        @else
            <div class="admin-table-wrap">
                <table class="admin-table document-table">
                    <thead>
                        <tr>
                            <th scope="col">Halaman</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Status Konten</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pages as $page)
                            <tr>
                                <td>
                                    <div class="document-identity">
                                        <div>
                                            <a class="activity-title-link" href="{{ route('admin.halaman.edit', ['slug' => $page->slug]) }}">
                                                {{ $page->title }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td><code>{{ $page->slug }}</code></td>
                                <td>
                                    <span @class([
                                        'admin-content-badge',
                                        'admin-content-badge-draft' => blank($page->content),
                                        'admin-content-badge-published' => filled($page->content),
                                    ])>
                                        {{ filled($page->content) ? 'Konten tersedia' : 'Belum diisi' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="activity-row-actions">
                                        <a href="{{ route('admin.halaman.edit', ['slug' => $page->slug]) }}" aria-label="Edit {{ $page->title }}">
                                            <i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
@endsection
