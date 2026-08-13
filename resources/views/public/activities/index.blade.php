@extends('layouts.public')

@section('title', 'Kegiatan — ' . ($site?->site_name ?: 'Program Studi Ilmu Komputer'))

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/list-filter.js') }}"></script>
<script>
ListFilter({
    searchId: 'kegiatan-search',
    gridId: 'kegiatan-grid',
    filterSelector: '.keg-filter',
    counterTextId: 'kegiatan-count-text',
    emptyId: 'kegiatan-empty',
    label: 'kegiatan'
});
</script>
@endpush

@section('content')
<x-hero title="Kegiatan" :breadcrumbs="['Kegiatan' => null]" image="{{ asset('assets/images/hero/hero-1.jpeg') }}">
    Ragam kegiatan yang memperkaya pengalaman mahasiswa — dari seminar nasional, workshop, hingga ajang prestasi tingkat nasional.
</x-hero>

<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="anim-fade-up anim-delay-1" data-reveal>
            <div class="text-left">
                <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold anim-fade-up anim-delay-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008v-.008Zm2.25-2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Z"/></svg>
                    Kegiatan {{ $site?->site_name ?: 'Uniwara' }}
                </h3>
                <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">Daftar Kegiatan</h1>
                <p class="mt-6 max-w-2xl text-[0.95rem] leading-relaxed text-ink/70 anim-fade-up anim-delay-3">Telusuri dan saring kegiatan berdasarkan kategori — temukan berita terbaru, capaian prestasi, agenda akademik, hingga kegiatan kemahasiswaan.</p>
            </div>
        </div>

        <div class="mt-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:gap-6">
                <label class="relative block w-full max-w-xl" for="kegiatan-search">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input id="kegiatan-search" type="search" placeholder="Cari judul, lokasi, atau kategori kegiatan..." class="h-12 w-full rounded-full border border-line bg-white pl-12 pr-4 text-sm text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-shadow">
                </label>
                <p id="kegiatan-count" class="inline-flex h-12 shrink-0 items-center gap-2 rounded-full border border-line bg-white px-5 text-sm font-medium text-muted">
                    <svg class="h-4 w-4 shrink-0 text-primary/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 2.994v2.25m10.5-2.25v2.25m-14.252 13.5V7.491a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v11.251m-18 0a2.25 2.25 0 0 0 2.25 2.25h13.5a2.25 2.25 0 0 0 2.25-2.25m-18 0v-7.5a2.25 2.25 0 0 1 2.25-2.25h13.5a2.25 2.25 0 0 1 2.25 2.25v7.5m-6.75-6h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008v-.008Z"/></svg>
                    <span id="kegiatan-count-text"></span>
                </p>
            </div>
            <div id="kegiatan-filters" class="mt-6 flex flex-wrap items-center gap-2.5" role="group" aria-label="Filter kategori kegiatan">
                <button type="button" data-filter="semua" aria-pressed="true" class="keg-filter filter-pill rounded-full border border-primary px-5 py-2 text-sm font-semibold text-white">Semua</button>
                @foreach($categories as $cat)
                <button type="button" data-filter="{{ strtolower($cat) }}" aria-pressed="false" class="keg-filter filter-pill rounded-full border border-line bg-white px-5 py-2 text-sm font-medium text-muted">{{ $cat }}</button>
                @endforeach
            </div>
        </div>

        <div id="kegiatan-grid" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($activities as $activity)
            <div data-category="{{ strtolower($activity['category']) ?: 'lainnya' }}" data-search="{{ $activity['title'] }} {{ $activity['category'] }} {{ $activity['excerpt'] }} {{ $activity['location'] }}">
                <x-activity-card :activity="$activity" />
            </div>
            @empty
            @endforelse
        </div>

        <div id="kegiatan-empty" class="mt-10 hidden flex-col items-center justify-center rounded-xl border border-dashed border-primary/30 bg-white/60 px-6 py-14 text-center">
            <svg class="h-12 w-12 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
            <h4 class="mt-4 font-display text-lg font-bold text-primary">Kegiatan tidak ditemukan</h4>
            <p class="mt-1 text-sm text-muted">Coba ubah kata kunci pencarian atau pilih kategori lain.</p>
        </div>
    </div>
</section>

<x-cta-banner />
@endsection
