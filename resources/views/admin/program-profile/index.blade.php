@extends('layouts.admin')

@section('title', 'Profil Prodi | Pengelola Situs Prodi')
@section('page-heading', 'Profil Prodi')

@section('content')
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Editor Konten Profil</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Gunakan toolbar di bawah setiap editor untuk mengatur format teks.</p>
        </div>
        @if ($programProfile->exists && $programProfile->updated_at)
            <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">Terakhir diperbarui {{ $programProfile->updated_at->translatedFormat('d F Y, H:i') }} WIB</span>
        @endif
    </div>

    <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('admin.profil.update') }}" method="post">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2 space-y-6">
                @php
                    $sections = [
                        ['key' => 'description', 'label' => 'Deskripsi Program Studi', 'icon' => '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/><path d="M4 20h16v-2.5c0-1.5-3-2.5-8-2.5s-8 1-8 2.5V20z" fill="currentColor"/>'],
                        ['key' => 'history', 'label' => 'Sejarah Program Studi', 'icon' => '<path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm2 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/>'],
                        ['key' => 'vision', 'label' => 'Visi Program Studi', 'icon' => '<path d="M12 4.5C7 4.5 2.73 7.61 1 12c1.73 4.39 6 7.5 11 7.5s9.27-3.11 11-7.5c-1.73-4.39-6-7.5-11-7.5zM12 17c-2.76 0-5-2.24-5-5s2.24-5 5-5 5 2.24 5 5-2.24 5-5 5zm0-8c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z" fill="currentColor"/>'],
                        ['key' => 'mission', 'label' => 'Misi Program Studi', 'icon' => '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z" fill="currentColor"/>'],
                        ['key' => 'goals', 'label' => 'Tujuan Program Studi', 'icon' => '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-5h10v2H7zm3.3-3.71L8.88 9.88l-1.59 1.59L4 8.18l1.41-1.41L8.88 9l3.3-3.29 3.3 3.29L17 8.18l-3.29 3.29z" fill="currentColor"/>'],
                    ];
                @endphp

                @foreach ($sections as $section)
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                                <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">{!! $section['icon'] !!}</svg>
                            </span>
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">{{ $section['label'] }}</h4>
                        </div>
                        <div class="p-6">
                            <div id="profile-{{ $section['key'] }}" class="quill-editor"></div>
                            <input id="profile-{{ $section['key'] }}-hidden" type="hidden" name="{{ $section['key'] }}" value="{{ old($section['key'], $programProfile->{$section['key']}) }}">
                            @error($section['key'])<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                @endforeach

                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex items-center gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                            <svg class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" fill=""/></svg>
                        </span>
                        <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Keunggulan Program Studi</h4>
                    </div>
                    <div class="p-6">
                        <div id="profile-advantages" class="quill-editor"></div>
                        <input id="profile-advantages-hidden" type="hidden" name="advantages" value="{{ old('advantages', $programProfile->advantages) }}">
                        @error('advantages')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800"><h4 class="text-base font-medium text-gray-800 dark:text-white/90">Akreditasi</h4></div>
                    <div class="p-6">
                        <label for="profile-accreditation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status akreditasi<span class="text-error-500">*</span></label>
                        <input id="profile-accreditation" name="accreditation" type="text" maxlength="255" value="{{ old('accreditation', $programProfile->accreditation) }}" placeholder="Contoh: Baik Sekali" required
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                        @error('accreditation')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.profil') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300">Batal</a>
                    <button type="submit" :disabled="submitting" class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-5 py-3 text-sm font-medium text-white transition flex-1 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/></svg>
                        <x-admin.spinner x-show="submitting" />
                        Simpan Profil
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
