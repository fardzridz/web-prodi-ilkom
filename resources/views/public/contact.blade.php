@extends('layouts.public')

@section('title', 'Kontak - Program Studi Ilmu Komputer')
@section('description', 'Informasi alamat, email, telepon, media sosial, dan formulir kontak Program Studi Ilmu Komputer.')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'home',
        'kicker' => 'Kontak',
        'title' => 'Kontak Program Studi',
        'description' => 'Informasi alamat, email, telepon, media sosial, dan lokasi Program Studi Ilmu Komputer.',
    ])

    <section class="contact-page-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
        <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] gap-[clamp(34px,6vw,72px)] items-start max-[1024px]:grid-cols-1">
            <div class="contact-page-info min-w-0">
                <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Hubungi Kami</p>
                <h2 class="m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-[-0.035em] max-[560px]:text-[34px]">Kami Siap Membantu</h2>
                <p class="mt-6 mb-0 max-w-[480px] text-grey-2 text-[17px] leading-[1.65] tracking-[-0.01em]">
                    Silakan sampaikan pertanyaan, saran, atau keperluan administrasi melalui formulir di samping.
                    Tim kami akan merespons melalui surel sesegera mungkin.
                </p>

                <div class="contact-page-details grid gap-5 mt-9">
                    @if ($contactInfo->address)
                        <div class="flex items-start gap-4">
                            <span class="flex w-11 h-11 items-center justify-center flex-none text-blue-mid bg-[#f0f2f4]"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                            <div class="text-grey-2 text-[15px] leading-[1.6]">{!! nl2br(e($contactInfo->address)) !!}</div>
                        </div>
                    @endif
                    @if ($contactInfo->email)
                        <div class="flex items-start gap-4">
                            <span class="flex w-11 h-11 items-center justify-center flex-none text-blue-mid bg-[#f0f2f4]"><i class="fa-solid fa-envelope" aria-hidden="true"></i></span>
                            <a class="text-blue-dark text-[15px] font-semibold underline underline-offset-[3px] hover:text-red" href="mailto:{{ $contactInfo->email }}">{{ $contactInfo->email }}</a>
                        </div>
                    @endif
                    @if ($contactInfo->phone)
                        <div class="flex items-start gap-4">
                            <span class="flex w-11 h-11 items-center justify-center flex-none text-blue-mid bg-[#f0f2f4]"><i class="fa-solid fa-phone" aria-hidden="true"></i></span>
                            <a class="text-blue-dark text-[15px] font-semibold underline underline-offset-[3px] hover:text-red" href="tel:+{{ '62'.ltrim(preg_replace('/\D/', '', $contactInfo->phone), '0') }}">{{ $contactInfo->phone }}</a>
                        </div>
                    @endif
                    @if ($contactInfo->instagram || $contactInfo->youtube)
                        <div class="flex items-start gap-4">
                            <span class="flex w-11 h-11 items-center justify-center flex-none text-blue-mid bg-[#f0f2f4]"><i class="fa-solid fa-share-nodes" aria-hidden="true"></i></span>
                            <div class="flex flex-wrap gap-4">
                                @if ($contactInfo->instagram)
                                    <a class="text-blue-dark text-[15px] font-semibold underline underline-offset-[3px] hover:text-red" href="{{ $contactInfo->instagram }}" target="_blank" rel="noopener"><i class="fa-brands fa-instagram" aria-hidden="true"></i> Instagram</a>
                                @endif
                                @if ($contactInfo->youtube)
                                    <a class="text-blue-dark text-[15px] font-semibold underline underline-offset-[3px] hover:text-red" href="{{ $contactInfo->youtube }}" target="_blank" rel="noopener"><i class="fa-brands fa-youtube" aria-hidden="true"></i> YouTube</a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <form class="contact-form grid gap-5 p-[clamp(24px,3vw,38px)] bg-[#f8f9fa]" method="POST" action="{{ route('contact.store') }}">
                @csrf

                @if (session('success'))
                    <div class="flex items-start gap-3 p-4 text-[15px] font-semibold text-blue-dark bg-[rgba(253,185,19,0.28)] border border-yellow" role="alert">
                        <i class="fa-solid fa-circle-check mt-0.5" aria-hidden="true"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="grid gap-1.5">
                    <label class="text-blue-dark text-[13px] font-bold uppercase tracking-[0.04em]" for="contact-name">Nama <span aria-hidden="true">*</span></label>
                    <input
                        class="min-h-[46px] px-4 text-[15px] text-grey-1 bg-white border border-[rgba(0,36,58,0.18)] focus:border-yellow focus:outline-none focus:ring-2 focus:ring-yellow/60 {{ $errors->has('name') ? 'border-red' : '' }}"
                        id="contact-name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        maxlength="255"
                        required
                        autocomplete="name"
                    >
                    @error('name')<small class="text-red text-[13px] font-semibold">{{ $message }}</small>@enderror
                </div>

                <div class="grid gap-1.5">
                    <label class="text-blue-dark text-[13px] font-bold uppercase tracking-[0.04em]" for="contact-email">Surel <span aria-hidden="true">*</span></label>
                    <input
                        class="min-h-[46px] px-4 text-[15px] text-grey-1 bg-white border border-[rgba(0,36,58,0.18)] focus:border-yellow focus:outline-none focus:ring-2 focus:ring-yellow/60 {{ $errors->has('email') ? 'border-red' : '' }}"
                        id="contact-email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        maxlength="255"
                        required
                        autocomplete="email"
                    >
                    @error('email')<small class="text-red text-[13px] font-semibold">{{ $message }}</small>@enderror
                </div>

                <div class="grid gap-1.5">
                    <label class="text-blue-dark text-[13px] font-bold uppercase tracking-[0.04em]" for="contact-subject">Subjek <span aria-hidden="true">*</span></label>
                    <input
                        class="min-h-[46px] px-4 text-[15px] text-grey-1 bg-white border border-[rgba(0,36,58,0.18)] focus:border-yellow focus:outline-none focus:ring-2 focus:ring-yellow/60 {{ $errors->has('subject') ? 'border-red' : '' }}"
                        id="contact-subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject') }}"
                        maxlength="255"
                        required
                    >
                    @error('subject')<small class="text-red text-[13px] font-semibold">{{ $message }}</small>@enderror
                </div>

                <div class="grid gap-1.5">
                    <label class="text-blue-dark text-[13px] font-bold uppercase tracking-[0.04em]" for="contact-message">Pesan <span aria-hidden="true">*</span></label>
                    <textarea
                        class="min-h-[150px] p-4 text-[15px] text-grey-1 bg-white border border-[rgba(0,36,58,0.18)] focus:border-yellow focus:outline-none focus:ring-2 focus:ring-yellow/60 {{ $errors->has('message') ? 'border-red' : '' }}"
                        id="contact-message"
                        name="message"
                        rows="6"
                        maxlength="5000"
                        required
                    >{{ old('message') }}</textarea>
                    @error('message')<small class="text-red text-[13px] font-semibold">{{ $message }}</small>@enderror
                </div>

                <button class="inline-flex min-h-[54px] items-center justify-center px-9 text-white text-[15px] font-bold uppercase tracking-[0.03em] bg-blue-mid hover:bg-blue-dark transition-colors duration-[180ms] ease-[ease]" type="submit">
                    <i class="fa-solid fa-paper-plane mr-3" aria-hidden="true"></i>
                    Kirim Pesan
                </button>
            </form>
        </div>
    </section>

    @include('partials.public.contact-cta')
@endsection
