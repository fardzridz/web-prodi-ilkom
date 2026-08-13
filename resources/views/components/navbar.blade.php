<header class="sticky top-0 z-20 bg-line text-ink transition-transform duration-300 ease-out will-change-transform">
    <div class="mx-4 px-4 h-20 flex items-center justify-between gap-3 sm:mx-8 sm:px-8 sm:h-24 sm:gap-6 lg:mx-16 lg:px-20 lg:h-28">
        <a href="{{ route('home') }}" class="anim-fade-down anim-delay-1 shrink-0">
            <img src="{{ asset('assets/images/logo-ilkom-motion.gif') }}" alt="Logo {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="h-36 w-auto object-contain sm:h-44 lg:h-60">
        </a>
        <nav aria-label="Navigasi utama" class="hidden lg:block">
            <ul class="flex items-center gap-8 text-base">
                @php
                    $navLinks = [
                        ['route' => 'home', 'label' => 'Beranda', 'delay' => 2],
                        ['route' => 'profile', 'label' => 'Profil', 'delay' => 3],
                        ['route' => 'activities.index', 'label' => 'Kegiatan', 'delay' => 4],
                        ['route' => 'lecturers', 'label' => 'Dosen', 'delay' => 5],
                        ['route' => 'alumni', 'label' => 'Alumni', 'delay' => 6],
                        ['route' => 'journal', 'label' => 'Link Jurnal', 'delay' => 7],
                        ['route' => 'documents', 'label' => 'Dokumen', 'delay' => 8],
                    ];
                @endphp
                @foreach($navLinks as $link)
                <li class="anim-fade-down anim-delay-{{ $link['delay'] }}">
                    <a href="{{ route($link['route']) }}" @class([
                        'text-primary font-semibold nav-underline' => request()->routeIs($link['route']),
                        'text-black hover:text-primary transition-colors nav-underline' => !request()->routeIs($link['route']),
                    ]) @if(request()->routeIs($link['route'])) aria-current="page" @endif>
                        {{ $link['label'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </nav>
        <div class="flex shrink-0 items-center gap-3">
            <button type="button" aria-label="Cari" class="anim-fade-down anim-delay-4 btn btn-primary h-11 w-11 p-0">
                <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </button>
            <a href="https://admisi.uniwara.ac.id" target="_blank" rel="noopener" class="anim-fade-down anim-delay-4 btn btn-primary btn-md h-11 hidden lg:flex">
                <span class="btn-label">Daftar Sekarang</span>
                <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
            <button type="button" id="menu-btn" aria-label="Buka menu" aria-expanded="false" aria-controls="mobile-menu" class="anim-fade-down anim-delay-5 lg:hidden inline-flex h-11 w-11 items-center justify-center rounded-full border border-primary/40 text-primary transition-colors hover:border-primary hover:text-primary">
                <svg data-icon-open class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg data-icon-close class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</header>
