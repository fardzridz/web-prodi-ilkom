@extends('layouts.public')

@section('title', 'Dokumen — ' . ($site?->site_name ?: 'Program Studi Ilmu Komputer'))

@push('head')
<script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/docx-preview@0.3.3/dist/docx-preview.min.js"></script>
@endpush

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/list-filter.js') }}"></script>
<script>
ListFilter({
    searchId: 'dokumen-search',
    gridId: 'dokumen-grid',
    filterSelector: '.dokumen-filter',
    counterTextId: 'dokumen-count-text',
    emptyId: 'dokumen-empty',
    label: 'dokumen',
    matchCat: 'includes',
    toggle: {
        kartuBtnId: 'view-kartu',
        listBtnId: 'view-list'
    }
});

(function () {
    const modal = document.getElementById('doc-preview-modal');
    if (!modal) return;
    const overlay = modal.querySelector('.doc-preview-overlay');
    const closeBtn = modal.querySelector('.doc-preview-close');
    const iframe = modal.querySelector('.doc-preview-iframe');
    const docxDiv = modal.querySelector('.doc-preview-docx');
    const loading = modal.querySelector('.doc-preview-loading');
    const fallback = modal.querySelector('.doc-preview-fallback');
    const titleEl = modal.querySelector('.doc-preview-title');
    const typeEl = modal.querySelector('.doc-preview-type');

    function reset() {
        iframe.src = '';
        iframe.classList.add('hidden');
        docxDiv.classList.add('hidden');
        docxDiv.innerHTML = '';
        loading.classList.add('hidden');
        fallback.classList.add('hidden');
    }

    function showLoading() {
        reset();
        loading.classList.remove('hidden');
    }

    function open(url, title, type, downloadUrl) {
        titleEl.textContent = title;
        typeEl.textContent = type;
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        if (type === 'PDF') {
            reset();
            iframe.classList.remove('hidden');
            iframe.src = url;
        } else if (type === 'DOCX') {
            showLoading();
            fetch(url)
                .then(function (r) { return r.arrayBuffer(); })
                .then(function (buf) {
                    loading.classList.add('hidden');
                    docxDiv.classList.remove('hidden');
                    return docx.renderAsync(buf, docxDiv, null, {
                        inWrapper: true,
                        ignoreWidth: false,
                        ignoreHeight: false,
                        ignoreFonts: false,
                        breakPages: true,
                        debug: false,
                        experimental: false,
                        className: 'docx-wrapper',
                        ignoreLastRenderedPageBreak: true,
                    });
                })
                .catch(function () {
                    loading.classList.add('hidden');
                    fallback.classList.remove('hidden');
                    var link = fallback.querySelector('.doc-fallback-download');
                    if (link) link.href = downloadUrl;
                });
        } else {
            reset();
            fallback.classList.remove('hidden');
            var link = fallback.querySelector('.doc-fallback-download');
            if (link) link.href = downloadUrl;
        }
    }

    function close() {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        reset();
    }

    document.querySelectorAll('.doc-preview-trigger').forEach(function (trigger) {
        trigger.addEventListener('click', function () {
            open(
                this.dataset.previewUrl,
                this.dataset.previewTitle,
                this.dataset.previewType,
                this.dataset.downloadUrl
            );
        });
    });

    overlay.addEventListener('click', close);
    closeBtn.addEventListener('click', close);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) close();
    });
})();
</script>
@endpush

@section('content')
<x-hero title="Dokumen" :breadcrumbs="['Dokumen' => null]" image="{{ asset('assets/images/hero/hero-1.jpeg') }}">
    Berkas resmi {{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }} — kurikulum, panduan, SOP, administrasi, dan akreditasi dalam satu tempat.
</x-hero>

<section class="bg-line overflow-x-hidden py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="anim-fade-up anim-delay-1" data-reveal>
            <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold anim-fade-up anim-delay-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                Dokumen Resmi Prodi
            </h3>
            <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">Pusat Dokumen</h1>
            <p class="mt-6 max-w-2xl text-[0.95rem] leading-relaxed text-ink/70 anim-fade-up anim-delay-3">Kumpulan berkas resmi Program Studi Ilmu Komputer — kurikulum, panduan akademik, SOP, formulir administrasi, hingga dokumen akreditasi. Unduh atau lihat pratinjau sesuai kebutuhan Anda.</p>
        </div>

        <div class="mt-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:gap-6">
                <label class="relative block w-full max-w-xl" for="dokumen-search">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input id="dokumen-search" type="search" placeholder="Cari nama dokumen..." class="h-12 w-full rounded-full border border-line bg-white pl-12 pr-4 text-sm text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-shadow">
                </label>
                <div class="flex items-center gap-3">
                    <p id="dokumen-count" class="inline-flex h-12 shrink-0 items-center gap-2 rounded-full border border-line bg-white px-5 text-sm font-medium text-muted">
                        <svg class="h-4 w-4 shrink-0 text-primary/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                        <span id="dokumen-count-text"></span>
                    </p>
                    <div id="dokumen-view-toggle" class="inline-flex h-12 shrink-0 items-center gap-1 rounded-full border border-line bg-white p-1" role="group" aria-label="Mode tampilan dokumen">
                        <button type="button" id="view-kartu" aria-pressed="true" class="dokumen-view-btn view-toggle-btn inline-flex h-10 items-center gap-2 rounded-full px-4 text-sm sm:px-4">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                            <span class="hidden sm:inline">Kartu</span>
                        </button>
                        <button type="button" id="view-list" aria-pressed="false" class="dokumen-view-btn view-toggle-btn inline-flex h-10 items-center gap-2 rounded-full px-4 text-sm sm:px-4">
                            <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"/></svg>
                            <span class="hidden sm:inline">List</span>
                        </button>
                    </div>
                </div>
            </div>

            <div id="dokumen-filters" class="mt-6 flex flex-wrap items-center gap-2.5" role="group" aria-label="Filter kategori dokumen">
                <button type="button" data-filter="semua" aria-pressed="true" class="dokumen-filter filter-pill rounded-full border border-primary px-5 py-2 text-sm font-semibold text-white">Semua</button>
                @foreach($documentCategories as $cat)
                <button type="button" data-filter="{{ strtolower($cat) }}" aria-pressed="false" class="dokumen-filter filter-pill rounded-full border border-line bg-white px-5 py-2 text-sm font-medium text-muted">{{ $cat }}</button>
                @endforeach
            </div>
        </div>

        <div id="dokumen-grid" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($documents as $document)
            <x-document-card :document="$document" data-category="{{ strtolower($document['category']) }}" data-search="{{ $document['title'] }} {{ $document['category'] }} {{ $document['description'] }}" />
            @empty
            @endforelse
        </div>

        <div id="dokumen-empty" class="mt-10 hidden flex-col items-center justify-center rounded-xl border border-dashed border-primary/30 bg-white/60 px-6 py-14 text-center">
            <svg class="h-12 w-12 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m6.75 12H9m1.5-6H9m4.5 3H9"/></svg>
            <h4 class="mt-4 font-display text-lg font-bold text-primary">Dokumen tidak ditemukan</h4>
            <p class="mt-1 text-sm text-muted">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
        </div>
    </div>
</section>

<x-cta-banner heading="Dokumen Lengkap, Akses Mudah" />

<div id="doc-preview-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true" aria-label="Pratinjau dokumen">
    <div class="doc-preview-overlay fixed inset-0 bg-ink/70 backdrop-blur-sm"></div>
    <div class="doc-preview-panel relative z-10 flex w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl" style="max-height: calc(100vh - 2rem);">
        <div class="doc-preview-header flex shrink-0 items-center justify-between gap-3 border-b border-line px-5 py-4 sm:px-7">
            <div class="min-w-0 flex-1">
                <h3 class="doc-preview-title truncate font-display text-lg font-bold text-primary"></h3>
                <span class="doc-preview-type mt-0.5 inline-block text-xs font-semibold uppercase tracking-widest text-muted"></span>
            </div>
            <button type="button" class="doc-preview-close inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-muted transition-colors hover:bg-line hover:text-primary" aria-label="Tutup pratinjau">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="doc-preview-body flex-1 bg-black/5">
            <iframe class="doc-preview-iframe hidden h-full w-full" src="" style="min-height: 70vh;" title="Pratinjau dokumen" allowfullscreen></iframe>
            <div class="doc-preview-docx hidden overflow-auto p-6 sm:p-10 docx-content" style="min-height: 70vh; max-height: 75vh;"></div>
            <div class="doc-preview-loading hidden flex items-center justify-center flex-col gap-3" style="min-height: 70vh;">
                <div class="h-10 w-10 animate-spin rounded-full border-[3px] border-primary/20 border-t-primary"></div>
                <span class="text-sm text-muted">Memuat pratinjau...</span>
            </div>
            <div class="doc-preview-fallback hidden flex flex-col items-center justify-center px-6 py-20 text-center">
                <svg class="h-16 w-16 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"/></svg>
                <h4 class="mt-5 font-display text-lg font-bold text-primary">Pratinjau tidak tersedia</h4>
                <p class="mt-2 text-sm text-muted">Tipe file ini tidak dapat ditampilkan langsung. Silakan unduh untuk melihat isinya.</p>
                <a href="#" class="doc-fallback-download btn btn-primary btn-md mt-6">
                    <span class="btn-label">Unduh Berkas</span>
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
