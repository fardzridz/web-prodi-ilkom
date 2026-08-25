<footer class="relative z-10 bg-primary text-cream">
    <div class="grid lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
        <div class="flex flex-col justify-between bg-white/5 px-4 py-12 sm:px-8 lg:px-16 lg:py-16 xl:pl-[calc((100vw-72rem)/2)]">
            <a href="{{ route('home') }}" class="inline-flex h-16 items-center justify-center rounded-2xl bg-cream ring-1 ring-gold/40 sm:h-20 lg:h-[6.5rem]">
                @if($site?->logo)
                    <img src="{{ asset('storage/'.$site->logo) }}" alt="Logo {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="h-32 w-auto max-w-[220px] object-contain sm:h-40 sm:max-w-[260px] lg:h-52 lg:max-w-[300px]" loading="lazy" decoding="async" width="300" height="64">
                @else
                    <picture>
                        <source srcset="{{ asset('assets/images/logo/logo-motion.webp') }}" type="image/webp">
                        <img src="{{ asset('assets/images/logo/logo-motion.gif') }}" alt="Logo {{ $site?->site_name ?: 'Ilmu Komputer' }}" class="h-32 w-auto max-w-[220px] object-contain sm:h-40 sm:max-w-[260px] lg:h-52 lg:max-w-[300px]" loading="lazy" decoding="async" width="300" height="64">
                    </picture>
                @endif
            </a>

            <div class="mt-8 space-y-5 sm:mt-10 lg:mt-14">
                @if($contactInfo?->email)
                <div>
                    <p class="flex items-center gap-2 text-sm font-bold text-cream">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        Email:
                    </p>
                    <p class="mt-1 pl-6 text-sm text-cream/80">{{ $contactInfo->email }}</p>
                </div>
                @endif
                @if($contactInfo?->address)
                <div>
                    <p class="flex items-center gap-2 text-sm font-bold text-cream">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        Lokasi:
                    </p>
                    <p class="mt-1 pl-6 text-sm text-cream/80">{{ $contactInfo->address }}</p>
                </div>
                @endif
                @if($contactInfo?->phone)
                <div>
                    <p class="flex items-center gap-2 text-sm font-bold text-cream">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        WhatsApp:
                    </p>
                    <p class="mt-1 pl-6 text-sm text-cream/80">{{ $contactInfo->phone }}</p>
                </div>
                @endif
            </div>

            <div class="mt-9 flex items-center gap-2.5">
                @if($contactInfo?->facebook)
                <a href="{{ $contactInfo->facebook }}" aria-label="Facebook" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-lg bg-black/25 text-cream/80 transition-colors hover:bg-gold hover:text-primary">
                    <svg class="h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.75H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.9h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>
                </a>
                @endif
                @if($contactInfo?->instagram)
                <a href="{{ $contactInfo->instagram }}" aria-label="Instagram" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-lg bg-black/25 text-cream/80 transition-colors hover:bg-gold hover:text-primary">
                    <svg class="h-[18px] w-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1.1" fill="currentColor" stroke="none"/></svg>
                </a>
                @endif
                @if($contactInfo?->youtube)
                <a href="{{ $contactInfo->youtube }}" aria-label="YouTube" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-lg bg-black/25 text-cream/80 transition-colors hover:bg-gold hover:text-primary">
                    <svg class="h-[18px] w-[18px]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 7.19a3.02 3.02 0 0 0-2.12-2.14C19.5 4.55 12 4.55 12 4.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 7.2 31.6 31.6 0 0 0 0 12a31.6 31.6 0 0 0 .5 4.81 3.02 3.02 0 0 0 2.12 2.14c1.87.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14A31.6 31.6 0 0 0 24 12a31.6 31.6 0 0 0-.5-4.81ZM9.55 15.02V8.98L15.82 12l-6.27 3.02Z"/></svg>
                </a>
                @endif
            </div>
        </div>

        <div class="px-4 py-12 sm:px-8 lg:px-16 lg:py-16 xl:pr-[calc((100vw-72rem)/2)]">
            <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-3">
                <div>
                    <h4 class="border-b border-white/20 pb-2 text-sm font-bold uppercase tracking-wide text-cream">Layanan</h4>
                    <ul class="mt-3 space-y-1 text-sm">
                        @forelse(($site?->footer_academic_links ?? []) as $link)
                            <li>
                                <a href="{{ $link['url'] ?? '#' }}" target="_blank" rel="noopener"
                                   class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">
                                    {{ $link['label'] ?? '' }}
                                </a>
                            </li>
                        @empty
                            <li class="py-1 text-xs italic text-cream/50">Belum ada layanan.</li>
                        @endforelse
                    </ul>
                </div>
                <div>
                    <h4 class="border-b border-white/20 pb-2 text-sm font-bold uppercase tracking-wide text-cream">Menu Cepat</h4>
                    <ul class="mt-3 space-y-1 text-sm">
                        <li><a href="{{ route('home') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Beranda</a></li>
                        <li><a href="{{ route('profile') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Profil</a></li>
                        <li><a href="{{ route('activities.index') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Kegiatan</a></li>
                        <li><a href="{{ route('lecturers') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Dosen</a></li>
                        <li><a href="{{ route('alumni') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Alumni</a></li>
                        <li><a href="{{ route('journal') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Link Jurnal</a></li>
                        <li><a href="{{ route('documents') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Dokumen</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="border-b border-white/20 pb-2 text-sm font-bold uppercase tracking-wide text-cream">Lokasi</h4>
                    <div id="map-facade" class="mt-3 flex h-52 w-full cursor-pointer flex-col items-center justify-center gap-3 overflow-hidden rounded-lg border border-white/20 bg-white/10 sm:h-56 lg:h-60" data-src="https://maps.google.com/maps?q=Universitas%20PGRI%20Wiranegara%20Pasuruan&t=&z=14&ie=UTF8&iwloc=&output=embed" role="button" tabindex="0" aria-label="Muat peta lokasi Universitas PGRI Wiranegara">
                        <svg class="h-10 w-10 text-cream/60" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        <span class="rounded-full bg-gold px-4 py-1.5 text-sm font-semibold text-primary">Lihat Peta</span>
                        <span class="text-xs text-cream/60">Klik untuk memuat Google Maps</span>
                    </div>
                    <script>
                        (function () {
                            var facade = document.getElementById('map-facade');
                            if (!facade) return;
                            var loaded = false;
                            var loadMap = function () {
                                if (loaded) return;
                                loaded = true;
                                var iframe = document.createElement('iframe');
                                iframe.src = facade.dataset.src;
                                iframe.title = 'Peta lokasi Universitas PGRI Wiranegara';
                                iframe.loading = 'lazy';
                                iframe.referrerPolicy = 'no-referrer-when-downgrade';
                                iframe.allowFullscreen = true;
                                iframe.style.border = '0';
                                iframe.className = 'mt-3 h-52 w-full overflow-hidden rounded-lg border border-white/20 sm:h-56 lg:h-60';
                                facade.replaceWith(iframe);
                            };
                            facade.addEventListener('click', loadMap, { once: true });
                            facade.addEventListener('keydown', function (e) { if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); loadMap(); } });
                            if ('IntersectionObserver' in window) {
                                var io = new IntersectionObserver(function (entries, obs) {
                                    if (entries[0].isIntersecting) { loadMap(); obs.disconnect(); }
                                }, { rootMargin: '200px' });
                                io.observe(facade);
                            }
                        })();
                    </script>
                </div>
            </div>

            <div class="mt-14 flex flex-wrap items-center justify-between gap-4 border-t border-white/10 pt-6 sm:mt-16">
                <p class="text-sm text-cream/80">
                    {{ $site?->footer_text ?: '© '.date('Y').' Program Studi Ilmu Komputer.' }}
                </p>
                <ul class="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                    <li><a href="{{ route('public.privacy-policy') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Kebijakan Privasi</a></li>
                    <li><a href="{{ route('public.accessibility') }}" class="inline-block py-1 text-cream/70 transition-colors hover:text-gold">Aksesibilitas</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
