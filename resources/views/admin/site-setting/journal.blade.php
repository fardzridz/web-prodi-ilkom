@extends('layouts.admin')

@section('title', 'Tautan E-Jurnal - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Tautan E-Jurnal')
@section('page-helper', 'Atur alamat tujuan menu Jurnal menuju situs e-jurnal resmi universitas.')

@section('content')
    <div class="journal-editor-layout">
        <form class="admin-panel content-editor-card" action="{{ route('admin.jurnal.update') }}" method="post">
            @csrf
            @method('PUT')
            <div class="content-editor-heading"><span aria-hidden="true"><i class="fa-solid fa-book-open-reader"></i></span><div><h2>Alamat E-Jurnal Resmi</h2></div></div>
            <div class="activity-field">
                <label for="journal-url">Alamat tautan e-jurnal <span aria-hidden="true">*</span></label>
                <input id="journal-url" name="journal_url" type="url" maxlength="2048" value="{{ old('journal_url', $siteSetting->journal_url) }}" placeholder="https://ejurnal.example.ac.id" required @error('journal_url') aria-invalid="true" @enderror>
                <small>Hanya URL HTTP/HTTPS yang dapat disimpan.</small>
                @error('journal_url')<small class="activity-field-error">{{ $message }}</small>@enderror
            </div>
            <div class="journal-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><p>Menu Jurnal tidak mengelola artikel di CMS ini. Pengunjung akan diarahkan ke situs e-jurnal resmi.</p></div>
            <button class="admin-button admin-button-primary" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan Tautan</button>
        </form>

        <aside class="admin-panel journal-preview-card">
            <p>Pratinjau Tautan</p>
            <a href="{{ $siteSetting->journal_url ?: '#' }}" target="_blank" rel="noopener noreferrer"><span><strong>Situs E-Jurnal Resmi</strong><small>{{ $siteSetting->journal_url ?: 'Belum diatur' }}</small></span><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
        </aside>
    </div>
@endsection
