@extends('layouts.public')

@section('title', $site?->site_name ?: 'Program Studi Ilmu Komputer')
@section('description', $homeSection?->welcome_description ?: 'Website resmi Program Studi Ilmu Komputer')

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
<script>
(function () {
    const hero = document.querySelector('[data-hero]');
    const header = document.querySelector('header');
    const topbar = document.querySelector('[data-topbar]');
    if (!hero || !header) return;
    const measure = () => {
        const topbarH = topbar ? topbar.offsetHeight : 0;
        hero.style.top = (topbarH + header.offsetHeight) + 'px';
    };
    measure();
    window.addEventListener('resize', measure);
})();

(function () {
    const slides = Array.from(document.querySelectorAll('[data-slide]'));
    const dots = Array.from(document.querySelectorAll('[data-slide-dot]'));
    if (!slides.length) return;
    let current = 0, timer = null;
    const show = (i) => {
        current = (i + slides.length) % slides.length;
        slides.forEach((s, k) => {
            s.classList.toggle('opacity-100', k === current);
            s.classList.toggle('opacity-0', k !== current);
            s.classList.toggle('pointer-events-none', k !== current);
        });
        dots.forEach((d, k) => {
            d.classList.toggle('bg-gold', k === current);
            d.classList.toggle('bg-cream/50', k !== current);
            d.setAttribute('aria-current', k === current ? 'true' : 'false');
        });
        replayAnimations(slides[current]);
    };
    const next = () => show(current + 1);
    const play = () => { stop(); timer = setInterval(next, 6000); };
    const stop = () => { if (timer) { clearInterval(timer); timer = null; } };
    dots.forEach((d, i) => d.addEventListener('click', () => { show(i); play(); }));
    const heroEl = slides[0].closest('[data-hero]');
    if (heroEl) {
        heroEl.addEventListener('mouseenter', stop);
        heroEl.addEventListener('mouseleave', play);
    }
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) play();
    show(0);
})();
</script>
@endpush

@section('content')
@php
    $heroTitle = $homeSection?->hero_title ?: "Logika diasah.\nKreativitas dikembangkan.\nMasa depan diciptakan.";
    $heroLines = explode("\n", $heroTitle);
    $heroSubtitle = $homeSection?->hero_subtitle ?: 'Dunia digital terus berubah, membawa tantangan dan peluang baru di setiap langkahnya. Di Program Studi Ilmu Komputer, mahasiswa belajar membangun solusi teknologi yang berguna bagi masyarakat.';
@endphp

{{-- Hero Spacer + Slider --}}
<div class="h-[560px] md:h-[700px] lg:h-[631px] 2xl:h-[846px]" aria-hidden="true"></div>
<section class="fixed left-0 right-0 top-0 z-0 h-[560px] overflow-hidden md:h-[700px] lg:h-[631px] 2xl:h-[846px]" data-hero aria-roledescription="carousel" aria-label="Sorotan program studi">
    @foreach($heroSlides as $index => $slide)
    <div class="hero-slide absolute inset-0 z-0 @if($index > 0) opacity-0 pointer-events-none @else opacity-100 @endif transition-opacity duration-700" data-slide role="group" aria-label="Slide {{ $index + 1 }}">
        <img src="{{ $slide['url'] }}" alt="{{ $slide['alt'] }}" class="absolute inset-0 z-0 h-full w-full object-cover">
        <div class="absolute inset-0 z-10 bg-gradient-to-r from-badge/85 via-badge/45 to-badge/10"></div>
        <div class="absolute inset-0 z-20 flex items-center mx-4 px-4 sm:mx-8 sm:px-8 lg:mx-16 lg:px-20">
            <div class="max-w-2xl text-cream">
                <h1 class="font-display text-2xl font-bold leading-tight sm:text-3xl md:text-5xl mb-5">
                    @foreach($heroLines as $i => $line)
                    <span class="block anim-fade-up anim-delay-{{ $i + 1 }}">{{ $line }}</span>
                    @endforeach
                </h1>
                <p class="anim-fade-up anim-delay-{{ count($heroLines) + 1 }} mb-8 max-w-xl text-base text-cream/85">{{ $heroSubtitle }}</p>
                <a href="{{ $heroCtaUrl }}" class="anim-zoom anim-delay-{{ count($heroLines) + 2 }} btn btn-primary btn-lg">
                    <span class="btn-label">Jelajahi Prodi</span>
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </div>
    @endforeach

    @if(count($heroSlides) > 1)
    <div class="anim-fade anim-delay-6 absolute bottom-5 left-1/2 z-30 flex -translate-x-1/2 items-center gap-2.5">
        @foreach($heroSlides as $index => $slide)
        <button type="button" class="hero-dot h-2.5 w-2.5 rounded-full @if($index === 0) bg-gold @else bg-cream/50 @endif transition-colors" data-slide-dot aria-label="Ke slide {{ $index + 1 }}" @if($index === 0) aria-current="true" @else aria-current="false" @endif></button>
        @endforeach
    </div>
    @endif
</section>

{{-- Sambutan Kaprodi --}}
<section class="relative z-10 bg-line py-16 lg:py-24">
    <div class="mx-4 px-4 sm:mx-8 sm:px-8 lg:mx-16 lg:px-20">
        <div class="mb-12 text-center lg:mb-16" data-reveal>
            <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold anim-fade-up anim-delay-1">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                Selamat datang di {{ $site?->site_name ?: 'Ilmu Komputer' }}
            </h3>
            <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">
                {{ $site?->university_name ?: 'Universitas PGRI Wiranegara' }}
            </h1>
        </div>

        <div class="mx-auto grid max-w-3xl items-center gap-12 md:grid-cols-[minmax(0,auto)_minmax(0,1fr)] md:gap-14 lg:max-w-4xl">
            <div class="justify-self-center">
                <img src="https://placehold.co/600x600/132845/FDC72F?text=FOTO+Kaprodi&font=roboto" alt="Foto Ketua Program Studi" class="h-72 w-72 object-cover sm:h-80 sm:w-80 lg:h-96 lg:w-96" loading="lazy">
            </div>
            <div data-reveal class="anim-fade-up anim-delay-1">
                <h2 class="mb-5 font-display text-2xl font-bold leading-tight text-primary sm:text-3xl">{{ $homeSection?->welcome_title ?: 'Sambutan' }}</h2>
                <div class="max-w-2xl space-y-4 text-[0.95rem] leading-relaxed text-ink/80">
                    @if($homeSection?->welcome_description)
                    <p>{{ $homeSection->welcome_description }}</p>
                    @else
                    <p>Assalamualaikum warahmatullahi wabarakatuh. Puji syukur kita panjatkan ke hadirat Allah SWT atas rahmat-Nya sehingga Program Studi Ilmu Komputer terus bertumbuh sebagai rumah bagi generasi yang ingin memecahkan masalah nyata lewat teknologi.</p>
                    <p>Kami membekali mahasiswa tidak hanya dengan keterampilan rekayasa perangkat lunak, data, dan kecerdasan buatan, tetapi juga integritas dan kemampuan berpikir kritis. Selamat bergabung, mari kita ciptakan masa depan bersama.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Mengapa Memilih --}}
@if(!empty($advantageSection['items']))
<section class="relative z-10 bg-primary pt-16 lg:pt-24">
    <div class="mx-4 px-4 pb-16 sm:mx-8 sm:px-8 lg:mx-16 lg:px-20 lg:pb-24">
        <div class="text-center" data-reveal>
            <h1 class="inline-block font-display text-3xl font-bold uppercase tracking-wide text-cream sm:text-4xl lg:text-5xl anim-fade-up anim-delay-1">{{ $advantageSection['heading'] }}</h1>
            <div class="my-20 flex justify-center anim-fade-up anim-delay-2">
                <span class="h-1 w-24 rounded-full bg-gold" aria-hidden="true"></span>
            </div>
        </div>

        <div class="mx-auto grid max-w-6xl gap-x-16 gap-y-14 sm:grid-cols-2">
            @foreach($advantageSection['items'] as $item)
            <article class="grid grid-cols-2 items-start gap-x-5 gap-y-4 sm:grid-cols-[minmax(0,140px)_auto_minmax(0,1fr)] sm:gap-x-6 anim-fade-up anim-delay-{{ ($loop->index % 3) + 1 }}" data-reveal>
                <div class="relative aspect-square w-full overflow-hidden rounded-lg border border-white/10">
                    <img src="{{ $item['image_url'] }}" alt="{{ $item['image_alt'] }}" class="absolute inset-0 h-full w-full object-cover" loading="lazy">
                </div>
                <div class="font-display text-6xl font-light leading-[0.74] text-gold">{{ str_pad($item['order'], 2, '0', STR_PAD_LEFT) }}</div>
                <div class="col-span-2 sm:col-span-1 sm:col-start-3">
                    <h3 class="font-sans text-xl font-bold leading-snug text-cream">{{ $item['title'] }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-cream/75">{{ $item['description'] }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Marquee --}}
<section class="relative z-10 overflow-hidden bg-primary py-3 select-none" aria-hidden="true">
    <div class="marquee-track flex w-max items-center">
        <div class="flex shrink-0 items-center gap-10 pr-10">
            <span class="font-sans text-4xl font-bold uppercase tracking-wide leading-none text-cream sm:text-5xl lg:text-7xl">Ilmu Komputer</span>
            <img src="{{ asset('assets/images/logone.png') }}" alt="" class="h-10 sm:h-12 lg:h-16">
            <span class="font-sans text-4xl font-bold uppercase tracking-wide leading-none text-gold sm:text-5xl lg:text-7xl">Uniwara</span>
            <img src="{{ asset('assets/images/logone.png') }}" alt="" class="h-10 sm:h-12 lg:h-16">
            <span class="font-sans text-4xl font-bold uppercase tracking-wide leading-none text-cream sm:text-5xl lg:text-7xl">Compscience</span>
            <img src="{{ asset('assets/images/logone.png') }}" alt="" class="h-10 sm:h-12 lg:h-16">
        </div>
        <div class="flex shrink-0 items-center gap-10 pr-10">
            <span class="font-sans text-4xl font-bold uppercase tracking-wide leading-none text-cream sm:text-5xl lg:text-7xl">Ilmu Komputer</span>
            <img src="{{ asset('assets/images/logone.png') }}" alt="" class="h-10 sm:h-12 lg:h-16">
            <span class="font-sans text-4xl font-bold uppercase tracking-wide leading-none text-gold sm:text-5xl lg:text-7xl">Uniwara</span>
            <img src="{{ asset('assets/images/logone.png') }}" alt="" class="h-10 sm:h-12 lg:h-16">
            <span class="font-sans text-4xl font-bold uppercase tracking-wide leading-none text-cream sm:text-5xl lg:text-7xl">Compscience</span>
            <img src="{{ asset('assets/images/logone.png') }}" alt="" class="h-10 sm:h-12 lg:h-16">
        </div>
    </div>
</section>

{{-- Profil Prodi --}}
<section class="relative z-10 bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="mx-auto grid max-w-6xl items-center gap-12 md:grid-cols-[minmax(0,auto)_minmax(0,1fr)] md:gap-20">
            <div class="justify-self-center">
                <img src="{{ asset('assets/images/image.png') }}" alt="Profil {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="aspect-square w-full max-w-sm object-cover sm:max-w-md lg:max-w-lg" loading="lazy">
            </div>
            <div data-reveal class="anim-fade-up anim-delay-1">
                <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold anim-fade-up anim-delay-1">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/></svg>
                    Tentang {{ $site?->site_name ?: 'Ilmu Komputer' }}
                </h3>
                @if($programProfile?->description)
                <div class="mt-6 space-y-5 text-[1.02rem] leading-relaxed text-ink/80 anim-fade-up anim-delay-3">
                    <x-public.program-profile-field :value="$programProfile->description" />
                </div>
                @endif
                <a href="{{ route('profile') }}" class="btn btn-primary btn-lg mt-8 anim-fade-up anim-delay-4">
                    <span class="btn-label">Lihat Profil</span>
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Kegiatan --}}
@if(!empty($activities))
<section id="kegiatan" class="relative z-10 bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="anim-fade-up anim-delay-1" data-reveal>
            <div class="mb-12 flex flex-wrap items-end justify-between gap-6 lg:mb-16">
                <div class="text-left">
                    <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold anim-fade-up anim-delay-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9 6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008v-.008Zm2.25-2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Z"/></svg>
                        Kegiatan {{ $site?->university_name ?: 'Uniwara' }}
                    </h3>
                    <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">Kegiatan</h1>
                    <p class="mt-6 max-w-2xl text-[0.95rem] leading-relaxed text-ink/70 anim-fade-up anim-delay-3">Ragam kegiatan yang memperkaya pengalaman mahasiswa — dari seminar nasional hingga ajang prestasi tingkat nasional.</p>
                </div>
                <a href="{{ route('activities.index') }}" class="btn btn-primary btn-md mb-1 anim-fade-up anim-delay-3">
                    <span class="btn-label">Lihat Lebih Banyak</span>
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($activities as $activity)
            <x-activity-card :activity="$activity" />
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- Alumni --}}
@if(!empty($alumni))
<section class="relative z-10 bg-primary py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="anim-fade-up anim-delay-1" data-reveal>
            <div class="mb-12 flex flex-wrap items-end justify-between gap-6 lg:mb-16">
                <div class="text-left">
                    <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-gold text-base font-semibold anim-fade-up anim-delay-1">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>
                        Alumni {{ $site?->university_name ?: 'Uniwara' }}
                    </h3>
                    <h1 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-cream sm:text-4xl lg:text-5xl anim-fade-up anim-delay-2">Alumni</h1>
                    <p class="mt-6 max-w-2xl text-[0.95rem] leading-relaxed text-cream/70 anim-fade-up anim-delay-3">Jejak karier lulusan Ilmu Komputer yang membuktikan bahwa kompetensi yang dibangun di kampus berdampak nyata di dunia kerja.</p>
                </div>
                <a href="{{ route('alumni') }}" class="btn btn-primary btn-md mb-1 anim-fade-up anim-delay-3">
                    <span class="btn-label">Lihat Lebih Banyak</span>
                    <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                </a>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($alumni as $alumnus)
            <x-alumni-card :alumni="$alumnus" />
            @endforeach
        </div>
    </div>
</section>
@endif

<x-cta-banner />
@endsection
