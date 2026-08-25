<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $seoTitle ?? config('seo.title', $site?->site_name ?: 'Program Studi Ilmu Komputer'))</title>
    <meta name="description" content="@yield('description', $seoDesc ?? config('seo.description', 'Website resmi Program Studi Ilmu Komputer ' . ($site?->university_name ?: 'Universitas PGRI Wiranegara') . '.'))">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    <meta name="robots" content="index, follow">
    <meta property="og:site_name" content="{{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }}">
    <meta property="og:title" content="@yield('title', $seoTitle ?? config('seo.title', $site?->site_name ?: 'Program Studi Ilmu Komputer'))">
    <meta property="og:description" content="@yield('description', $seoDesc ?? config('seo.description', 'Website resmi Program Studi Ilmu Komputer.'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical ?? url()->current() }}">
    @hasSection('og_image')
    <meta property="og:image" content="{{ $__env->yieldContent('og_image') }}">
    @else
    <meta property="og:image" content="{{ $site?->logo ? asset('storage/'.$site->logo) : asset('assets/images/logo/logo.webp') }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    @include('components.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;700;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if(request()->routeIs('home') && isset($heroSlides[0]['url']))
        <link rel="preload" as="image" href="{{ $heroSlides[0]['url'] }}" fetchpriority="high" imagesrcset="{{ $heroSlides[0]['url'] }}" imagesizes="100vw">
    @endif
    @if(config('seo.ga4_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('seo.ga4_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('seo.ga4_id') }}');
        </script>
    @endif
    @include('components.seo-jsonld')
    @stack('head')
</head>
<body class="overflow-x-hidden bg-line text-ink font-sans text-base leading-normal antialiased">
    <x-topbar />

    <x-navbar />

    <x-mobile-menu />

    <main>
        @yield('content')
    </main>

    <x-footer />

    <x-scroll-to-top />

    <x-floating-contact />

    @stack('scripts')
</body>
</html>
