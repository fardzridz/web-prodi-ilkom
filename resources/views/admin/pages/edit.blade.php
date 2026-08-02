@extends('layouts.admin')

@section('title', $page->title.' - Pengelola Situs Prodi')
@section('page-section', 'Halaman')
@section('page-heading', $page->title)
@section('page-helper', 'Perbarui konten halaman publik yang ditampilkan untuk pengunjung website.')

@section('content')
<form class="profile-editor-form" action="{{ route('admin.halaman.update', ['slug' => $page->slug]) }}" method="post">
    @csrf
    @method('PUT')

    <section class="admin-panel profile-editor-intro">
        <span aria-hidden="true"><i class="fa-solid fa-file-lines"></i></span>
        <div>
            <h2>Editor Konten Halaman</h2>
            <p>
                Gunakan toolbar untuk memformat konten. Konten ditampilkan di halaman publik {{ $page->title }}.
            </p>
        </div>
        <small>
            @if ($page->updated_at)
                Terakhir diperbarui {{ $page->updated_at->translatedFormat('d F Y, H:i') }} WIB
            @else
                Halaman belum pernah disimpan
            @endif
        </small>
    </section>

    <div class="profile-editor-grid">
        <section class="admin-panel profile-field-card">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-file-pen"></i></span>
                <div>
                    <h2>Konten Halaman</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="page-content">Konten halaman</label>
                <div id="page-content" class="quill-editor"></div>
                <input id="page-content-hidden" type="hidden" name="content" value="{{ old('content', $page->content) }}">
                @error('content')<small id="page-content-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>
        </section>
    </div>

    <div class="profile-editor-actions">
        <a class="admin-button admin-button-secondary" href="{{ route('admin.halaman') }}">Batal</a>
        <button class="admin-button admin-button-primary" type="submit">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
            Simpan Halaman
        </button>
    </div>
</form>
@endsection
