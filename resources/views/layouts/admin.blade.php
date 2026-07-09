<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Website Prodi')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script defer src="{{ asset('js/app.js') }}"></script>
</head>
<body class="admin-shell">
    @include('partials.admin.sidebar')

    <div class="admin-main">
        @include('partials.admin.topbar')

        <main class="admin-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
