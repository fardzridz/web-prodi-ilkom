@extends('layouts.public')

@section('title', 'Beranda - Program Studi Ilmu Komputer')
@section('description', 'Beranda Program Studi Ilmu Komputer Universitas PGRI Wiranegara.')

@section('content')
<section class="hero-section relative h-[630px] min-h-0 overflow-hidden text-white bg-blue-deep max-[1024px]:h-[564px]">
    <div class="hero-media absolute inset-0 overflow-hidden bg-blue-deep bg-cover bg-center" aria-hidden="true">
        @foreach ($heroSlides as $slide)
        <span
            class="hero-slide {{ $slide['delay_class'] }} absolute inset-0 opacity-0 bg-center bg-cover backface-hidden will-change-[opacity,transform] scale-[1.04]"
            style="background-image: url('{{ $slide['url'] }}');"></span>
        @endforeach
    </div>
    <div class="hero-dots absolute inset-0 z-[1] opacity-[0.38]" aria-hidden="true"></div>
    <div class="hero-stripes absolute inset-y-0 right-[-4px] left-auto z-[1] w-24" aria-hidden="true"></div>
    <div class="container hero-content w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[2] h-full pt-[120px] pb-[88px] max-[1024px]:pt-16 max-[1024px]:pb-16">
        <h1 class="max-w-[800px] mt-0 mr-0 mb-4 ml-0 font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">{!! nl2br(e($homeSection->hero_title)) !!}</h1>
        <p class="max-w-[500px] mt-4 mb-0 text-[17.6px] font-light leading-[1.6] tracking-[-0.01em] max-[1024px]:pr-9 max-[1024px]:text-[17px]">{{ $homeSection->hero_subtitle }}</p>
        <div class="hero-actions flex flex-wrap items-center gap-y-7 gap-x-14 mt-9 max-[560px]:grid max-[560px]:justify-items-start max-[560px]:gap-[18px]">
            <a class="button button-primary inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase bg-blue-mid" href="{{ $heroCtaUrl }}">{{ $homeSection->cta_text }}</a>
        </div>
    </div>
</section>

@include('partials.public.floating-nav', ['active' => 'home'])

<section id="profil-section" class="intro-section section-space py-20 max-[560px]:py-16">
    <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
        <div class="image-frame image-frame-large placeholder-campus relative min-h-[520px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] max-[1024px]:min-h-[360px]"></div>
        <div class="copy-block [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-[-0.035em] max-[560px]:[&_h2]:text-[34px] [&>p]:max-w-[450px] [&>p]:my-6 [&>p]:text-grey-2 [&>p]:text-[17px] [&>p]:leading-[1.65] [&>p]:tracking-[-0.01em]">
            <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Profil Prodi</p>
            <h2>{{ $homeSection->welcome_title }}</h2>
            <p>{{ $homeSection->welcome_description }}</p>
            <a class="button button-blue inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase bg-blue-mid" href="{{ route('profile') }}">Lihat Profil</a>
        </div>
    </div>
</section>

<section id="keunggulan-section" class="why-section section-space bg-[#f8f9fa] py-[92px] pb-24 max-[560px]:py-16">
    <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
        <h2 class="section-title m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-[-0.035em] max-[560px]:text-[34px] text-center">Mengapa Memilih Ilmu Komputer?</h2>
        <div class="why-list grid grid-cols-2 gap-y-[84px] gap-x-[74px] max-w-[1120px] mt-[78px] mx-auto max-[1024px]:grid-cols-1 max-[1024px]:gap-[58px] max-[1024px]:max-w-[760px]">
            @foreach ([
            ['title' => 'Arah Karier Luas', 'copy' => 'Lulusan disiapkan untuk berkiprah sebagai software developer, network specialist, big data specialist, game specialist, mobile developer, researcher, hingga startup entrepreneur.', 'class' => 'placeholder-classroom'],
            ['title' => 'Peminatan yang Jelas', 'copy' => 'Mulai semester 5 mahasiswa dapat memperdalam bidang KBJ, KCV, RPL, atau MGM sesuai minat dan rencana profesi.', 'class' => 'placeholder-community'],
            ['title' => 'Pembelajaran Berbasis Proyek', 'copy' => 'Mahasiswa diarahkan membangun portofolio nyata melalui praktikum, proyek aplikasi, dan kegiatan kolaboratif.', 'class' => 'placeholder-career'],
            ['title' => 'Koneksi Akademik dan Industri', 'copy' => 'Kegiatan prodi menghubungkan perkuliahan dengan sertifikasi, magang, PKL, dan pengabdian masyarakat.', 'class' => 'placeholder-aid'],
            ] as $index => $item)
            <article class="why-card relative grid grid-cols-[150px_46px_minmax(0,1fr)] gap-[22px] items-start min-h-[230px] pt-[18px] isolate max-[560px]:grid-cols-[72px_minmax(0,1fr)] max-[560px]:gap-4 max-[560px]:min-h-0 max-[560px]:p-0 max-[560px]:[&_.why-copy]:col-span-2">
                <div class="image-frame why-card-image {{ $item['class'] }} relative w-[150px] h-[150px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] max-[560px]:w-[72px] max-[560px]:h-[72px]"></div>
                <div class="why-number text-red font-display text-[64px] font-thin leading-[0.74] tracking-normal max-[560px]:text-[56px]">{{ $index + 1 }}</div>
                <div class="why-copy [&_h3]:max-w-[330px] [&_h3]:m-0 [&_h3]:text-blue-dark [&_h3]:font-body [&_h3]:text-[clamp(22px,2.2vw,30px)] max-[560px]:[&_h3]:text-2xl [&_h3]:font-bold [&_h3]:leading-[0.98] [&_h3]:tracking-[-0.02em] [&_p]:max-w-[360px] [&_p]:mt-5 [&_p]:mb-0 [&_p]:text-grey-2 [&_p]:text-[15px] [&_p]:leading-normal">
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['copy'] }}</p>
                </div>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section id="kurikulum-section" class="scholarship-section relative overflow-hidden min-h-[252px] text-blue-dark bg-[rgba(253,185,19,0.9)] max-[560px]:min-h-[236px]">
    <div class="scholarship-media absolute inset-0 z-0" aria-hidden="true"></div>
    <div class="container scholarship-content w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[2] grid min-h-[252px] content-center py-[34px] max-[560px]:min-h-[236px] max-[560px]:py-8 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>p]:max-w-[590px] [&>p]:mt-3.5 [&>p]:mb-0 [&>p]:text-[rgba(0,36,58,0.84)] [&>p]:text-[17px]">
        <h2>Kurikulum Berbasis Kompetensi</h2>
        <p>Rangkaian perkuliahan disusun untuk membangun fondasi komputasi, kemampuan rekayasa, peminatan, dan pengalaman lapangan.</p>
    </div>
</section>

<section id="kegiatan-section" class="visit-section section-space py-20 max-[560px]:py-16">
    <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
        <h2 class="section-title m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-[-0.035em] max-[560px]:text-[34px]">Kegiatan Prodi</h2>
        <div class="visit-grid grid grid-cols-3 gap-[34px] mt-[42px] max-[1024px]:grid-cols-1">
            @foreach ($activities as $activity)
            <article class="visit-card h-full [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:line-clamp-2 [&_h3]:min-h-[2.1em] [&_h3]:overflow-hidden [&_h3]:text-blue-dark [&_h3]:font-display [&_h3]:text-[34px] [&_h3]:font-medium [&_h3]:leading-[1.05] [&_h3]:tracking-[-0.035em] [&_p]:text-grey-2">
                <a href="{{ route('activities.show', $activity['slug']) }}" class="flex h-full flex-col">
                    <div class="image-frame placeholder-visit relative min-h-[250px] overflow-hidden bg-center bg-cover" style="background-image: linear-gradient(rgba(0, 36, 58, 0.12), rgba(0, 36, 58, 0.12)), url('{{ asset($activity['image']) }}');"></div>
                    <h3>{{ $activity['title'] }}</h3>
                    <p>{{ $activity['excerpt'] }}</p>
                    <div class="visit-meta grid gap-[7px] mt-4 text-grey-2 text-[13px] leading-[1.35]" aria-label="Detail kegiatan">
                        <span><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ $activity['date_label'] }}</span>
                        <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $activity['location'] }}</span>
                        <span><i class="fa-solid fa-tag" aria-hidden="true"></i> {{ $activity['category'] }}</span>
                    </div>
                </a>
            </article>
            @endforeach
        </div>
    </div>
</section>

<section id="alumni-section" class="explore-section home-alumni-section section-space bg-white py-20 max-[560px]:py-16">
    <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
        <h2 class="section-title m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-[-0.035em] max-[560px]:text-[34px]">Alumni Ilmu Komputer</h2>
        <div class="home-alumni-grid grid grid-cols-4 gap-[18px] mt-6 max-[1024px]:grid-cols-2 max-[560px]:grid-cols-1 max-[560px]:gap-3.5">
            @foreach ($alumni as $graduate)
            <a class="home-alumni-card grid grid-cols-[82px_minmax(0,1fr)] gap-[18px] items-center min-h-[138px] p-[18px] bg-[#f0f2f4] max-[560px]:min-h-[124px] max-[560px]:p-4" href="{{ route('alumni') }}">
                <span class="home-alumni-photo block w-[82px] h-[82px] overflow-hidden bg-center bg-cover" style="background-image: linear-gradient(rgba(0, 36, 58, 0.08), rgba(0, 36, 58, 0.08)), url('{{ asset($graduate['image']) }}');" aria-hidden="true"></span>
                <span class="home-alumni-body grid gap-[7px] min-w-0">
                    <strong>{{ $graduate['name'] }}</strong>
                    <span>{{ $graduate['role'] }}.</span>
                </span>
            </a>
            @endforeach
        </div>
    </div>
</section>

@include('partials.public.contact-cta', ['id' => 'kontak-section'])
@endsection