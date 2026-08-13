@props(['title', 'breadcrumbs' => [], 'image' => null])

<section class="relative overflow-hidden bg-badge text-cream" aria-label="Header halaman {{ $title }}">
    @if($image)
    <img src="{{ $image }}" alt="" class="absolute inset-0 z-0 h-full w-full object-cover">
    <div class="absolute inset-0 z-10 bg-gradient-to-r from-badge/90 via-badge/60 to-badge/25"></div>
    @endif
    <div class="relative z-20 mx-4 px-4 py-14 sm:mx-8 sm:px-8 sm:py-16 lg:mx-16 lg:px-20 lg:py-20">
        <nav aria-label="Breadcrumb" class="anim-fade-up anim-delay-1 flex flex-wrap items-center gap-1.5 text-sm">
            <a href="{{ route('home') }}" class="text-cream/60 hover:text-gold transition-colors">Beranda</a>
            @foreach($breadcrumbs as $label => $url)
            <span class="text-cream/30" aria-hidden="true">/</span>
            @if($url)
            <a href="{{ $url }}" class="text-cream/60 hover:text-gold transition-colors">{{ $label }}</a>
            @else
            <span class="font-semibold text-gold" aria-current="page">{{ $label }}</span>
            @endif
            @endforeach
        </nav>
        <h1 class="anim-fade-up anim-delay-2 mt-3 font-display text-3xl font-bold uppercase tracking-wide sm:text-4xl lg:text-5xl">
            {{ $title }}
        </h1>
        @if($slot->isNotEmpty())
        <p class="anim-fade-up anim-delay-3 mt-3 max-w-2xl text-sm text-cream/80 sm:text-base">
            {{ $slot }}
        </p>
        @endif
    </div>
</section>
