<aside id="sidebar"
    class="fixed top-0 left-0 z-99999 flex flex-col h-screen px-5 mt-0 text-gray-900 transition-all duration-300 ease-in-out bg-white border-r border-gray-200 dark:bg-gray-900 dark:border-gray-800"
    x-data="{
        activeSubmenu: null,
        toggleSubmenu(key) { this.activeSubmenu = this.activeSubmenu === key ? null : key; },
        isSubmenuOpen(key) { return this.activeSubmenu === key; },
        isActive(path) { return document.querySelector(`[data-route='${path}']`)?.getAttribute('aria-current') === 'page'; }
    }"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.isHovered = true"
    @mouseleave="$store.sidebar.isHovered = false">

    <div class="flex pt-8 pb-7"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'">
        <a href="{{ route('admin.dashboard') }}">
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="dark:hidden" src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo Prodi" width="150" height="40" />
            <img x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                class="hidden dark:block" src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo Prodi" width="150" height="40" />
            <img x-show="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen"
                src="{{ asset('assets/images/logo/logo-icon.svg') }}" alt="Logo" width="32" height="32" />
        </a>
    </div>

    <div class="flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                <div>
                    <a href="{{ route('admin.dashboard') }}" class="menu-item group"
                        :class="[
                            '{{ request()->routeIs('admin.dashboard') ? 'menu-item-active' : 'menu-item-inactive' }}',
                            (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                        ]"
                        @if(request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <span @class(['menu-item-icon-active' => request()->routeIs('admin.dashboard'), 'menu-item-icon-inactive' => ! request()->routeIs('admin.dashboard')])>
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z" fill="currentColor"/></svg>
                        </span>
                        <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">Dashboard</span>
                    </a>
                </div>

                <div>
                    <h2 class="flex mb-4 text-xs uppercase leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Konten Situs</span>
                        </template>
                        <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.999 10.245a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zm12 0a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zM10.25 12a1.75 1.75 0 113.5 0 1.75 1.75 0 01-3.5 0z" fill="currentColor"/></svg>
                        </template>
                    </h2>
                    <ul class="flex flex-col gap-1">
                        @php
                            $menuItems = [
                                ['route' => 'admin.beranda', 'label' => 'Beranda', 'icon' => '<path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" fill="currentColor"/>'],
                                ['route' => 'admin.profil', 'label' => 'Profil Prodi', 'icon' => '<path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z" fill="currentColor"/><path d="M4 20h16v-2.5c0-1.5-3-2.5-8-2.5s-8 1-8 2.5V20z" fill="currentColor"/>'],
                            ];
                        @endphp
                        @foreach ($menuItems as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" class="menu-item group"
                                    :class="[
                                        '{{ request()->routeIs($item['route'].'*') ? 'menu-item-active' : 'menu-item-inactive' }}',
                                        (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                    ]"
                                    @if(request()->routeIs($item['route'].'*')) aria-current="page" @endif>
                                    <span @class(['menu-item-icon-active' => request()->routeIs($item['route'].'*'), 'menu-item-icon-inactive' => ! request()->routeIs($item['route'].'*')])>
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $item['icon'] !!}</svg>
                                    </span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="flex mb-4 text-xs uppercase leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Data Prodi</span>
                        </template>
                        <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.999 10.245a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zm12 0a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zM10.25 12a1.75 1.75 0 113.5 0 1.75 1.75 0 01-3.5 0z" fill="currentColor"/></svg>
                        </template>
                    </h2>
                    <ul class="flex flex-col gap-1">
                        @php
                            $dataItems = [
                                ['route' => 'admin.dosen.index', 'label' => 'Dosen', 'icon' => '<path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/><path d="M5 9.5V8c0-.5.5-1 1-1h1l1-2h2l1 2h1c.5 0 1 .5 1 1v1.5" stroke="currentColor" stroke-width="1.5" fill="none"/>'],
                                ['route' => 'admin.alumni.index', 'label' => 'Alumni', 'icon' => '<path d="M12 3L1 9l4 2.18v6L12 21l7-3.82v-6l2-1.09V17h2V9L12 3zm6.82 6L12 12.72 5.18 9 12 5.28 18.82 9zM17 15.99l-5 2.73-5-2.73v-3.72L12 15l5-2.73v3.72z" fill="currentColor"/>'],
                            ];
                        @endphp
                        @foreach ($dataItems as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" class="menu-item group"
                                    :class="[
                                        '{{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'menu-item-active' : 'menu-item-inactive' }}',
                                        (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                    ]"
                                    @if(request()->routeIs(str_replace('.index', '.*', $item['route']))) aria-current="page" @endif>
                                    <span @class(['menu-item-icon-active' => request()->routeIs(str_replace('.index', '.*', $item['route'])), 'menu-item-icon-inactive' => ! request()->routeIs(str_replace('.index', '.*', $item['route']))])>
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $item['icon'] !!}</svg>
                                    </span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="flex mb-4 text-xs uppercase leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Publikasi</span>
                        </template>
                        <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.999 10.245a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zm12 0a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zM10.25 12a1.75 1.75 0 113.5 0 1.75 1.75 0 01-3.5 0z" fill="currentColor"/></svg>
                        </template>
                    </h2>
                    <ul class="flex flex-col gap-1">
                        @php
                            $pubItems = [
                                ['route' => 'admin.kegiatan.index', 'label' => 'Kegiatan', 'icon' => '<path d="M19 4h-1V2h-2v2H8V2H6v2H5c-1.11 0-1.99.9-1.99 2L3 20c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 16H5V10h14v10zm0-12H5V6h14v2zm-7 5h5v5h-5v-5z" fill="currentColor"/>'],
                                ['route' => 'admin.dokumen.index', 'label' => 'Dokumen', 'icon' => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="currentColor"/>'],
                                ['route' => 'admin.kategori-dokumen.index', 'label' => 'Kategori Dokumen', 'icon' => '<path d="M20 6h-8l-2-2H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-2 8h-3v3h-2v-3h-3v-2h3V9h2v3h3v2z" fill="currentColor"/>'],
                            ];
                        @endphp
                        @foreach ($pubItems as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" class="menu-item group"
                                    :class="[
                                        '{{ request()->routeIs(str_replace('.index', '.*', $item['route'])) ? 'menu-item-active' : 'menu-item-inactive' }}',
                                        (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                    ]"
                                    @if(request()->routeIs(str_replace('.index', '.*', $item['route']))) aria-current="page" @endif>
                                    <span @class(['menu-item-icon-active' => request()->routeIs(str_replace('.index', '.*', $item['route'])), 'menu-item-icon-inactive' => ! request()->routeIs(str_replace('.index', '.*', $item['route']))])>
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $item['icon'] !!}</svg>
                                    </span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div>
                    <h2 class="flex mb-4 text-xs uppercase leading-[20px] text-gray-400"
                        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'lg:justify-center' : 'justify-start'">
                        <template x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                            <span>Pengaturan</span>
                        </template>
                        <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.999 10.245a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zm12 0a1.75 1.75 0 110 3.5 1.75 1.75 0 010-3.5zM10.25 12a1.75 1.75 0 113.5 0 1.75 1.75 0 01-3.5 0z" fill="currentColor"/></svg>
                        </template>
                    </h2>
                    <ul class="flex flex-col gap-1">
                        @php
                            $settingItems = [
                                ['route' => 'admin.kontak', 'label' => 'Kontak', 'icon' => '<path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 3c1.93 0 3.5 1.57 3.5 3.5S13.93 13 12 13s-3.5-1.57-3.5-3.5S10.07 6 12 6zm7 13H5v-.23c0-.62.28-1.2.76-1.58C7.47 15.82 9.64 15 12 15s4.53.82 6.24 2.19c.48.38.76.97.76 1.58V19z" fill="currentColor"/>'],
                                ['route' => 'admin.halaman', 'label' => 'Halaman Situs', 'icon' => '<path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" fill="currentColor"/>'],
                                ['route' => 'admin.pengaturan', 'label' => 'Pengaturan Situs', 'icon' => '<path d="M19.14 12.94c.04-.3.06-.61.06-.94 0-.33-.02-.64-.06-.94l2.02-1.58a.49.49 0 00.12-.61l-1.92-3.32a.49.49 0 00-.59-.22l-2.39.96c-.5-.38-1.03-.7-1.62-.94L14.4 2.81c-.04-.24-.24-.41-.48-.41h-3.84c-.24 0-.43.17-.47.41l-.36 2.54c-.59.24-1.13.56-1.62.94l-2.39-.96a.49.49 0 00-.59.22L2.74 8.87c-.12.21-.08.47.12.61l2.02 1.58c-.04.3-.06.62-.06.94s.02.64.06.94l-2.02 1.58a.49.49 0 00-.12.61l1.92 3.32c.12.22.37.29.59.22l2.39-.96c.5.38 1.03.7 1.62.94l.36 2.54c.05.24.24.41.48.41h3.84c.24 0 .44-.17.47-.41l.36-2.54c.59-.24 1.13-.56 1.62-.94l2.39.96c.22.07.47 0 .59-.22l1.92-3.32c.12-.22.07-.47-.12-.61l-2.01-1.58zM12 15.6c-1.98 0-3.6-1.62-3.6-3.6s1.62-3.6 3.6-3.6 3.6 1.62 3.6 3.6-1.62 3.6-3.6 3.6z" fill="currentColor"/>'],
                                ['route' => 'admin.akun-admin', 'label' => 'Akun Pengelola', 'icon' => '<path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill="currentColor"/>'],
                            ];
                        @endphp
                        @foreach ($settingItems as $item)
                            <li>
                                <a href="{{ route($item['route']) }}" class="menu-item group"
                                    :class="[
                                        '{{ request()->routeIs($item['route'].'*') ? 'menu-item-active' : 'menu-item-inactive' }}',
                                        (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ? 'xl:justify-center' : 'justify-start'
                                    ]"
                                    @if(request()->routeIs($item['route'].'*')) aria-current="page" @endif>
                                    <span @class(['menu-item-icon-active' => request()->routeIs($item['route'].'*'), 'menu-item-icon-inactive' => ! request()->routeIs($item['route'].'*')])>
                                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none">{!! $item['icon'] !!}</svg>
                                    </span>
                                    <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" class="menu-item-text">{{ $item['label'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </nav>

        <div x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" x-transition class="mt-auto pb-6">
            <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-white/[0.03]">
                <span class="flex items-center justify-center w-10 h-10 rounded-full bg-brand-500/10 text-brand-500 dark:bg-brand-500/10">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z" fill="currentColor"/>
                    </svg>
                </span>
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">Panel Pengelola</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">v{{ config('app.version', '1.0') }}</p>
                </div>
            </div>
        </div>
    </div>
</aside>
