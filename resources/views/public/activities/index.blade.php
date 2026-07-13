@extends('layouts.public')

@section('title', 'Kegiatan Program Studi Ilmu Komputer')
@section('description', 'Informasi seminar, workshop, kuliah tamu, pelatihan, sertifikasi, PKL, kunjungan industri, dan agenda kolaborasi Program Studi Ilmu Komputer.')

@section('content')
    @php
        $activityFilters = collect($activities)->pluck('category')->unique()->values();
    @endphp

    @include('partials.public.page-hero', [
        'active' => 'activities',
        'variant' => 'page-hero-activities',
        'kicker' => 'Kegiatan Program Studi',
        'title' => 'Agenda Ilmu Komputer',
        'description' => 'Informasi seminar, workshop, kuliah tamu, pelatihan, sertifikasi, PKL, kunjungan industri, dan agenda kolaborasi yang mendukung kompetensi mahasiswa Ilmu Komputer.',
    ])

    <section class="activities-list-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
            <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Daftar Kegiatan</p>
            <h2 class="internal-heading m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">Kegiatan Prodi Ilmu Komputer</h2>
            <div class="filter-row flex flex-wrap gap-3 mt-7" aria-label="Kategori kegiatan">
                <button class="filter-pill is-active inline-flex items-center min-h-10 px-[18px] text-blue-dark text-[13px] font-bold bg-white border border-[rgba(0,36,58,0.12)] cursor-pointer transition-colors duration-[180ms] ease-[ease] focus-visible:[outline:3px_solid_rgba(253,185,19,0.72)] focus-visible:outline-offset-[3px]" type="button" data-filter-target="#kegiatan-grid" data-filter-category="*" aria-pressed="true">Semua</button>
                @foreach ($activityFilters as $filter)
                    <button class="filter-pill inline-flex items-center min-h-10 px-[18px] text-blue-dark text-[13px] font-bold bg-white border border-[rgba(0,36,58,0.12)] cursor-pointer transition-colors duration-[180ms] ease-[ease] focus-visible:[outline:3px_solid_rgba(253,185,19,0.72)] focus-visible:outline-offset-[3px]" type="button" data-filter-target="#kegiatan-grid" data-filter-category="{{ $filter }}" aria-pressed="false">{{ $filter }}</button>
                @endforeach
            </div>

            <div id="kegiatan-grid" class="visit-grid activities-page-grid grid grid-cols-3 gap-[34px] max-[1024px]:grid-cols-1 mt-9">
                @foreach ($activities as $activity)
                    <article class="visit-card activities-page-card h-full load-more-item{{ $loop->iteration > 6 ? ' is-hidden' : '' }} [&_h3]:mt-6 [&_h3]:mb-2 [&_h3]:text-blue-dark [&_h3]:font-display [&_h3]:text-[34px] [&_h3]:font-medium [&_h3]:leading-none [&_h3]:tracking-[-0.035em] [&_p]:text-grey-2" data-activity-date="{{ $activity['date'] }}" data-activity-location="{{ $activity['location'] }}" data-activity-category="{{ $activity['category'] }}" data-activity-slug="{{ $activity['slug'] }}" data-activity-image="{{ asset($activity['image']) }}">
                        <a href="{{ route('activities.show', $activity['slug']) }}" class="group flex h-full flex-col focus-visible:[outline:3px_solid_rgba(253,185,19,0.72)] focus-visible:outline-offset-[5px]" aria-label="Baca detail {{ $activity['title'] }}">
                            <div class="image-frame {{ $activity['image_class'] }} relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)]" aria-hidden="true"></div>
                            <h3>{{ $activity['title'] }}</h3>
                            <p>{{ $activity['excerpt'] }}</p>
                            <div class="visit-meta grid gap-[7px] mt-4 text-grey-2 text-[13px] leading-[1.35]" aria-label="Detail kegiatan">
                                <time datetime="{{ $activity['date'] }}"><i class="fa-regular fa-calendar" aria-hidden="true"></i> {{ $activity['date_label'] }}</time>
                                <span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> {{ $activity['location'] }}</span>
                                <span><i class="fa-solid fa-tag" aria-hidden="true"></i> {{ $activity['category'] }}</span>
                            </div>
                            <span class="activity-detail-link inline-flex items-center gap-2 mt-[18px] text-red text-[13px] font-extrabold uppercase tracking-[0.04em] [&_i]:w-auto [&_i]:text-[12px] [&_i]:transition-transform [&_i]:duration-[180ms] [&_i]:ease-[ease]">Baca Detail <i class="fa-solid fa-arrow-right group-hover:translate-x-[3px] group-focus-visible:translate-x-[3px]" aria-hidden="true"></i></span>
                        </a>
                    </article>
                @endforeach
            </div>

            <div class="flex justify-center mt-[42px]">
                <button class="button button-blue load-more-button inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase bg-blue-mid" type="button" data-load-more-target="#kegiatan-grid" data-load-more-initial="6" data-load-more-step="3">
                    Muat Lebih Banyak
                </button>
            </div>
        </div>
    </section>

    @include('partials.public.contact-cta', [
        'title' => 'Punya agenda untuk dipublikasikan?',
        'primaryLabel' => 'Kirim Informasi',
        'secondaryLabel' => 'Lihat Profil',
        'secondaryHref' => route('profile'),
    ])
@endsection
