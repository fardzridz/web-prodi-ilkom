@props(['lecturer', 'dataCategory' => '', 'dataSearch' => ''])

<article class="card-pill group relative flex h-full flex-col rounded-2xl border border-line bg-white p-6" data-category="{{ $dataCategory }}" data-search="{{ $dataSearch }}" data-reveal>
    <div class="aspect-square w-full overflow-hidden rounded-xl bg-primary-light">
        <img src="{{ $lecturer['image'] ?? asset('assets/images/hero/hero-1.jpeg') }}" alt="{{ $lecturer['name'] }}" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
    </div>

    <div class="mt-5 flex flex-col gap-2.5 flex-1">
        @if($lecturer['position'])
        <span class="inline-flex w-fit items-center gap-1 rounded-full bg-gold-light px-2.5 py-1 text-[0.7rem] font-bold uppercase tracking-widest text-gold-deep">
            {{ $lecturer['position'] }}
        </span>
        @endif

        <h3 class="font-display text-lg font-bold leading-tight text-primary">
            {{ $lecturer['name'] }}
        </h3>

        @if($lecturer['nidn'])
        <p class="text-[0.8rem] font-medium tracking-wide text-muted/80">NIDN {{ $lecturer['nidn'] }}</p>
        @endif

        @if($lecturer['expertise'])
        <p class="text-[0.85rem] leading-relaxed text-primary/80">
            <span class="font-semibold text-primary">Bidang: </span>{{ $lecturer['expertise'] }}
        </p>
        @endif

        @if($lecturer['description'])
        <p class="text-[0.85rem] leading-relaxed text-muted line-clamp-3">{{ $lecturer['description'] }}</p>
        @endif
    </div>

    @if($lecturer['education'])
    <div class="mt-4 border-t border-line pt-4">
        <span class="text-[0.8rem] font-semibold uppercase tracking-wider text-primary/70">{{ $lecturer['education'] }}</span>
    </div>
    @endif
</article>
