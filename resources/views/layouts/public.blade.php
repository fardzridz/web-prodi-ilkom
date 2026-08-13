<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $site?->site_name ?: 'Program Studi Ilmu Komputer')</title>
    <meta name="description" content="@yield('description', 'Website resmi Program Studi Ilmu Komputer ' . ($site?->university_name ?: 'Universitas PGRI Wiranegara') . '.')">
    <meta property="og:site_name" content="{{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }}">
    <meta property="og:title" content="@yield('title', $site?->site_name ?: 'Program Studi Ilmu Komputer')">
    <meta property="og:description" content="@yield('description', 'Website resmi Program Studi Ilmu Komputer.')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @hasSection('og_image')
    <meta property="og:image" content="{{ $__env->yieldContent('og_image') }}">
    @else
    <meta property="og:image" content="{{ $site?->logo ? asset('storage/'.$site->logo) : asset('assets/images/logone.png') }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ $site?->favicon ? asset('storage/'.$site->favicon) : asset('assets/images/logone.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;700;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    @vite('resources/css/app.css')
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

    @stack('scripts')
</body>
</html>
