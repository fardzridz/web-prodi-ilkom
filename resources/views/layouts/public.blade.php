<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Program Studi Ilmu Komputer')</title>
    <meta name="description" content="@yield('description', 'Website resmi Program Studi Ilmu Komputer Universitas PGRI Wiranegara.')">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'blue-dark': '#00243a',
                        'blue-deep': '#001d2e',
                        'blue-mid': '#29557b',
                        'blue-section': '#153d5b',
                        red: '#e91b2f',
                        'red-dark': '#961a26',
                        yellow: '#fdb913',
                        'grey-1': '#343638',
                        'grey-2': '#5b5c5e',
                        'grey-3': '#898a8c',
                        'grey-4': '#a7a9ac',
                        'grey-5': '#e6e9ec',
                    },
                    fontFamily: {
                        body: ['Fakt Local', 'Fakt', 'Fakt Pro', 'Aptos', 'Segoe UI Variable Text', 'Segoe UI', 'Arial', 'sans-serif'],
                        display: ['Grold Local', 'Grold', 'Aptos Display', 'Segoe UI Variable Display', 'Segoe UI', 'Arial', 'sans-serif'],
                        slim: ['Grold Slim Local', 'GroldSlim', 'Grold Local', 'Aptos', 'Segoe UI Variable Text', 'Segoe UI', 'Arial', 'sans-serif'],
                        rounded: ['Grold Rounded Local', 'Grold Local', 'Aptos Display', 'Segoe UI', 'Arial', 'sans-serif'],
                        script: ['Gotcha Local', 'Brush Script MT', 'cursive'],
                    },
                },
            },
        };
    </script>
    <link rel="stylesheet" href="{{ asset('css/public.css') }}">
</head>
<body class="m-0 overflow-x-clip bg-[#f8f9fa] text-grey-1 font-body text-base font-normal leading-[1.55] tracking-normal antialiased">
    @include('partials.public.header')

    <main>
        @yield('content')
    </main>

    @include('partials.public.footer')
    @include('partials.public.sticky-actions')
    <script src="{{ asset('js/public.js') }}"></script>
</body>
</html>
