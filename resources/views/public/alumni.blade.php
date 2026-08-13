@extends('layouts.public')

@section('title', 'Alumni — ' . ($site?->site_name ?: 'Program Studi Ilmu Komputer'))

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('js/list-filter.js') }}"></script>
<script>
ListFilter({
    searchId: 'alumni-search',
    gridId: 'alumni-grid',
    filterSelector: '.alumni-filter',
    counterTextId: 'alumni-count-text',
    emptyId: 'alumni-empty',
    label: 'alumni',
    matchCat: 'includes',
    activeClasses: ['border-gold', 'bg-gold', 'text-primary', 'font-semibold'],
    inactiveClasses: ['border', 'border-white/20', 'text-cream/70', 'font-medium']
});
</script>
@endpush

@section('content')
<x-hero title="Alumni" :breadcrumbs="['Alumni' => null]" image="{{ asset('assets/images/hero/hero-1.jpeg') }}">
    Jejak karier dan testimoni para lulusan {{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }}.
</x-hero>

<section class="bg-primary py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="anim-fade-up anim-delay-1" data-reveal>
            <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-gold text-base font-semibold anim-fade-up anim-delay-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                Alumni
            </h3>
            <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-cream sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">Jejak Alumni</h1>
            <p class="mt-6 max-w-2xl text-[0.95rem] leading-relaxed text-cream/75 anim-fade-up anim-delay-3">Lihat perjalanan karier lulusan yang telah berkontribusi di berbagai bidang teknologi.</p>
        </div>

        <div class="mt-10">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between lg:gap-6">
                <label class="relative block w-full max-w-xl" for="alumni-search">
                    <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-cream/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                    <input id="alumni-search" type="search" placeholder="Cari nama, posisi, atau perusahaan..." class="h-12 w-full rounded-full border border-white/20 bg-white/10 pl-12 pr-4 text-sm text-cream placeholder:text-cream/40 focus:border-gold focus:outline-none focus:ring-2 focus:ring-gold/20 transition-shadow">
                </label>
                <p id="alumni-count" class="inline-flex h-12 shrink-0 items-center gap-2 rounded-full border border-white/20 bg-white/10 px-5 text-sm font-medium text-cream/70">
                    <svg class="h-4 w-4 shrink-0 text-gold/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/></svg>
                    <span id="alumni-count-text"></span>
                </p>
            </div>

            <div id="alumni-filters" class="mt-6 flex flex-wrap items-center gap-2.5" role="group" aria-label="Filter posisi alumni">
                <button type="button" data-filter="semua" aria-pressed="true" class="alumni-filter filter-pill filter-pill--gold rounded-full border border-gold px-5 py-2 text-sm font-semibold text-primary">Semua</button>
                @foreach($jobPositions as $pos)
                <button type="button" data-filter="{{ strtolower($pos) }}" aria-pressed="false" class="alumni-filter filter-pill filter-pill--gold rounded-full border border-white/20 px-5 py-2 text-sm font-medium text-cream/70 hover:border-gold">{{ $pos }}</button>
                @endforeach
            </div>
        </div>

        <div id="alumni-grid" class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse($alumni as $alumnus)
            <x-alumni-card :alumni="$alumnus" data-category="{{ strtolower($alumnus['company'] ?? '') }} {{ strtolower($alumnus['job_position'] ?? '') }}" data-search="{{ $alumnus['name'] }} {{ $alumnus['job_position'] }} {{ $alumnus['company'] }} {{ $alumnus['batch_year'] }}" />
            @empty
            @endforelse
        </div>

        <div id="alumni-empty" class="mt-10 hidden flex-col items-center justify-center rounded-xl border border-dashed border-gold/30 bg-white/5 px-6 py-14 text-center">
            <svg class="h-12 w-12 text-gold/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z"/></svg>
            <h4 class="mt-4 font-display text-lg font-bold text-cream">Alumni tidak ditemukan</h4>
            <p class="mt-1 text-sm text-cream/60">Coba ubah kata kunci pencarian atau pilih bidang lain.</p>
        </div>
    </div>
</section>

<x-cta-banner heading="Bangun Jejak Karier Berkualitas" />
@endsection
