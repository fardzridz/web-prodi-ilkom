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
                    Isi daftar seperti misi dan tujuan satu poin per baris agar tetap mudah dibaca ketika dihubungkan ke halaman publik.
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
                        <p>Tentang Prodi</p>
                        <h2>Profil Singkat</h2>
                    </div>
                </div>
                <div class="activity-field">
                    <label class="sr-only" for="profile-description">Deskripsi program studi</label>
                    <textarea
                        id="profile-description"
                        name="description"
                        rows="7"
                        maxlength="20000"
                        required
                        data-character-count
                        @error('description') aria-invalid="true" aria-describedby="profile-description-error" @enderror
                    >{{ old('description', $programProfile->description) }}</textarea>
                    <small><span data-character-count-output>0</span> / 20.000 karakter</small>
                    @error('description')<small id="profile-description-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="admin-panel profile-field-card">
                <div class="profile-field-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-landmark"></i></span>
                    <div>
                        <p>Perjalanan Akademik</p>
                        <h2>Sejarah Prodi</h2>
                    </div>
                </div>
                <div class="activity-field">
                    <label class="sr-only" for="profile-history">Sejarah program studi</label>
                    <textarea
                        id="profile-history"
                        name="history"
                        rows="7"
                        maxlength="20000"
                        required
                        data-character-count
                        @error('history') aria-invalid="true" aria-describedby="profile-history-error" @enderror
                    >{{ old('history', $programProfile->history) }}</textarea>
                    <small><span data-character-count-output>0</span> / 20.000 karakter</small>
                    @error('history')<small id="profile-history-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="admin-panel profile-field-card profile-field-vision">
                <div class="profile-field-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-eye"></i></span>
                    <div>
                        <p>Arah Pengembangan</p>
                        <h2>Visi Prodi</h2>
                    </div>
                </div>
                <div class="activity-field">
                    <label class="sr-only" for="profile-vision">Visi program studi</label>
                    <textarea
                        id="profile-vision"
                        name="vision"
                        rows="6"
                        maxlength="5000"
                        required
                        data-character-count
                        @error('vision') aria-invalid="true" aria-describedby="profile-vision-error" @enderror
                    >{{ old('vision', $programProfile->vision) }}</textarea>
                    <small><span data-character-count-output>0</span> / 5.000 karakter</small>
                    @error('vision')<small id="profile-vision-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="admin-panel profile-field-card">
                <div class="profile-field-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-list-ol"></i></span>
                    <div>
                        <p>Langkah Utama</p>
                        <h2>Misi Prodi</h2>
                    </div>
                </div>
                <div class="activity-field">
                    <label class="sr-only" for="profile-mission">Misi program studi</label>
                    <textarea
                        id="profile-mission"
                        name="mission"
                        rows="9"
                        maxlength="20000"
                        required
                        data-character-count
                        @error('mission') aria-invalid="true" aria-describedby="profile-mission-error" @enderror
                    >{{ old('mission', $programProfile->mission) }}</textarea>
                    <small>Satu poin per baris · <span data-character-count-output>0</span> / 20.000 karakter</small>
                    @error('mission')<small id="profile-mission-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="admin-panel profile-field-card">
                <div class="profile-field-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-bullseye"></i></span>
                    <div>
                        <p>Arah Lulusan</p>
                        <h2>Tujuan Program Studi</h2>
                    </div>
                </div>
                <div class="activity-field">
                    <label class="sr-only" for="profile-goals">Tujuan program studi</label>
                    <textarea
                        id="profile-goals"
                        name="goals"
                        rows="9"
                        maxlength="20000"
                        required
                        data-character-count
                        @error('goals') aria-invalid="true" aria-describedby="profile-goals-error" @enderror
                    >{{ old('goals', $programProfile->goals) }}</textarea>
                    <small>Satu poin per baris · <span data-character-count-output>0</span> / 20.000 karakter</small>
                    @error('goals')<small id="profile-goals-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="admin-panel profile-field-card profile-field-accreditation">
                <div class="profile-field-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-award"></i></span>
                    <div>
                        <p>Akreditasi</p>
                        <h2>Badge Akreditasi</h2>
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
                        @error('accreditation') aria-invalid="true" aria-describedby="profile-accreditation-error" @enderror
                    >
                    @error('accreditation')<small id="profile-accreditation-error" class="activity-field-error">{{ $message }}</small>@enderror
                </div>
            </section>

            <section class="admin-panel profile-field-card profile-field-advantages">
                <div class="profile-field-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-star"></i></span>
                    <div>
                        <p>Keunggulan dan Peminatan</p>
                        <h2>Keunggulan Prodi</h2>
                    </div>
                </div>
                <div class="activity-field">
                    <label class="sr-only" for="profile-advantages">Keunggulan program studi</label>
                    <textarea
                        id="profile-advantages"
                        name="advantages"
                        rows="9"
                        maxlength="20000"
                        required
                        data-character-count
                        @error('advantages') aria-invalid="true" aria-describedby="profile-advantages-error" @enderror
                    >{{ old('advantages', $programProfile->advantages) }}</textarea>
                    <small><span data-character-count-output>0</span> / 20.000 karakter</small>
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
