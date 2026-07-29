@extends('layouts.admin')

@section('title', 'Pengaturan Situs - Pengelola Situs Prodi')
@section('page-section', 'Pengaturan')
@section('page-heading', 'Pengaturan Situs')
@section('page-helper', 'Atur identitas situs, logo, favicon, tautan e-jurnal, dan isi footer.')

@section('content')
    @php($footerLinks = old('footer_links', $siteSetting->footer_academic_links ?? []))

    <form class="content-editor-form" action="{{ route('admin.pengaturan.update') }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <section class="admin-panel content-editor-intro">
            <span aria-hidden="true"><i class="fa-solid fa-gears"></i></span>
            <div><h2>Identitas Website</h2><p>Pengaturan ini menjadi sumber tunggal untuk nama situs, identitas institusi, media merek, e-jurnal, dan footer.</p></div>
            <small>{{ $siteSetting->exists && $siteSetting->updated_at ? 'Terakhir diperbarui '.$siteSetting->updated_at->translatedFormat('d F Y, H:i').' WIB' : 'Pengaturan belum pernah disimpan' }}</small>
        </section>

        <div class="settings-editor-layout">
            <div class="settings-editor-main">
                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading"><span aria-hidden="true"><i class="fa-solid fa-building-columns"></i></span><div><p>Informasi Resmi</p><h2>Nama dan Institusi</h2></div></div>
                    <div class="content-form-grid">
                        <div class="activity-field content-field-full">
                            <label for="setting-site-name">Nama situs <span aria-hidden="true">*</span></label>
                            <input id="setting-site-name" name="site_name" type="text" maxlength="255" value="{{ old('site_name', $siteSetting->site_name) }}" required @error('site_name') aria-invalid="true" @enderror>
                            @error('site_name')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field">
                            <label for="setting-university-name">Nama universitas <span aria-hidden="true">*</span></label>
                            <input id="setting-university-name" name="university_name" type="text" maxlength="255" value="{{ old('university_name', $siteSetting->university_name) }}" required @error('university_name') aria-invalid="true" @enderror>
                            @error('university_name')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field">
                            <label for="setting-faculty-name">Nama fakultas <span aria-hidden="true">*</span></label>
                            <input id="setting-faculty-name" name="faculty_name" type="text" maxlength="255" value="{{ old('faculty_name', $siteSetting->faculty_name) }}" required @error('faculty_name') aria-invalid="true" @enderror>
                            @error('faculty_name')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                        <div class="activity-field content-field-full">
                            <label for="setting-journal-url">Alamat tautan e-jurnal <span aria-hidden="true">*</span></label>
                            <input id="setting-journal-url" name="journal_url" type="url" maxlength="2048" value="{{ old('journal_url', $siteSetting->journal_url) }}" placeholder="https://ejurnal.example.ac.id" required @error('journal_url') aria-invalid="true" @enderror>
                            @error('journal_url')<small class="activity-field-error">{{ $message }}</small>@enderror
                        </div>
                    </div>
                </section>

                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading"><span aria-hidden="true"><i class="fa-solid fa-palette"></i></span><div><p>Media Merek</p><h2>Logo dan Favicon</h2></div></div>
                    <div class="brand-upload-grid">
                        <div class="brand-upload-card" data-file-field>
                            <div class="brand-current-preview">
                                @if ($siteSetting->logo)
                                    <img src="{{ asset('storage/'.$siteSetting->logo) }}" alt="Logo situs saat ini">
                                @else
                                    <i class="fa-regular fa-image" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="activity-field">
                                <label for="setting-logo">Logo situs</label>
                                <input id="setting-logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp" data-file-input @error('logo') aria-invalid="true" @enderror>
                                <small data-file-name-output>JPG, PNG, atau WebP · maksimal 2 MB</small>
                                @error('logo')<small class="activity-field-error">{{ $message }}</small>@enderror
                            </div>
                            @if ($siteSetting->logo)<label class="content-remove-check"><input name="remove_logo" type="checkbox" value="1" @checked(old('remove_logo'))> Hapus logo saat disimpan</label>@endif
                        </div>
                        <div class="brand-upload-card" data-file-field>
                            <div class="brand-current-preview is-favicon">
                                @if ($siteSetting->favicon)
                                    <img src="{{ asset('storage/'.$siteSetting->favicon) }}" alt="Favicon situs saat ini">
                                @else
                                    <i class="fa-solid fa-window-maximize" aria-hidden="true"></i>
                                @endif
                            </div>
                            <div class="activity-field">
                                <label for="setting-favicon">Ikon situs</label>
                                <input id="setting-favicon" name="favicon" type="file" accept=".ico,.png" data-file-input @error('favicon') aria-invalid="true" @enderror>
                                <small data-file-name-output>ICO atau PNG · maksimal 512 KB</small>
                                @error('favicon')<small class="activity-field-error">{{ $message }}</small>@enderror
                            </div>
                            @if ($siteSetting->favicon)<label class="content-remove-check"><input name="remove_favicon" type="checkbox" value="1" @checked(old('remove_favicon'))> Hapus favicon saat disimpan</label>@endif
                        </div>
                    </div>
                </section>

                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading content-editor-heading-action">
                        <span aria-hidden="true"><i class="fa-solid fa-link"></i></span><div><p>Navigasi Bawah</p><h2>Tautan Akademik Footer</h2></div>
                        <button class="admin-button admin-button-secondary" type="button" data-footer-link-add><i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah Tautan</button>
                    </div>
                    <div class="footer-link-list" data-footer-link-list data-next-index="{{ count($footerLinks) }}">
                        @forelse ($footerLinks as $index => $link)
                            <div class="footer-link-item" data-footer-link-item>
                                <div class="activity-field"><label for="footer-link-label-{{ $index }}">Label tautan</label><input id="footer-link-label-{{ $index }}" name="footer_links[{{ $index }}][label]" type="text" maxlength="255" value="{{ $link['label'] ?? '' }}" required></div>
                                <div class="activity-field"><label for="footer-link-url-{{ $index }}">Alamat tautan</label><input id="footer-link-url-{{ $index }}" name="footer_links[{{ $index }}][url]" type="url" maxlength="2048" value="{{ $link['url'] ?? '' }}" required></div>
                                <button class="slide-editor-remove" type="button" data-footer-link-remove><i class="fa-solid fa-trash" aria-hidden="true"></i><span class="sr-only">Hapus tautan</span></button>
                                @error("footer_links.$index.label")<small class="activity-field-error footer-link-error">{{ $message }}</small>@enderror
                                @error("footer_links.$index.url")<small class="activity-field-error footer-link-error">{{ $message }}</small>@enderror
                            </div>
                        @empty
                            <div class="content-empty-state" data-footer-link-empty><i class="fa-solid fa-link-slash" aria-hidden="true"></i><p>Belum ada tautan akademik pada footer.</p></div>
                        @endforelse
                    </div>
                </section>

                <section class="admin-panel content-editor-card">
                    <div class="content-editor-heading"><span aria-hidden="true"><i class="fa-solid fa-align-left"></i></span><div><p>Hak Cipta</p><h2>Teks Footer</h2></div></div>
                    <div class="activity-field">
                        <label for="setting-footer-text">Teks bawah halaman <span aria-hidden="true">*</span></label>
                        <textarea id="setting-footer-text" name="footer_text" rows="4" maxlength="5000" required data-character-count @error('footer_text') aria-invalid="true" @enderror>{{ old('footer_text', $siteSetting->footer_text) }}</textarea>
                        <small><span data-character-count-output>0</span> / 5.000 karakter</small>
                        @error('footer_text')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                </section>
            </div>

            <aside class="home-editor-aside">
                <section class="admin-panel settings-summary-card"><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><h2>Upload Aman</h2><p>File baru divalidasi sebelum menggantikan file lama. Logo dan favicon lama baru dihapus setelah perubahan berhasil disimpan.</p></section>
                <button class="admin-button admin-button-primary content-save-button" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan Pengaturan</button>
            </aside>
        </div>
    </form>

    <template data-footer-link-template>
        <div class="footer-link-item" data-footer-link-item>
            <div class="activity-field"><label for="footer-link-label-__INDEX__">Label tautan</label><input id="footer-link-label-__INDEX__" name="footer_links[__INDEX__][label]" type="text" maxlength="255" required></div>
            <div class="activity-field"><label for="footer-link-url-__INDEX__">Alamat tautan</label><input id="footer-link-url-__INDEX__" name="footer_links[__INDEX__][url]" type="url" maxlength="2048" required></div>
            <button class="slide-editor-remove" type="button" data-footer-link-remove><i class="fa-solid fa-trash" aria-hidden="true"></i><span class="sr-only">Hapus tautan</span></button>
        </div>
    </template>
@endsection
