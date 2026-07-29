<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Pengelola Situs Prodi')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.css">
    <link rel="stylesheet" href="{{ asset('css/app/app.css') }}?v={{ filemtime(public_path('css/app/app.css')) }}">
    <script defer src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/trix/1.3.1/trix.min.js"></script>
</head>
<body class="admin-body">
    <div id="admin-sidebar-overlay" class="admin-sidebar-overlay" data-sidebar-close aria-hidden="true"></div>

    @include('partials.admin.sidebar')

    <div class="admin-main">
        @include('partials.admin.topbar')

        <main class="admin-content">
            <div class="admin-content-inner">
                <nav class="admin-breadcrumb" aria-label="Jejak halaman">
                    <a href="{{ route('admin.dashboard') }}">Pengelola</a>
                    <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
                    <span>@yield('page-section', 'Dashboard')</span>
                </nav>

                <header class="admin-page-heading">
                    <h1>@yield('page-heading', 'Dashboard')</h1>
                    <p>@yield('page-helper', 'Kelola konten situs Program Studi Ilmu Komputer.')</p>
                </header>

                @include('partials.admin.flash')

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
