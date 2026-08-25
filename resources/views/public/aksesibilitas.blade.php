@extends('layouts.public')

@section('content')
<x-hero title="{{ $page?->title ?: 'Aksesibilitas' }}" :breadcrumbs="['Aksesibilitas' => null]" />

<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-3xl px-4 sm:px-8">
        <div class="rich-text">
            @if($page?->sanitized_content)
                {!! $page->sanitized_content !!}
            @else
            <h2>Pernyataan Aksesibilitas</h2>
            <p>{{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }} berkomitmen untuk menyediakan situs web yang dapat diakses oleh semua pengguna, termasuk penyandang disabilitas.</p>
            <h3>Standar yang Digunakan</h3>
            <p>Kami berupaya mematuhi pedoman WCAG 2.1 Level AA untuk memastikan aksesibilitas konten web.</p>
            <h3>Umpan Balik</h3>
            <p>Jika Anda mengalami kesulitan mengakses konten di situs ini, silakan hubungi kami melalui halaman kontak.</p>
            @endif
        </div>
    </div>
</section>

<x-cta-banner />
@endsection
