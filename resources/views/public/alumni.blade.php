@extends('layouts.public')

@section('title', 'Alumni Program Studi Ilmu Komputer')
@section('description', 'Alumni Program Studi Ilmu Komputer, bidang kerja lulusan, dan jejaring alumni.')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'alumni',
        'variant' => 'page-hero-alumni',
        'kicker' => 'Alumni Program Studi',
        'title' => 'Jejaring Alumni Ilmu Komputer',
        'description' => 'Bidang kerja lulusan, testimoni, dan ruang kolaborasi alumni Program Studi Ilmu Komputer.',
    ])

    <section class="alumni-intro-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container internal-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] gap-[clamp(34px,6vw,72px)] items-start max-[1024px]:grid-cols-1">
            <div class="content-panel p-[clamp(28px,4vw,46px)] [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-normal [&_p]:text-grey-2 [&_p]:leading-[1.65]">
                <h2>Lulusan yang Terhubung</h2>
                <p class="lead-copy mt-[22px] mb-0 text-[17px] font-light">Alumni Ilmu Komputer dapat berperan sebagai Startup Entrepreneur, Researcher, Software Developer, Intelligent System Specialist, Network Specialist, Big Data Specialist, Game Specialist, dan Mobile Application Specialist.</p>
                <p>Halaman ini disiapkan untuk menampilkan profil alumni, cerita karier, tracer study, dan jejaring kolaborasi dengan mahasiswa aktif.</p>
            </div>
        </div>
    </section>

    <section class="alumni-list-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
        <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
            <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Data Alumni</p>
            <h2 class="internal-heading m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">Profil Alumni</h2>
            <div class="alumni-grid grid grid-cols-3 gap-6 mt-9 max-[1024px]:grid-cols-1">
                @foreach ($alumni as $graduate)
                    <article class="alumni-card grid gap-5 min-h-[330px] p-7 bg-[#f0f2f4] [&_h3]:m-0 [&_h3]:text-blue-dark [&_h3]:font-body [&_h3]:text-xl [&_h3]:font-bold [&_h3]:leading-[1.15] [&_p]:m-0 [&_p]:text-grey-2 [&_p]:leading-[1.6]" data-alumni-status="active" data-alumni-name="{{ $graduate['name'] }}" data-alumni-batch-year="{{ $graduate['batch_year'] }}" data-alumni-graduation-year="{{ $graduate['graduation_year'] }}" data-alumni-job-position="{{ $graduate['job_position'] }}" data-alumni-company="{{ $graduate['company'] }}" data-alumni-photo="{{ asset($graduate['image']) }}">
                        <div class="alumni-avatar w-[74px] h-[74px] overflow-hidden bg-[linear-gradient(135deg,var(--blue-mid),var(--yellow))] bg-center bg-cover" role="img" aria-label="Foto {{ $graduate['name'] }}" style="background-image: linear-gradient(rgba(0, 36, 58, 0.08), rgba(0, 36, 58, 0.08)), url('{{ asset($graduate['image']) }}');"></div>
                        <div>
                            <span class="alumni-role inline-block text-red text-xs font-bold tracking-[0.06em] uppercase">Angkatan {{ $graduate['batch_year'] }} &middot; Lulus {{ $graduate['graduation_year'] }}</span>
                            <h3>{{ $graduate['name'] }}</h3>
                        </div>
                        <p>{{ $graduate['quote'] }}</p>
                        <div class="alumni-meta grid gap-1.5 text-grey-2 text-[13px]">
                            <span><i class="{{ $graduate['icon'] }}" aria-hidden="true"></i> {{ $graduate['job_position'] }}</span>
                            <span><i class="fa-solid fa-building" aria-hidden="true"></i> {{ $graduate['company'] }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.public.contact-cta', [
        'title' => 'Alumni ingin memperbarui data?',
        'primaryLabel' => 'Kirim Data',
        'secondaryLabel' => 'Lihat Dokumen',
        'secondaryHref' => route('documents'),
    ])
@endsection
