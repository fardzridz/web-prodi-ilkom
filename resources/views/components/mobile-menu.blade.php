<div id="mobile-menu" aria-label="Menu mobile" class="lg:hidden fixed inset-0 z-50 flex flex-col overflow-x-auto bg-line transition-transform duration-300 ease-out" style="transform: translateX(100%)">
    <div class="flex h-20 shrink-0 items-center justify-between px-4 sm:h-24 sm:px-8">
        <a href="{{ route('home') }}" class="shrink-0">
            @if($site?->logo)
                <img src="{{ asset('storage/'.$site->logo) }}" alt="Logo {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="h-36 w-auto max-w-[200px] object-contain sm:h-44 sm:max-w-[260px]" loading="lazy" decoding="async" width="260" height="55">
            @else
                <picture>
                    <source srcset="{{ asset('assets/images/logo/logo-motion.webp') }}" type="image/webp">
                    <img src="{{ asset('assets/images/logo/logo-motion.gif') }}" alt="Logo {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="h-36 w-auto max-w-[200px] object-contain sm:h-44 sm:max-w-[260px]" loading="lazy" decoding="async" width="260" height="55">
                </picture>
            @endif
        </a>
        <button type="button" id="menu-close" aria-label="Tutup menu" class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-primary/40 text-primary transition-colors hover:border-primary hover:text-primary">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    <ul class="flex flex-col gap-1 px-4 pb-4 text-base sm:px-8">
        @php
            $mobileLinks = [
                ['route' => 'home', 'label' => 'Beranda', 'delay' => 1],
                ['route' => 'profile', 'label' => 'Profil', 'delay' => 2],
                ['route' => 'activities.index', 'label' => 'Kegiatan', 'delay' => 3],
                ['route' => 'lecturers', 'label' => 'Dosen', 'delay' => 4],
                ['route' => 'alumni', 'label' => 'Alumni', 'delay' => 5],
                ['route' => 'journal', 'label' => 'Link Jurnal', 'delay' => 6],
                ['route' => 'documents', 'label' => 'Dokumen', 'delay' => 7],
            ];
        @endphp
        @foreach($mobileLinks as $link)
        <li class="anim-fade-left anim-delay-{{ $link['delay'] }}">
            <a href="{{ route($link['route']) }}" @class([
                'inline-block rounded-lg px-3 py-2.5 text-primary font-semibold' => request()->routeIs($link['route']),
                'block rounded-lg px-3 py-2.5 text-black hover:bg-primary-light hover:text-primary transition-colors' => !request()->routeIs($link['route']),
            ]) @if(request()->routeIs($link['route'])) aria-current="page" @endif>
                @if(request()->routeIs($link['route']))
                    <span class="relative nav-underline-static">{{ $link['label'] }}</span>
                @else
                    {{ $link['label'] }}
                @endif
            </a>
        </li>
        @endforeach
        <li class="anim-fade-left anim-delay-8">
            <a href="{{ $site?->registration_url ?: 'https://admisi.uniwara.ac.id' }}" target="_blank" rel="noopener" class="btn btn-primary btn-md h-11 w-full">
                <span class="btn-label">Daftar Sekarang</span>
                <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </li>
    </ul>
</div>
