@extends('layouts.public')

@section('content')
@php
    $profileImage = $programProfile->description_image
        ? asset('storage/'.$programProfile->description_image)
        : asset('assets/images/image.png');
    $historyImage = $programProfile->history_image
        ? asset('storage/'.$programProfile->history_image)
        : 'https://placehold.co/800x1000/0B1A2F/FDC72F?text=SEJARAH+PRODI&font=roboto';
    $goalsImage = $programProfile->goals_image
        ? asset('storage/'.$programProfile->goals_image)
        : 'https://placehold.co/800x1000/0B1A2F/FDC72F?text=TUJUAN+PEMBELAJARAN&font=roboto';
    $advantagesImage = $programProfile->advantages_image
        ? asset('storage/'.$programProfile->advantages_image)
        : 'https://placehold.co/800x1000/1B365D/FDC72F?text=KEUNGGULAN+PRODI&font=roboto';
@endphp

<x-hero title="Profil" :breadcrumbs="['Profil' => null]" image="{{ asset('assets/images/hero/hero-1.webp') }}">
    Kenali lebih dekat {{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }} — sejarah, visi, misi, akreditasi, dan keunggulan yang membentuk lulusan siap bersaing.
</x-hero>

{{-- Profil Prodi --}}
<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="mx-auto grid max-w-6xl items-center gap-12 md:grid-cols-[minmax(0,auto)_minmax(0,1fr)] md:gap-20">
            <div class="justify-self-center">
                <img src="{{ $profileImage }}" alt="Profil {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="aspect-square w-full max-w-sm object-cover sm:max-w-md lg:max-w-lg" loading="lazy" decoding="async" width="512" height="512">
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
            </div>
        </div>
    </div>
</section>

{{-- Sejarah --}}
@if($programProfile?->history)
<section class="bg-primary py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="grid max-w-6xl items-center gap-12 md:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)] md:gap-20">
            <div data-reveal class="anim-fade-up anim-delay-1">
                <div class="mt-6">
                    <x-public.program-profile-field class="rich-text-on-dark" :value="$programProfile->history" />
                </div>
            </div>
            <div class="justify-self-center md:justify-self-end">
                <img src="{{ $historyImage }}" alt="Perjalanan Program Studi Ilmu Komputer" class="aspect-square w-full max-w-sm object-cover sm:max-w-md lg:max-w-lg" loading="lazy" decoding="async" width="512" height="512">
            </div>
        </div>
    </div>
</section>
@endif

{{-- Visi & Misi --}}
<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="grid max-w-6xl items-start gap-12 md:grid-cols-2 md:gap-16">
            <div data-reveal class="anim-fade-up anim-delay-1">
                @if($programProfile?->vision)
                <div class="mt-6 font-display text-xl font-bold leading-snug text-primary sm:text-2xl">
                    <x-public.program-profile-field :value="$programProfile->vision" />
                </div>
                @endif
            </div>
            <div data-reveal class="anim-fade-up anim-delay-2">
                @if($programProfile?->mission)
                <div class="rich-text mt-6 space-y-7">
                    <x-public.program-profile-field :value="$programProfile->mission" />
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- Badge Akreditasi --}}
<section class="bg-gold text-primary py-12 sm:py-14 lg:py-16">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="flex flex-col items-start justify-between gap-10 lg:flex-row lg:items-center lg:gap-12">
            <div class="max-w-2xl" data-reveal>
                <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary/80 text-base font-bold">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                    Akreditasi · BAN-PT
                </h3>
                <h2 class="mt-4 font-display text-2xl font-bold uppercase tracking-wide sm:text-3xl lg:text-4xl">Badge Akreditasi</h2>
            </div>
            <div class="inline-flex shrink-0 items-center rounded-full bg-primary px-8 py-4 font-display text-2xl font-bold text-gold sm:text-3xl anim-fade-up anim-delay-2" data-reveal>
                {{ $programProfile?->accreditation ?: 'Baik Sekali' }}
            </div>
        </div>
    </div>
</section>

{{-- Arah / Tujuan Pembelajaran --}}
@if($programProfile?->goals)
<section class="bg-primary py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="grid max-w-6xl items-center gap-12 md:grid-cols-[minmax(0,1fr)_minmax(0,0.9fr)] md:gap-20">
            <div data-reveal>
                <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-gold text-base font-semibold">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z"/></svg>
                    Arah Pembelajaran
                </h3>
                <div class="mt-6">
                    <x-public.program-profile-field class="rich-text-on-dark" :value="$programProfile->goals" />
                </div>
            </div>
            <div class="justify-self-center md:justify-self-end">
                <img src="{{ $goalsImage }}" alt="Tujuan pembelajaran Ilmu Komputer" class="aspect-square w-full max-w-sm object-cover sm:max-w-md lg:max-w-lg" loading="lazy" decoding="async" width="512" height="512">
            </div>
        </div>
    </div>
</section>
@endif

{{-- Keunggulan --}}
@if($programProfile?->advantages)
<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="grid max-w-6xl items-center gap-12 md:grid-cols-[minmax(0,0.9fr)_minmax(0,1fr)] md:gap-20">
            <div class="justify-self-center md:justify-self-start">
                <img src="{{ $advantagesImage }}" alt="Keunggulan Program Studi Ilmu Komputer" class="aspect-square w-full max-w-sm object-cover sm:max-w-md lg:max-w-lg" loading="lazy" decoding="async" width="512" height="512">
            </div>
            <div data-reveal class="anim-fade-up anim-delay-1">
                <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/></svg>
                    Pembeda Program Studi
                </h3>
                <div class="mt-6 space-y-6 text-ink/80">
                    <x-public.program-profile-field :value="$programProfile->advantages" />
                </div>
                <div class="mt-9 flex flex-wrap gap-3.5">
                    <a href="{{ route('activities.index') }}" class="btn btn-primary btn-lg">
                        <span class="btn-label">Lihat Kegiatan Prodi</span>
                        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                    <a href="{{ route('documents') }}" class="btn btn-outline btn-lg">
                        <span class="btn-label">Dokumen Akademik</span>
                        <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

<x-cta-banner />
@endsection
