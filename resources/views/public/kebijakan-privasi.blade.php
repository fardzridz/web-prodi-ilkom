@extends('layouts.public')

@section('title', ($page?->title ?: 'Kebijakan Privasi') . ' — ' . ($site?->site_name ?: 'Program Studi Ilmu Komputer'))

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush

@section('content')
<x-hero title="{{ $page?->title ?: 'Kebijakan Privasi' }}" :breadcrumbs="['Kebijakan Privasi' => null]" />

<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-3xl px-4 sm:px-8">
        <div class="rich-text">
            @if($page?->sanitized_content)
                {!! $page->sanitized_content !!}
            @else
            <h2>Kebijakan Privasi</h2>
            <p>Kami menghormati privasi pengunjung situs web {{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }}. Halaman ini menjelaskan informasi apa saja yang kami kumpulkan dan bagaimana kami menggunakannya.</p>
            <h3>Informasi yang Kami Kumpulkan</h3>
            <p>Kami dapat mengumpulkan informasi yang Anda berikan melalui formulir kontak, termasuk nama, alamat email, dan isi pesan.</p>
            <h3>Penggunaan Informasi</h3>
            <p>Informasi yang dikumpulkan digunakan untuk merespons pertanyaan, meningkatkan layanan, dan keperluan administratif internal.</p>
            <h3>Keamanan</h3>
            <p>Kami menerapkan langkah-langkah keamanan yang wajar untuk melindungi informasi dari akses yang tidak sah.</p>
            @endif
        </div>
    </div>
</section>

<x-cta-banner />
@endsection
