@php
    $mobileLinks = [
        ['label' => 'Beranda', 'route' => 'home', 'href' => route('home'), 'icon' => 'fa-house'],
        ['label' => 'Profil', 'route' => 'profile', 'href' => route('profile'), 'icon' => 'fa-id-card'],
        ['label' => 'Kegiatan', 'route' => 'activities.*', 'href' => route('activities.index'), 'icon' => 'fa-calendar-days'],
        ['label' => 'Dosen', 'route' => 'lecturers', 'href' => route('lecturers'), 'icon' => 'fa-chalkboard-user'],
        ['label' => 'Alumni', 'route' => 'alumni', 'href' => route('alumni'), 'icon' => 'fa-user-graduate'],
        ['label' => 'Jurnal', 'route' => 'journal', 'href' => route('journal'), 'icon' => 'fa-book-open', 'external' => true],
        ['label' => 'Dokumen', 'route' => 'documents', 'href' => route('documents'), 'icon' => 'fa-file-lines'],
    ];
@endphp

<header class="site-header relative z-[50] bg-blue-dark text-white">
    <div class="header-shell relative grid grid-cols-[auto_minmax(0,1fr)] items-end w-[min(100%_-_90px,var(--container))] h-[110px] mx-auto max-[1024px]:w-full max-[1024px]:h-[66px] max-[1024px]:px-[22px] max-[1024px]:grid-cols-[auto_minmax(0,1fr)_auto]">
        <a class="brand inline-flex items-center self-center" href="{{ route('home') }}" aria-label="Beranda">
            <img class="brand-logo block w-auto max-w-none h-[70px] object-contain max-[1024px]:h-12 max-[1024px]:max-w-[calc(100vw_-_90px)]" src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo Program Studi">
        </a>

        <div class="desktop-header absolute top-0 right-0 flex items-center h-10 pr-4 pl-[60px] gap-[30px] isolate max-[1024px]:hidden" aria-label="Tautan cepat">
            <a class="inline-flex items-center gap-1.5 text-[#eef4f8] text-[13px] font-light leading-none" href="#" aria-label="Instagram">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
                <span>Instagram</span>
            </a>
            <a class="inline-flex items-center gap-1.5 text-[#eef4f8] text-[13px] font-light leading-none" href="#" aria-label="YouTube">
                <i class="fa-brands fa-youtube" aria-hidden="true"></i>
                <span>YouTube</span>
            </a>
            <a class="inline-flex items-center gap-1.5 text-[#eef4f8] text-[13px] font-light leading-none" href="#" aria-label="Alamat kampus di Pasuruan">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                <span>Pasuruan</span>
            </a>
            <div class="language-switcher relative">
                <button class="language-toggle inline-flex items-center gap-1.5 h-[26px] px-[11px] text-white font-body text-[13px] font-bold leading-none cursor-pointer bg-red border-0" type="button" aria-haspopup="true" aria-expanded="false">
                    ID
                </button>
                <div class="language-menu absolute top-[calc(100%+8px)] right-0 z-20 hidden min-w-[72px] py-1.5 bg-white border border-[rgba(0,36,58,0.14)] shadow-[0_12px_22px_rgba(0,29,46,0.22)] [&_a]:block [&_a]:py-[7px] [&_a]:px-[13px] [&_a]:text-blue-dark [&_a]:text-[13px] [&_a]:font-bold [&_a]:leading-none [&_a:hover]:bg-yellow" role="menu" aria-label="Pilih bahasa">
                    <a href="{{ url()->current() }}" role="menuitem" aria-current="true">ID</a>
                    <a href="#" role="menuitem">EN</a>
                </div>
            </div>
        </div>

        <nav class="desktop-nav flex justify-end items-center gap-[26px] pb-5 text-grey-4 font-body text-[15px] font-normal leading-[1.2] tracking-normal normal-case max-[1024px]:hidden [&_a]:inline-flex [&_a]:items-center [&_a]:gap-2 [&_a]:px-[5px] [&_a]:font-light [&_i]:text-[17px] [&_i]:leading-none" aria-label="Navigasi utama">
            <a href="mailto:univ.pgriwiranegara@gmail.com">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
                <span>univ.pgriwiranegara@gmail.com</span>
            </a>
            <a href="https://wa.me/6282141554377">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
                <span>0821-4155-4377</span>
            </a>
        </nav>

        <button class="menu-toggle absolute right-0 bottom-[13px] hidden w-10 h-10 items-center justify-center text-white cursor-pointer border-0 rounded-full bg-[#001b2c] max-[1024px]:inline-flex max-[1024px]:right-[22px]" type="button" aria-label="Buka menu" aria-expanded="false">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="mobile-nav fixed inset-0 z-[100] grid content-start gap-[22px] overflow-y-auto overscroll-contain pt-[84px] px-[26px] pb-[34px] text-blue-dark bg-white opacity-0 invisible pointer-events-none [transition:opacity_0.26s_ease_0.78s,visibility_0s_linear_1.04s] [-webkit-overflow-scrolling:touch]" aria-label="Navigasi seluler">
        <button class="mobile-close absolute top-[18px] right-[18px] w-11 h-11 border-0 bg-transparent" type="button" aria-label="Tutup menu"></button>
        <div class="mobile-nav-header flex items-center flex-wrap gap-3 pb-6 border-b border-[rgba(0,36,58,0.14)]" aria-label="Tautan cepat">
            <a class="mobile-quick-link mobile-anim-item inline-flex w-11 h-11 items-center justify-center flex-none border border-[rgba(0,36,58,0.18)] rounded-full bg-[rgba(0,36,58,0.045)] opacity-0 translate-x-[52px] [transition:opacity_0.28s_ease,transform_0.48s_cubic-bezier(0.22,1,0.36,1)] [transition-delay:calc(var(--exit-order)*45ms)]" href="#" aria-label="Instagram">
                <i class="fa-brands fa-instagram" aria-hidden="true"></i>
            </a>
            <a class="mobile-quick-link mobile-anim-item inline-flex w-11 h-11 items-center justify-center flex-none border border-[rgba(0,36,58,0.18)] rounded-full bg-[rgba(0,36,58,0.045)] opacity-0 translate-x-[52px] [transition:opacity_0.28s_ease,transform_0.48s_cubic-bezier(0.22,1,0.36,1)] [transition-delay:calc(var(--exit-order)*45ms)]" href="#" aria-label="YouTube">
                <i class="fa-brands fa-youtube" aria-hidden="true"></i>
            </a>
            <a class="mobile-quick-link mobile-anim-item inline-flex w-11 h-11 items-center justify-center flex-none border border-[rgba(0,36,58,0.18)] rounded-full bg-[rgba(0,36,58,0.045)] opacity-0 translate-x-[52px] [transition:opacity_0.28s_ease,transform_0.48s_cubic-bezier(0.22,1,0.36,1)] [transition-delay:calc(var(--exit-order)*45ms)]" href="#" aria-label="Lokasi kampus di Pasuruan">
                <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
            </a>
            <a class="mobile-quick-link mobile-anim-item inline-flex w-11 h-11 items-center justify-center flex-none border border-[rgba(0,36,58,0.18)] rounded-full bg-[rgba(0,36,58,0.045)] opacity-0 translate-x-[52px] [transition:opacity_0.28s_ease,transform_0.48s_cubic-bezier(0.22,1,0.36,1)] [transition-delay:calc(var(--exit-order)*45ms)]" href="mailto:univ.pgriwiranegara@gmail.com" aria-label="Email univ.pgriwiranegara@gmail.com">
                <i class="fa-solid fa-envelope" aria-hidden="true"></i>
            </a>
            <a class="mobile-quick-link mobile-anim-item inline-flex w-11 h-11 items-center justify-center flex-none border border-[rgba(0,36,58,0.18)] rounded-full bg-[rgba(0,36,58,0.045)] opacity-0 translate-x-[52px] [transition:opacity_0.28s_ease,transform_0.48s_cubic-bezier(0.22,1,0.36,1)] [transition-delay:calc(var(--exit-order)*45ms)]" href="https://wa.me/6282141554377" aria-label="WhatsApp 0821-4155-4377">
                <i class="fa-brands fa-whatsapp" aria-hidden="true"></i>
            </a>
        </div>

        <div class="mobile-nav-menu grid gap-2 [&>a]:flex [&>a]:items-center [&>a]:gap-3.5 [&>a]:min-h-[58px] [&>a]:py-3 [&>a]:text-blue-dark [&>a]:font-slim [&>a]:text-[28px] [&>a]:font-normal [&>a]:leading-none [&>a]:border-b [&>a]:border-[rgba(0,36,58,0.12)] [&_i]:w-7 [&_i]:text-[22px] [&_i]:text-center">
            @foreach ($mobileLinks as $link)
                <a class="mobile-anim-item opacity-0 translate-x-[52px] [transition:opacity_0.28s_ease,transform_0.48s_cubic-bezier(0.22,1,0.36,1)] [transition-delay:calc(var(--exit-order)*45ms)]" href="{{ $link['href'] }}" @if (request()->routeIs($link['route'])) aria-current="page" @endif @if ($link['external'] ?? false) target="_blank" rel="noopener" aria-label="Buka web jurnal" @endif>
                    <i class="fa-solid {{ $link['icon'] }}" aria-hidden="true"></i>
                    <span>{{ $link['label'] }} @if ($link['external'] ?? false)<i class="fa-solid fa-arrow-up-right-from-square nav-external-icon !w-auto ml-1.5 text-[11px] opacity-[0.78]" aria-hidden="true"></i>@endif</span>
                </a>
            @endforeach
        </div>
    </nav>
</header>
