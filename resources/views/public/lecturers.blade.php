@extends('layouts.public')

@section('title', 'Dosen — ' . ($site?->site_name ?: 'Program Studi Ilmu Komputer'))

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/list-filter.js') }}"></script>
<script>
ListFilter({
    searchId: 'dosen-search',
    gridId: 'dosen-grid',
    filterSelector: '.dosen-filter',
    counterTextId: 'dosen-count-text',
    emptyId: 'dosen-empty',
    label: 'dosen'
});
</script>
@endpush

@section('content')
<x-hero title="Dosen" :breadcrumbs="['Dosen' => null]" image="{{ asset('assets/images/hero/hero-1.jpeg') }}">
    Tim pengajar {{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }} — praktisi akademisi dengan keahlian beragam yang siap membimbing mahasiswa menghadapi tantangan teknologi.
</x-hero>

<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="anim-fade-up anim-delay-1" data-reveal>
            <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold anim-fade-up anim-delay-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                Tim Pengajar
            </h3>
            <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">Daftar Dosen</h1>
            <p class="mt-6 max-w-2xl text-[0.95rem] leading-relaxed text-ink/70 anim-fade-up anim-delay-3">Telusuri dan saring dosen berdasarkan bidang keahlian — temukan pengajar sesuai fokus riset dan minat studi Anda.</p>
        </div>

        <div class="mt-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:gap-6">
                <label class="relative block w-full max-w-xl" for="dosen-search">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-muted" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input id="dosen-search" type="search" placeholder="Cari nama atau NIDN dosen..." class="h-12 w-full rounded-full border border-line bg-white pl-12 pr-4 text-sm text-ink placeholder:text-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20 transition-shadow">
                </label>
                <p id="dosen-count" class="inline-flex h-12 shrink-0 items-center gap-2 rounded-full border border-line bg-white px-5 text-sm font-medium text-muted">
                    <svg class="h-4 w-4 shrink-0 text-primary/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/></svg>
                    <span id="dosen-count-text"></span>
                </p>
            </div>

            <div id="dosen-filters" class="mt-6 flex flex-wrap items-center gap-2.5" role="group" aria-label="Filter bidang keahlian dosen">
                <button type="button" data-filter="semua" aria-pressed="true" class="dosen-filter filter-pill rounded-full border border-primary px-5 py-2 text-sm font-semibold text-white">Semua</button>
                @foreach($expertises as $exp)
                <button type="button" data-filter="{{ $exp }}" aria-pressed="false" class="dosen-filter filter-pill rounded-full border border-line bg-white px-5 py-2 text-sm font-medium text-muted">{{ $exp }}</button>
                @endforeach
            </div>
        </div>

        <div id="dosen-grid" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($lecturers as $lecturer)
            <x-dosen-card :lecturer="$lecturer" data-category="{{ $lecturer['expertise'] }}" data-search="{{ $lecturer['name'] }} {{ $lecturer['nidn'] }} {{ $lecturer['expertise'] }} {{ $lecturer['position'] }}" />
            @empty
            @endforelse
        </div>

        <div id="dosen-empty" class="mt-10 hidden flex-col items-center justify-center rounded-xl border border-dashed border-primary/30 bg-white/60 px-6 py-14 text-center">
            <svg class="h-12 w-12 text-primary/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
            <h4 class="mt-4 font-display text-lg font-bold text-primary">Dosen tidak ditemukan</h4>
            <p class="mt-1 text-sm text-muted">Coba ubah kata kunci pencarian atau pilih bidang keahlian lain.</p>
        </div>
    </div>
</section>

<x-cta-banner heading="Belajar Langsung dari Pengajar Terbaik" />
@endsection
