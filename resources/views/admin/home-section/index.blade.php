@extends('layouts.admin')

@section('title', 'Beranda - Pengelola Situs Prodi')
@section('page-section', 'Konten Situs')
@section('page-heading', 'Beranda')
@section('page-helper', 'Atur tampilan utama, tombol ajakan, gambar hero, dan sambutan pada halaman beranda.')

@section('content')
@php
    $defaultSlides = collect($homeSection->hero_slides ?? [])->map(fn ($slide) => [
        'existing_path' => $slide['path'] ?? null,
        'alt' => $slide['alt'] ?? '',
        'remove' => false,
    ])->values()->all();
    $slideRows = old('slides', $defaultSlides);
    $previewSlide = collect($slideRows)->first(
        fn (array $slide): bool => filled($slide['existing_path'] ?? null) && empty($slide['remove'])
    );
    $previewTitle = old('hero_title', $homeSection->hero_title ?: 'Judul beranda');
    $previewSubtitle = old('hero_subtitle', $homeSection->hero_subtitle ?: 'Kalimat pembuka beranda');
    $previewCta = old('cta_text', $homeSection->cta_text ?: 'Teks tombol');
    $previewWelcomeTitle = old('welcome_title', $homeSection->welcome_title ?: 'Judul sambutan');
    $previewWelcomeDescription = old('welcome_description', $homeSection->welcome_description ?: 'Isi sambutan beranda');
@endphp

<form class="content-editor-form" action="{{ route('admin.beranda.update') }}" method="post" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <section class="admin-panel content-editor-intro">
        <span aria-hidden="true"><i class="fa-solid fa-house-laptop"></i></span>
        <div>
            <h2>Editor Beranda</h2>
            <p>Konten disimpan sebagai satu pengaturan beranda. Gambar hero maksimal lima slide dan setiap gambar wajib memiliki teks alternatif.</p>
        </div>
        <small>
            {{ $homeSection->exists && $homeSection->updated_at ? 'Terakhir diperbarui '.$homeSection->updated_at->translatedFormat('d F Y, H:i').' WIB' : 'Beranda belum pernah disimpan' }}
        </small>
    </section>

    <div class="home-editor-layout">
        <div class="home-editor-main">
            <section class="admin-panel content-editor-card">
                <div class="content-editor-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                    <div>
                        <p>Tampilan Utama</p>
                        <h2>Hero Beranda</h2>
                    </div>
                </div>

                <div class="content-form-grid">
                    <div class="activity-field content-field-full">
                        <label for="home-hero-title">Judul utama <span aria-hidden="true">*</span></label>
                        <textarea id="home-hero-title" name="hero_title" rows="3" maxlength="500" required data-character-count data-home-preview-field="title" @error('hero_title') aria-invalid="true" @enderror>{{ old('hero_title', $homeSection->hero_title) }}</textarea>
                        <small>Tekan Enter untuk baris baru. <span data-character-count-output>0</span> / 500 karakter</small>
                        @error('hero_title')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="activity-field content-field-full">
                        <label for="home-hero-subtitle">Kalimat pembuka <span aria-hidden="true">*</span></label>
                        <textarea id="home-hero-subtitle" name="hero_subtitle" rows="5" maxlength="5000" required data-character-count data-home-preview-field="subtitle" @error('hero_subtitle') aria-invalid="true" @enderror>{{ old('hero_subtitle', $homeSection->hero_subtitle) }}</textarea>
                        <small><span data-character-count-output>0</span> / 5.000 karakter</small>
                        @error('hero_subtitle')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="activity-field">
                        <label for="home-cta-text">Teks tombol <span aria-hidden="true">*</span></label>
                        <input id="home-cta-text" name="cta_text" type="text" maxlength="100" value="{{ old('cta_text', $homeSection->cta_text) }}" required data-home-preview-field="cta" @error('cta_text') aria-invalid="true" @enderror>
                        @error('cta_text')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="activity-field">
                        <label for="home-cta-link">Tujuan tombol <span aria-hidden="true">*</span></label>
                        <input id="home-cta-link" name="cta_link" type="text" maxlength="2048" value="{{ old('cta_link', $homeSection->cta_link) }}" placeholder="/profil" required @error('cta_link') aria-invalid="true" @enderror>
                        <small>Gunakan path internal seperti /profil atau URL HTTP/HTTPS.</small>
                        @error('cta_link')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                </div>
            </section>

            <section class="admin-panel content-editor-card">
                <div class="content-editor-heading content-editor-heading-action">
                    <span aria-hidden="true"><i class="fa-solid fa-images"></i></span>
                    <div>
                        <p>Media Utama</p>
                        <h2>Gambar Hero</h2>
                    </div>
                    <button class="admin-button admin-button-secondary" type="button" data-home-slide-add>
                        <i class="fa-solid fa-plus" aria-hidden="true"></i> Tambah Slide
                    </button>
                </div>

                <div class="slide-editor-list" data-home-slide-list data-next-index="{{ count($slideRows) }}">
                    @forelse ($slideRows as $index => $slide)
                    <article class="slide-editor-item" data-slide-item @if (! empty($slide['existing_path'])) data-existing-path="{{ $slide['existing_path'] }}" @endif>
                        <input type="hidden" name="slides[{{ $index }}][existing_path]" value="{{ $slide['existing_path'] ?? '' }}">
                        @if (! empty($slide['existing_path']))
                        <div class="slide-editor-preview">
                            <img src="{{ asset('storage/'.$slide['existing_path']) }}" alt="{{ $slide['alt'] ?? 'Pratinjau slide hero' }}">
                            <span>Slide tersimpan</span>
                        </div>
                        @else
                        <div class="slide-editor-preview is-empty"><i class="fa-regular fa-image" aria-hidden="true"></i><span>Slide baru</span></div>
                        @endif
                        <div class="slide-editor-fields">
                            <div class="activity-field" data-file-field>
                                <span class="activity-field-label">{{ empty($slide['existing_path']) ? 'Gambar slide' : 'Ganti gambar' }}</span>
                                <label class="slide-file-button" for="home-slide-file-{{ $index }}">
                                    <i class="fa-solid fa-image" aria-hidden="true"></i>
                                    <strong>{{ empty($slide['existing_path']) ? 'Pilih gambar' : 'Ganti gambar' }}</strong>
                                </label>
                                <input id="home-slide-file-{{ $index }}" class="sr-only" name="slides[{{ $index }}][file]" type="file" accept=".jpg,.jpeg,.png,.webp" data-file-input @error("slides.$index.file") aria-invalid="true" @enderror>
                                <small data-file-name-output>JPG, PNG, atau WebP · maksimal 4 MB</small>
                                @error("slides.$index.file")<small class="activity-field-error">{{ $message }}</small>@enderror
                            </div>
                            <div class="activity-field">
                                <label for="home-slide-alt-{{ $index }}">Teks pengganti gambar <span aria-hidden="true">*</span></label>
                                <input id="home-slide-alt-{{ $index }}" name="slides[{{ $index }}][alt]" type="text" maxlength="255" value="{{ $slide['alt'] ?? '' }}" @error("slides.$index.alt") aria-invalid="true" @enderror>
                                @error("slides.$index.alt")<small class="activity-field-error">{{ $message }}</small>@enderror
                            </div>
                        </div>
                        <div class="slide-editor-actions">
                            <span class="activity-field-label slide-editor-actions-spacer" aria-hidden="true">&nbsp;</span>
                            <button class="slide-editor-remove" type="button" data-slide-remove aria-label="Hapus slide">
                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                <span class="sr-only">Hapus slide</span>
                            </button>
                        </div>
                    </article>
                    @empty
                    <div class="content-empty-state" data-slide-empty><i class="fa-regular fa-images" aria-hidden="true"></i>
                        <p>Belum ada gambar hero. Tambahkan jika beranda membutuhkan slider.</p>
                    </div>
                    @endforelse
                </div>
            </section>

            <section class="admin-panel content-editor-card">
                <div class="content-editor-heading">
                    <span aria-hidden="true"><i class="fa-solid fa-handshake-angle"></i></span>
                    <div>
                        <p>Profil Singkat</p>
                        <h2>Sambutan Beranda</h2>
                    </div>
                </div>
                <div class="content-form-grid">
                    <div class="activity-field content-field-full">
                        <label for="home-welcome-title">Judul sambutan <span aria-hidden="true">*</span></label>
                        <input id="home-welcome-title" name="welcome_title" type="text" maxlength="255" value="{{ old('welcome_title', $homeSection->welcome_title) }}" required data-home-preview-field="welcome-title" @error('welcome_title') aria-invalid="true" @enderror>
                        @error('welcome_title')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                    <div class="activity-field content-field-full">
                        <label for="home-welcome-description">Isi sambutan <span aria-hidden="true">*</span></label>
                        <textarea id="home-welcome-description" name="welcome_description" rows="7" maxlength="10000" required data-character-count data-home-preview-field="welcome-description" @error('welcome_description') aria-invalid="true" @enderror>{{ old('welcome_description', $homeSection->welcome_description) }}</textarea>
                        <small><span data-character-count-output>0</span> / 10.000 karakter</small>
                        @error('welcome_description')<small class="activity-field-error">{{ $message }}</small>@enderror
                    </div>
                </div>
            </section>
        </div>

        <aside class="home-editor-aside">
            <section class="admin-panel home-preview-card" data-home-preview>
                <p>Pratinjau Konten</p>
                <div class="home-preview-visual" data-home-preview-visual>
                    @if ($previewSlide)
                        <img src="{{ asset('storage/'.$previewSlide['existing_path']) }}" alt="{{ $previewSlide['alt'] ?: 'Pratinjau slide hero' }}">
                    @else
                        <i class="fa-solid fa-panorama" aria-hidden="true"></i>
                    @endif
                </div>
                <h2 data-home-preview-title>{!! nl2br(e($previewTitle)) !!}</h2>
                <p class="home-preview-subtitle" data-home-preview-subtitle>{{ $previewSubtitle }}</p>
                <span class="home-preview-cta" data-home-preview-cta>{{ $previewCta }}</span>
                <div class="home-preview-welcome">
                    <strong data-home-preview-welcome-title>{{ $previewWelcomeTitle }}</strong>
                    <p data-home-preview-welcome-description>{{ $previewWelcomeDescription }}</p>
                </div>
                <small>Hero → CTA → sambutan</small>
                <a class="home-preview-public-link" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer">
                    <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
                    Buka beranda
                </a>
            </section>
            <button class="admin-button admin-button-primary content-save-button" type="submit"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> Simpan Beranda</button>
        </aside>
    </div>
</form>

<template data-home-slide-template>
    <article class="slide-editor-item" data-slide-item>
        <input type="hidden" name="slides[__INDEX__][existing_path]" value="">
        <div class="slide-editor-preview is-empty"><i class="fa-regular fa-image" aria-hidden="true"></i><span>Slide baru</span></div>
        <div class="slide-editor-fields">
            <div class="activity-field" data-file-field>
                <span class="activity-field-label">Gambar slide</span>
                <label class="slide-file-button" for="home-slide-file-__INDEX__">
                    <i class="fa-solid fa-image" aria-hidden="true"></i>
                    <strong>Pilih gambar</strong>
                </label>
                <input id="home-slide-file-__INDEX__" class="sr-only" name="slides[__INDEX__][file]" type="file" accept=".jpg,.jpeg,.png,.webp" data-file-input>
                <small data-file-name-output>JPG, PNG, atau WebP · maksimal 4 MB</small>
            </div>
            <div class="activity-field">
                <label for="home-slide-alt-__INDEX__">Teks pengganti gambar <span aria-hidden="true">*</span></label>
                <input id="home-slide-alt-__INDEX__" name="slides[__INDEX__][alt]" type="text" maxlength="255">
            </div>
        </div>
        <div class="slide-editor-actions">
            <span class="activity-field-label slide-editor-actions-spacer" aria-hidden="true">&nbsp;</span>
            <button class="slide-editor-remove" type="button" data-slide-remove aria-label="Hapus slide">
                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                <span class="sr-only">Hapus slide</span>
            </button>
        </div>
    </article>
</template>
@endsection