@extends('layouts.public')

@section('title', 'Visi Misi - Program Studi Ilmu Komputer')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'profile',
        'kicker' => 'Profil',
        'title' => 'Visi dan Misi',
        'description' => 'Visi, misi, tujuan, dan sasaran Program Studi Ilmu Komputer.',
    ])

    <section class="profile-core-section core-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container core-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-start max-[1024px]:grid-cols-1">
            <article class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95]">
                <h2>Visi</h2>
                <div class="rich-text-content">
                    <blockquote><p>Menjadi program studi yang unggul dalam pengembangan ilmu komputer, teknologi informasi, dan kewirausahaan digital.</p></blockquote>
                </div>
            </article>
            <article class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95]">
                <h2>Misi</h2>
                <div class="rich-text-content">
                    <ul>
                        <li>Menyelenggarakan pendidikan ilmu komputer yang adaptif.</li>
                        <li>Mengembangkan penelitian dan pengabdian berbasis teknologi.</li>
                        <li>Mendorong kolaborasi, etika profesional, dan kewirausahaan digital.</li>
                    </ul>
                </div>
            </article>
        </div>
    </section>
@endsection
