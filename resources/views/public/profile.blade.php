@extends('layouts.public')

@section('title', 'Profil Program Studi Ilmu Komputer')
@section('description', 'Profil Program Studi Ilmu Komputer, misi, tujuan, kompetensi lulusan, dan program unggulan.')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js" defer></script>
@endpush

@section('content')
@include('partials.public.page-hero', [
'active' => 'profile',
'kicker' => 'Profil Program Studi',
'title' => 'Ilmu Komputer',
'description' => 'Mengenal identitas, misi, tujuan, kompetensi, dan program unggulan Program Studi Ilmu Komputer Universitas PGRI Wiranegara.',
])

<section class="profile-intro-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
    <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
        <div class="image-frame image-frame-large placeholder-campus relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
        <div class="copy-block [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-[-0.035em] max-[560px]:[&_h2]:text-[34px] [&>p]:max-w-[450px] [&>p]:my-6 [&>p]:text-grey-2 [&>p]:text-[17px] [&>p]:leading-[1.65] [&>p]:tracking-[-0.01em]">
            <x-public.program-profile-field
                :value="$programProfile->description"
                class="grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold"
                data-profile-field="description" />
        </div>
    </div>
</section>

<section class="profile-history-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
    <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
        <div class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
            <x-public.program-profile-field
                :value="$programProfile->history"
                class="grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold"
                data-profile-field="history" />
        </div>
        <div class="profile-visual-stack relative z-[1] grid gap-5 content-start">
            <div class="image-frame image-frame-large profile-history-visual relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
        </div>
    </div>
</section>

<section id="visi-misi-page" class="profile-core-section core-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
    <div class="container core-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-start max-[1024px]:grid-cols-1">
        <div class="profile-core-copy">
            <x-public.program-profile-field
                :value="$programProfile->vision"
                class="grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold"
                data-profile-field="vision" />
        </div>
        <div class="profile-rich-copy profile-mission-box relative z-[1] max-w-[760px] pt-2 self-center py-2 max-[560px]:py-1 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
            <x-public.program-profile-field
                :value="$programProfile->mission"
                class="grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold"
                data-profile-field="mission" />
        </div>
    </div>
</section>

<section class="profile-band scholarship-section relative overflow-hidden min-h-[252px] text-blue-dark bg-[rgba(253,185,19,0.9)] max-[560px]:min-h-[236px]">
    <div class="scholarship-media absolute inset-0 z-0" aria-hidden="true"></div>
    <div class="container scholarship-content profile-band-content w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[2] grid min-h-[252px] content-center py-[34px] max-[560px]:min-h-[236px] max-[560px]:py-8 grid-cols-[minmax(0,1fr)_minmax(280px,0.64fr)] gap-[clamp(24px,5vw,64px)] items-center max-[560px]:grid-cols-1 max-[560px]:gap-4 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>p]:max-w-[590px] [&>p]:mt-3.5 [&>p]:mb-0 [&>p]:text-[rgba(0,36,58,0.84)] [&>p]:text-[17px]">
        <h2>Badge Akreditasi</h2>
        <div class="profile-band-accreditation max-w-[520px] text-blue-dark">
            <div class="rich-text-content profile-band-rich grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 gap-2 mt-2.5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="accreditation">
                <p><strong>{{ $programProfile->accreditation }}</strong></p>
            </div>
        </div>
    </div>
</section>

<section class="profile-goals-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
    <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
        <div class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
            <x-public.program-profile-field
                :value="$programProfile->goals"
                class="grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold"
                data-profile-field="goals" />
        </div>
        <div class="profile-visual-stack relative z-[1] grid gap-5 content-start">
            <div class="image-frame image-frame-large profile-goals-visual relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
        </div>
    </div>
</section>

<section class="profile-advantages-section profile-focus-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
    <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
        <div class="profile-visual-stack relative z-[1] grid gap-5 content-start">
            <div class="image-frame image-frame-large profile-advantages-visual relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
        </div>
        <div class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
            <x-public.program-profile-field
                :value="$programProfile->advantages"
                class="grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold"
                data-profile-field="advantages" />
            <div class="profile-section-cta flex flex-wrap gap-3.5 mt-7 max-[560px]:grid max-[560px]:justify-items-start">
                <a class="button button-red inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase bg-red" href="{{ route('activities.index') }}">Lihat Kegiatan Prodi</a>
                <a class="button button-light-outline inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase border-2 border-current bg-transparent !bg-transparent" href="{{ route('documents') }}">Dokumen Akademik</a>
            </div>
        </div>
    </div>
</section>

@include('partials.public.contact-cta', [
'title' => 'Ingin mengenal prodi lebih lanjut?',
'secondaryLabel' => 'Lihat Kegiatan',
'secondaryHref' => route('activities.index'),
])
@endsection