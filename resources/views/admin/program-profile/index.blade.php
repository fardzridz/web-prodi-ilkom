@extends('layouts.admin')

@section('title', 'Profil Prodi - Pengelola Situs Prodi')
@section('page-section', 'Konten Situs')
@section('page-heading', 'Profil Prodi')
@section('page-helper', 'Atur seluruh isi halaman profil publik tanpa mengubah susunan tampilannya.')

@section('content')
<form class="profile-editor-form" action="{{ route('admin.profil.update') }}" method="post">
    @csrf
    @method('PUT')

    <section class="admin-panel profile-editor-intro">
        <span aria-hidden="true"><i class="fa-solid fa-pen-ruler"></i></span>
        <div>
            <h2>Editor Konten Profil</h2>
            <p>
                Gunakan toolbar di bawah setiap editor untuk mengatur format teks, daftar, heading, dan tautan. Konten akan ditampilkan langsung di halaman publik.
            </p>
        </div>
        <small>
            @if ($programProfile->exists && $programProfile->updated_at)
            Terakhir diperbarui {{ $programProfile->updated_at->translatedFormat('d F Y, H:i') }} WIB
            @else
            Profil belum pernah disimpan
            @endif
        </small>
    </section>

    <div class="profile-editor-grid">
        <section class="admin-panel profile-field-card">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-address-card"></i></span>
                <div>
                    <h2>Section Profil Prodi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-description">Deskripsi program studi</label>
                <div id="profile-description" class="quill-editor"></div>
                <input id="profile-description-hidden" type="hidden" name="description" value="{{ old('description', $programProfile->description) }}">
                @error('description')<small id="profile-description-error" class="activity-field-error">{{ $message }}</small>@enderror

            </div>
        </section>

        <section class="admin-panel profile-field-card">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-landmark"></i></span>
                <div>
                    <h2>Section Sejarah Prodi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-history">Sejarah program studi</label>
                <div id="profile-history" class="quill-editor"></div>
                <input id="profile-history-hidden" type="hidden" name="history" value="{{ old('history', $programProfile->history) }}">
                @error('history')<small id="profile-history-error" class="activity-field-error">{{ $message }}</small>@enderror

            </div>
        </section>

        <section class="admin-panel profile-field-card profile-field-vision">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-eye"></i></span>
                <div>
                    <h2>Section Visi Prodi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-vision">Visi program studi</label>
                <div id="profile-vision" class="quill-editor"></div>
                <input id="profile-vision-hidden" type="hidden" name="vision" value="{{ old('vision', $programProfile->vision) }}">
                @error('vision')<small id="profile-vision-error" class="activity-field-error">{{ $message }}</small>@enderror

            </div>
        </section>

        <section class="admin-panel profile-field-card">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-list-ol"></i></span>
                <div>
                    <h2>Section Misi Prodi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-mission">Misi program studi</label>
                <div id="profile-mission" class="quill-editor"></div>
                <input id="profile-mission-hidden" type="hidden" name="mission" value="{{ old('mission', $programProfile->mission) }}">
                @error('mission')<small id="profile-mission-error" class="activity-field-error">{{ $message }}</small>@enderror

            </div>
        </section>

        <section class="admin-panel profile-field-card">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-bullseye"></i></span>
                <div>
                    <h2>Section Tujuan Program Studi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-goals">Tujuan program studi</label>
                <div id="profile-goals" class="quill-editor"></div>
                <input id="profile-goals-hidden" type="hidden" name="goals" value="{{ old('goals', $programProfile->goals) }}">
                @error('goals')<small id="profile-goals-error" class="activity-field-error">{{ $message }}</small>@enderror

            </div>
        </section>

        <section class="admin-panel profile-field-card profile-field-accreditation">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-award"></i></span>
                <div>
                    <h2>Section Badge Akreditasi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-accreditation">Status akreditasi <span aria-hidden="true">*</span></label>
                <input
                    id="profile-accreditation"
                    name="accreditation"
                    type="text"
                    value="{{ old('accreditation', $programProfile->accreditation) }}"
                    maxlength="255"
                    placeholder="Contoh: Baik Sekali"
                    required
                    @error('accreditation') aria-invalid="true" aria-describedby="profile-accreditation-error" @enderror>
                @error('accreditation')<small id="profile-accreditation-error" class="activity-field-error">{{ $message }}</small>@enderror
            </div>
        </section>

        <section class="admin-panel profile-field-card profile-field-advantages">
            <div class="profile-field-heading">
                <span aria-hidden="true"><i class="fa-solid fa-star"></i></span>
                <div>
                    <h2>Section Keunggulan Prodi</h2>
                </div>
            </div>
            <div class="activity-field">
                <label for="profile-advantages">Keunggulan program studi</label>
                <div id="profile-advantages" class="quill-editor"></div>
                <input id="profile-advantages-hidden" type="hidden" name="advantages" value="{{ old('advantages', $programProfile->advantages) }}">
                @error('advantages')<small id="profile-advantages-error" class="activity-field-error">{{ $message }}</small>@enderror

            </div>
        </section>
    </div>

    <div class="profile-editor-actions">
        <a class="admin-button admin-button-secondary" href="{{ route('admin.profil') }}">Batal</a>
        <button class="admin-button admin-button-primary" type="submit">
            <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>
            Simpan Profil
        </button>
    </div>
</form>
@endsection