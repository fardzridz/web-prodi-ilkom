@extends('layouts.public')

@section('title', 'Aksesibilitas - Program Studi Ilmu Komputer')

@section('content')
    @include('partials.public.page-hero', [
        'active' => null,
        'kicker' => 'Informasi',
        'title' => 'Aksesibilitas',
        'description' => 'Komitmen kami terhadap aksesibilitas website bagi semua pengguna.',
    ])

    <section class="core-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
            @if (filled($page->sanitized_content))
                <article class="rich-text-content max-w-[820px]">
                    {!! $page->sanitized_content !!}
                </article>
            @else
                <div class="page-empty-content py-16 text-center">
                    <p class="m-0 text-lg text-grey-3">Konten sedang disiapkan. Silakan kunjungi kembali halaman ini nanti.</p>
                </div>
            @endif
        </div>
    </section>
@endsection
