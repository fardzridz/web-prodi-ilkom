@extends('layouts.admin')

@section('title', 'Beranda | Pengelola Situs Prodi')
@section('page-heading', 'Beranda')

@section('content')
@php
    $defaultSlides = collect($homeSection->hero_slides ?? [])->map(fn ($slide) => [
        'existing_path' => $slide['path'] ?? null, 'alt' => $slide['alt'] ?? '', 'remove' => false,
    ])->values()->all();
    $slideRows = old('slides', $defaultSlides);
    $defaultAdvantages = collect(\App\Models\HomeSection::advantageItems($homeSection->advantages))
        ->map(fn (array $advantage, int $index): array => [
            'existing_path' => $advantage['image'] ?? null, 'title' => $advantage['title'] ?? '',
            'description' => $advantage['description'] ?? '', 'order' => $advantage['order'] ?? $index + 1, 'remove' => false,
        ])->values()->all();
    $advantageRows = old('advantages', $defaultAdvantages);
    $advantageHeading = old('advantages_heading', \App\Models\HomeSection::advantageHeading($homeSection->advantages));
    $previewSlide = collect($slideRows)->first(fn (array $slide): bool => filled($slide['existing_path'] ?? null) && empty($slide['remove']));
    $previewTitle = old('hero_title', $homeSection->hero_title ?: 'Judul beranda');
    $previewSubtitle = old('hero_subtitle', $homeSection->hero_subtitle ?: 'Kalimat pembuka beranda');
    $previewCta = old('cta_text', $homeSection->cta_text ?: 'Teks tombol');
    $previewWelcomeTitle = old('welcome_title', $homeSection->welcome_title ?: 'Judul sambutan');
    $previewWelcomeDescription = old('welcome_description', $homeSection->welcome_description ?: 'Isi sambutan beranda');
    $welcomeImagePath = $homeSection->welcome_image;
@endphp

    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Editor Beranda</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gambar hero maksimal lima slide dan setiap gambar wajib memiliki teks alternatif.</p>
        </div>
        @if ($homeSection->exists && $homeSection->updated_at)
            <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">Terakhir diperbarui {{ $homeSection->updated_at->translatedFormat('d F Y, H:i') }} WIB</span>
        @endif
    </div>

    <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('admin.beranda.update') }}" method="post" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Hero Beranda</h4></div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="home-hero-title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul utama<span class="text-error-500">*</span></label>
                            <textarea id="home-hero-title" name="hero_title" rows="3" maxlength="500" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ old('hero_title', $homeSection->hero_title) }}</textarea>
                            @error('hero_title')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="home-hero-subtitle" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kalimat pembuka<span class="text-error-500">*</span></label>
                            <textarea id="home-hero-subtitle" name="hero_subtitle" rows="5" maxlength="5000" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ old('hero_subtitle', $homeSection->hero_subtitle) }}</textarea>
                            @error('hero_subtitle')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                            <div>
                                <label for="home-cta-text" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Teks tombol<span class="text-error-500">*</span></label>
                                <input id="home-cta-text" name="cta_text" type="text" maxlength="100" value="{{ old('cta_text', $homeSection->cta_text) }}" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('cta_text')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="home-cta-link" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tujuan tombol<span class="text-error-500">*</span></label>
                                <input id="home-cta-link" name="cta_link" type="text" maxlength="2048" value="{{ old('cta_link', $homeSection->cta_link) }}" placeholder="/profil" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                @error('cta_link')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3">
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Gambar Hero</h4>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300" onclick="addSlide()">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill=""/></svg> Tambah Slide
                        </button>
                    </div>
                    <div class="p-6" id="slide-list">
                        <div class="space-y-4" id="slide-container">
                            @foreach ($slideRows as $index => $slide)
                                <div class="slide-item rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                    <input type="hidden" name="slides[{{ $index }}][existing_path]" value="{{ $slide['existing_path'] ?? '' }}">
                                    @if (!empty($slide['existing_path']))
                                        <img src="{{ asset('storage/'.$slide['existing_path']) }}" alt="" class="mb-3 aspect-video w-full max-w-sm rounded-lg object-cover" />
                                    @endif
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label for="home-slide-file-{{ $index }}" class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">{{ empty($slide['existing_path']) ? 'Gambar' : 'Ganti gambar' }}</label>
                                            <input id="home-slide-file-{{ $index }}" name="slides[{{ $index }}][file]" type="file" accept=".jpg,.jpeg,.png,.webp"
                                                class="dark:bg-dark-900 text-sm w-full text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100 dark:file:bg-brand-500/15 dark:file:text-brand-400" />
                                        </div>
                                        <div>
                                            <label for="home-slide-alt-{{ $index }}" class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Alt text<span class="text-error-500">*</span></label>
                                            <input id="home-slide-alt-{{ $index }}" name="slides[{{ $index }}][alt]" type="text" maxlength="255" value="{{ $slide['alt'] ?? '' }}"
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                    </div>
                                    <button type="button" onclick="this.closest('.slide-item').remove();updateSlideCount()"
                                        class="mt-3 inline-flex items-center gap-1 text-sm text-gray-400 hover:text-error-500 transition">
                                        <svg class="fill-current" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/></svg> Hapus slide
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Sambutan Beranda</h4></div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="home-welcome-title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul sambutan<span class="text-error-500">*</span></label>
                            <input id="home-welcome-title" name="welcome_title" type="text" maxlength="255" value="{{ old('welcome_title', $homeSection->welcome_title) }}" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('welcome_title')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="home-welcome-description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Isi sambutan<span class="text-error-500">*</span></label>
                            <textarea id="home-welcome-description" name="welcome_description" rows="7" maxlength="10000" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ old('welcome_description', $homeSection->welcome_description) }}</textarea>
                            @error('welcome_description')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Gambar sambutan</label>
                            <x-admin.image-upload id="home-welcome-image" name="welcome_image"
                                :existing-src="$welcomeImagePath ? asset('storage/'.$welcomeImagePath) : null"
                                label="Gambar sambutan"
                                help-text="Gunakan PNG/WebP dengan latar transparan agar potongan orang tampil rapi. JPG, PNG, WebP — maks 4 MB."
                                accept="image/jpeg,image/png,image/webp"
                                preview-class="max-h-40 w-full rounded-lg object-contain" />
                            @error('welcome_image')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            @if (!empty($welcomeImagePath))
                                <label class="mt-2 inline-flex cursor-pointer items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                    <input type="checkbox" name="welcome_remove" value="1" {{ old('welcome_remove') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                                    Hapus gambar sambutan
                                </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3">
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Keunggulan Prodi</h4>
                        <button type="button" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300" onclick="addAdvantage()">
                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill=""/></svg> Tambah Keunggulan
                        </button>
                    </div>
                    <div class="p-6 space-y-5">
                        <div>
                            <label for="home-advantages-heading" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Judul bagian keunggulan<span class="text-error-500">*</span></label>
                            <input id="home-advantages-heading" name="advantages_heading" type="text" maxlength="255" value="{{ $advantageHeading }}" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                            @error('advantages_heading')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                        <div class="space-y-4" id="advantage-container">
                            @foreach ($advantageRows as $index => $advantage)
                                <div class="advantage-item rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                                    <input type="hidden" name="advantages[{{ $index }}][existing_path]" value="{{ $advantage['existing_path'] ?? '' }}">
                                    @if (!empty($advantage['existing_path']))
                                        <img src="{{ asset('storage/'.$advantage['existing_path']) }}" alt="" class="mb-3 aspect-square w-14 rounded-lg object-cover" />
                                    @endif
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Urutan<span class="text-error-500">*</span></label>
                                            <input name="advantages[{{ $index }}][order]" type="number" min="1" max="20" value="{{ $advantage['order'] ?? $index + 1 }}" required
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <div>
                                            <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Judul<span class="text-error-500">*</span></label>
                                            <input name="advantages[{{ $index }}][title]" type="text" maxlength="255" value="{{ $advantage['title'] ?? '' }}" required
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Deskripsi<span class="text-error-500">*</span></label>
                                        <textarea name="advantages[{{ $index }}][description]" rows="2" maxlength="1000" required
                                            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y">{{ $advantage['description'] ?? '' }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">{{ empty($advantage['existing_path']) ? 'Gambar' : 'Ganti gambar' }}</label>
                                        <input name="advantages[{{ $index }}][image]" type="file" accept=".jpg,.jpeg,.png,.webp"
                                            class="dark:bg-dark-900 text-sm w-full text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100 dark:file:bg-brand-500/15 dark:file:text-brand-400" />
                                    </div>
                                    <button type="button" onclick="this.closest('.advantage-item').remove();updateAdvantageCount()"
                                        class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-error-500 transition">
                                        <svg class="fill-current" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/></svg> Hapus keunggulan
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Pratinjau</h4></div>
                    <div class="p-6 space-y-4">
                        @if ($previewSlide)
                            <img src="{{ asset('storage/'.$previewSlide['existing_path']) }}" alt="" class="w-full h-32 rounded object-cover" />
                        @else
                            <div class="flex items-center justify-center h-32 rounded bg-gray-100 dark:bg-white/5 text-gray-400">
                                <svg class="fill-current" width="32" height="32" viewBox="0 0 24 24" fill="none"><path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z" fill=""/></svg>
                            </div>
                        @endif
                        <h3 class="font-semibold text-gray-800 dark:text-white/90">{!! nl2br(e($previewTitle)) !!}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $previewSubtitle }}</p>
                        <span class="inline-block rounded-full bg-brand-50 px-4 py-1.5 text-sm font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">{{ $previewCta }}</span>
                        <hr class="border-gray-100 dark:border-gray-800" />
                        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $previewWelcomeTitle }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Str::limit($previewWelcomeDescription, 150) }}</p>
                    </div>
                </div>

                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300 w-full justify-center">
                    <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M19 19H5V5h7V3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2v-7h-2v7zM14 3v2h3.59l-9.83 9.83 1.41 1.41L19 6.41V10h2V3h-7z" fill=""/></svg> Buka Beranda
                </a>

                <button type="submit" :disabled="submitting" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/></svg>
                    <x-admin.spinner x-show="submitting" />
                    Simpan Beranda
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script>
let slideIdx = {{ count($slideRows) }};
function addSlide(){const c=document.getElementById('slide-container'),d=document.createElement('div');
d.className='slide-item rounded-lg border border-gray-200 dark:border-gray-700 p-4';
d.innerHTML=`<input type="hidden" name="slides[${slideIdx}][existing_path]" value="">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div><label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Gambar</label>
<input name="slides[${slideIdx}][file]" type="file" accept=".jpg,.jpeg,.png,.webp"
class="dark:bg-dark-900 text-sm w-full text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100 dark:file:bg-brand-500/15 dark:file:text-brand-400"></div>
<div><label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Alt text<span class="text-error-500">*</span></label>
<input name="slides[${slideIdx}][alt]" type="text" maxlength="255"
class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></div></div>
<button type="button" onclick="this.closest('.slide-item').remove();updateSlideCount()"
class="mt-3 inline-flex items-center gap-1 text-sm text-gray-400 hover:text-error-500 transition"><svg class="fill-current" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/></svg> Hapus slide</button>`;
c.appendChild(d);slideIdx++;}
function updateSlideCount(){}
let advIdx = {{ count($advantageRows) }};
function addAdvantage(){const c=document.getElementById('advantage-container'),d=document.createElement('div');
d.className='advantage-item rounded-lg border border-gray-200 dark:border-gray-700 p-4';
d.innerHTML=`<input type="hidden" name="advantages[${advIdx}][existing_path]" value="">
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
<div><label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Urutan<span class="text-error-500">*</span></label>
<input name="advantages[${advIdx}][order]" type="number" min="1" max="20" required
class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></div>
<div><label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Judul<span class="text-error-500">*</span></label>
<input name="advantages[${advIdx}][title]" type="text" maxlength="255" required
class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></div></div>
<div class="mb-3"><label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Deskripsi<span class="text-error-500">*</span></label>
<textarea name="advantages[${advIdx}][description]" rows="2" maxlength="1000" required
class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-3 py-2 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-y"></textarea></div>
<div class="mb-3"><label class="mb-1 block text-xs font-medium text-gray-500 dark:text-gray-400">Gambar</label>
<input name="advantages[${advIdx}][image]" type="file" accept=".jpg,.jpeg,.png,.webp"
class="dark:bg-dark-900 text-sm w-full text-gray-700 dark:text-gray-300 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-50 file:text-brand-600 hover:file:bg-brand-100 dark:file:bg-brand-500/15 dark:file:text-brand-400"></div>
<button type="button" onclick="this.closest('.advantage-item').remove();updateAdvantageCount()"
class="inline-flex items-center gap-1 text-sm text-gray-400 hover:text-error-500 transition"><svg class="fill-current" width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/></svg> Hapus keunggulan</button>`;
c.appendChild(d);advIdx++;}
function updateAdvantageCount(){}
</script>
@endpush
