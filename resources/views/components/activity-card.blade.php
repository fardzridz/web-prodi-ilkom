@props(['activity'])

<article class="card-pill card-pill--link relative flex flex-col rounded-xl border border-line bg-white p-5 anim-fade-up anim-delay-1" data-reveal>
    <div class="pointer-events-none absolute -right-4 bottom-2 -rotate-[20deg]" aria-hidden="true">
        <span class="block h-3 w-28 rounded-full bg-line"></span>
        <span class="mt-2 block h-3 w-28 rounded-full bg-line"></span>
        <span class="mt-2 block h-3 w-28 rounded-full bg-line"></span>
    </div>
    <a href="{{ route('activities.show', $activity['slug']) }}" class="aspect-video overflow-hidden rounded-lg bg-primary-light">
        <img src="{{ $activity['image'] }}" alt="{{ $activity['title'] }}" class="h-full w-full object-cover" loading="lazy">
    </a>
    <div class="mt-4 flex items-center gap-4">
        @if($activity['category'])
        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-muted">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/></svg>
            {{ $activity['category'] }}
        </span>
        @endif
        <span class="inline-flex items-center gap-1.5 text-sm font-medium text-muted">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
            {{ $activity['date_label'] }}
        </span>
    </div>
    @if($activity['location'])
    <div class="mt-3 flex items-center gap-1.5 text-sm text-muted">
        <svg class="h-4 w-4 shrink-0 text-primary/70" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
        {{ $activity['location'] }}
    </div>
    @endif
    <h3 class="mt-3 font-display text-xl font-bold leading-snug text-primary line-clamp-2">
        <a href="{{ route('activities.show', $activity['slug']) }}" class="hover:text-gold-deep transition-colors">{{ $activity['title'] }}</a>
    </h3>
    <p class="text-sm text-muted line-clamp-2">{{ $activity['excerpt'] }}</p>
    <div class="mt-auto flex items-center gap-2.5 pt-6">
        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary-light">
            <svg class="h-4 w-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/></svg>
        </span>
        <span class="text-xs font-semibold uppercase tracking-wide text-primary">{{ $site?->site_name ?: 'Admin Ilkom' }}</span>
    </div>
</article>
