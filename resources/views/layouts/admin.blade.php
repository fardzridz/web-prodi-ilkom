<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pengelola Situs Prodi')</title>

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    @once
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
    @endonce

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
</head>

<body
    x-data="{ loaded: true }"
    x-init="
        $store.theme.init();
        $store.sidebar.isExpanded = window.innerWidth >= 1280;
        const checkMobile = () => {
            if (window.innerWidth < 1280) {
                $store.sidebar.closeMobile();
                $store.sidebar.isExpanded = false;
            } else {
                $store.sidebar.isMobileOpen = false;
                $store.sidebar.isExpanded = true;
            }
        };
        window.addEventListener('resize', checkMobile);
    "
    class="font-outfit bg-gray-50 dark:bg-gray-900">

    <x-admin.preloader />

    <div class="min-h-screen xl:flex">
        <div
            :class="$store.sidebar.isMobileOpen ? 'block xl:hidden' : 'hidden'"
            class="fixed z-50 h-screen w-full bg-gray-900/50"
            @click="$store.sidebar.closeMobile()">
        </div>

        @include('partials.admin.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">

            @include('partials.admin.app-header')

            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                @include('partials.admin.flash')

                <x-admin.page-breadcrumb :pageTitle="trim($__env->yieldContent('page-heading', 'Dashboard'))" />

                @yield('content')
            </div>
        </div>
    </div>

    <script src="{{ asset('js/quill-init.js') }}"></script>

    @stack('scripts')

</body>

</html>
