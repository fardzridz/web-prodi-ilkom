@extends('layouts.admin')

@section('title', 'Pengaturan Situs | Pengelola Situs Prodi')
@section('page-heading', 'Pengaturan Situs')

@section('content')
    @php($footerLinks = old('footer_links', $siteSetting->footer_academic_links ?? []))

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Identitas Website</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pengaturan nama situs, logo, e-jurnal, tautan pendaftaran, dan footer.</p>
            </div>
            @if ($siteSetting->exists && $siteSetting->updated_at)
                <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                    Terakhir diperbarui {{ $siteSetting->updated_at->translatedFormat('d F Y, H:i') }} WIB
                </span>
            @endif
        </div>

        <form x-data="{ submitting: false }" @submit="submitting = true" action="{{ route('admin.pengaturan.update') }}" method="post" enctype="multipart/form-data" id="settings-form">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Nama dan Institusi</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Informasi resmi identitas program studi.</p>
                        </div>
                        <div class="p-6 space-y-5">
                            <div>
                                <label for="setting-site-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Nama situs<span class="text-error-500">*</span>
                                </label>
                                <input id="setting-site-name" name="site_name" type="text" maxlength="255"
                                    value="{{ old('site_name', $siteSetting->site_name) }}" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('site_name')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                                <div>
                                    <label for="setting-university-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Universitas<span class="text-error-500">*</span>
                                    </label>
                                    <input id="setting-university-name" name="university_name" type="text" maxlength="255"
                                        value="{{ old('university_name', $siteSetting->university_name) }}" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    @error('university_name')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    <label for="setting-faculty-name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                        Fakultas<span class="text-error-500">*</span>
                                    </label>
                                    <input id="setting-faculty-name" name="faculty_name" type="text" maxlength="255"
                                        value="{{ old('faculty_name', $siteSetting->faculty_name) }}" required
                                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                    @error('faculty_name')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>
                            </div>
                            <div>
                                <label for="setting-journal-url" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tautan e-Jurnal<span class="text-error-500">*</span>
                                </label>
                                <input id="setting-journal-url" name="journal_url" type="url" maxlength="2048"
                                    value="{{ old('journal_url', $siteSetting->journal_url) }}" placeholder="https://ejurnal.example.ac.id" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('journal_url')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="setting-registration-url" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                    Tautan Pendaftaran<span class="text-error-500">*</span>
                                </label>
                                <input id="setting-registration-url" name="registration_url" type="url" maxlength="2048"
                                    value="{{ old('registration_url', $siteSetting->registration_url) }}" placeholder="https://admisi.example.ac.id" required
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
                                @error('registration_url')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Logo dan Favicon</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Media merek situs program studi.</p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Logo situs</label>
                                    <x-admin.image-upload id="setting-logo" name="logo"
                                        :existing-src="$siteSetting->logo ? asset('storage/'.$siteSetting->logo) : null"
                                        label="Upload logo"
                                        help-text="JPG, PNG, WebP — maks 2 MB &bull; GIF — maks 7 MB"
                                        preview-class="max-h-20 mx-auto object-contain" />
                                    @if ($siteSetting->logo)
                                        <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 cursor-pointer">
                                            <input name="remove_logo" type="checkbox" value="1" @checked(old('remove_logo')) class="rounded border-gray-300" />
                                            Hapus logo saat disimpan
                                        </label>
                                    @endif
                                    @error('logo')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Favicon situs</label>
                                    <x-admin.image-upload id="setting-favicon" name="favicon"
                                        :existing-src="$siteSetting->favicon ? asset('storage/'.$siteSetting->favicon) : null"
                                        label="Upload favicon"
                                        help-text="ICO, PNG — maks 512 KB"
                                        accept="image/x-icon,image/png"
                                        preview-class="max-h-20 mx-auto object-contain" />
                                    @if ($siteSetting->favicon)
                                        <label class="mt-3 inline-flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 cursor-pointer">
                                            <input name="remove_favicon" type="checkbox" value="1" @checked(old('remove_favicon')) class="rounded border-gray-300" />
                                            Hapus favicon saat disimpan
                                        </label>
                                    @endif
                                    @error('favicon')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3">
                            <div>
                                <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Tautan Akademik Footer</h4>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Navigasi bagian bawah situs.</p>
                            </div>
                            <button type="button"
                                class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 ring-1 ring-inset ring-gray-300 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300"
                                onclick="addFooterLink()">
                                <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M19 13h-6v6h-2v-6H5v-2h6V5h2v6h6v2z" fill=""/>
                                </svg>
                                Tambah Tautan
                            </button>
                        </div>
                        <div class="p-6" id="footer-link-list">
                            <div class="space-y-4" id="footer-link-container">
                                @foreach ($footerLinks as $index => $link)
                                    <div class="flex items-start gap-3 footer-link-item">
                                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <input name="footer_links[{{ $index }}][label]" type="text" maxlength="255"
                                                value="{{ $link['label'] ?? '' }}" required placeholder="Label tautan"
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                            <input name="footer_links[{{ $index }}][url]" type="url" maxlength="2048"
                                                value="{{ $link['url'] ?? '' }}" required placeholder="https://..."
                                                class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                                        </div>
                                        <button type="button" onclick="this.closest('.footer-link-item').remove(); updateEmptyState()"
                                            class="shrink-0 inline-flex items-center justify-center h-11 w-11 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition">
                                            <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                                                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/>
                                            </svg>
                                        </button>
                                        @error("footer_links.$index.label")<p class="text-sm text-error-500">{{ $message }}</p>@enderror
                                        @error("footer_links.$index.url")<p class="text-sm text-error-500">{{ $message }}</p>@enderror
                                    </div>
                                @endforeach
                            </div>
                            <div id="footer-link-empty" class="py-8 text-center {{ count($footerLinks) > 0 ? 'hidden' : '' }}">
                                <svg class="fill-current mb-3 mx-auto text-gray-300 dark:text-gray-600" width="36" height="36" viewBox="0 0 24 24" fill="none">
                                    <path d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z" fill=""/>
                                </svg>
                                <p class="text-sm text-gray-400 dark:text-gray-500">Belum ada tautan akademik pada footer.</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Teks Footer</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Hak cipta dan teks bawah halaman.</p>
                        </div>
                        <div class="p-6">
                            <textarea id="setting-footer-text" name="footer_text" rows="4" maxlength="5000" required
                                class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 resize-y">{{ old('footer_text', $siteSetting->footer_text) }}</textarea>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ mb_strlen($siteSetting->footer_text ?? '') }} / 5.000 karakter</p>
                            @error('footer_text')<p class="mt-1.5 text-sm text-error-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                        <div class="px-6 py-5">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                                <svg class="fill-current" width="20" height="20" viewBox="0 0 24 24" fill="none">
                                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill=""/>
                                </svg>
                            </div>
                            <h4 class="text-base font-medium text-gray-800 dark:text-white/90">Upload Aman</h4>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">File baru divalidasi sebelum menggantikan file lama. Logo dan favicon lama baru dihapus setelah perubahan berhasil disimpan.</p>
                        </div>
                    </div>

                    <button type="submit" :disabled="submitting"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 flex w-full items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg x-show="!submitting" class="fill-current" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M17 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V7l-4-4zm-5 16c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm3-10H5V5h10v4z" fill=""/>
                        </svg>
                        <x-admin.spinner x-show="submitting" />
                        Simpan Pengaturan
                    </button>
                </div>
            </div>
    </form>
@endsection

@push('scripts')
<script>
    let footerLinkIndex = {{ count($footerLinks) }};

    function addFooterLink() {
        const container = document.getElementById('footer-link-container');
        const empty = document.getElementById('footer-link-empty');
        const idx = footerLinkIndex++;

        const div = document.createElement('div');
        div.className = 'flex items-start gap-3 footer-link-item';
        div.innerHTML = `
            <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-3">
                <input name="footer_links[${idx}][label]" type="text" maxlength="255" required placeholder="Label tautan"
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
                <input name="footer_links[${idx}][url]" type="url" maxlength="2048" required placeholder="https://..."
                    class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
            </div>
            <button type="button" onclick="this.closest('.footer-link-item').remove(); updateEmptyState()"
                class="shrink-0 inline-flex items-center justify-center h-11 w-11 rounded-lg text-gray-400 hover:text-error-500 hover:bg-error-50 dark:hover:bg-error-500/10 transition">
                <svg class="fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none">
                    <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill=""/>
                </svg>
            </button>`;
        container.appendChild(div);
        updateEmptyState();
    }

    function updateEmptyState() {
        const container = document.getElementById('footer-link-container');
        const empty = document.getElementById('footer-link-empty');
        empty.classList.toggle('hidden', container.querySelectorAll('.footer-link-item').length > 0);
    }
</script>
@endpush
