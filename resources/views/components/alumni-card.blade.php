@props(['alumni', 'dataCategory' => '', 'dataSearch' => ''])

<article class="relative flex h-full flex-col rounded-xl border border-white/10 bg-white/5 p-6 transition-colors hover:bg-white/10" data-category="{{ $dataCategory }}" data-search="{{ $dataSearch }}" data-reveal>
    @if($alumni['batch_year'] || $alumni['graduation_year'])
    <div class="inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-wide text-gold">
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
        </svg>
        @if($alumni['batch_year'])Angkatan {{ $alumni['batch_year'] }}@endif
        @if($alumni['batch_year'] && $alumni['graduation_year']) · @endif
        @if($alumni['graduation_year'])Lulus {{ $alumni['graduation_year'] }}@endif
    </div>
    @endif
    <div class="mt-6 flex items-center gap-4">
        <img src="{{ $alumni['image'] }}" alt="Foto {{ $alumni['name'] }}" class="h-16 w-16 shrink-0 rounded-full object-cover" loading="lazy">
        <h3 class="font-display text-xl font-bold leading-snug text-cream">{{ $alumni['name'] }}</h3>
    </div>
    @if($alumni['quote'])
    <p class="mt-3 text-sm leading-relaxed text-cream/75 line-clamp-3">{{ $alumni['quote'] }}</p>
    @endif
    <div class="mt-auto border-t border-white/10 pt-6">
        @if($alumni['job_position'])
        <span class="block text-sm font-bold text-gold">{{ $alumni['job_position'] }}</span>
        @endif
        @if($alumni['company'])
        <span class="mt-1 block text-xs text-cream/60">{{ $alumni['company'] }}</span>
        @endif
    </div>
</article>
