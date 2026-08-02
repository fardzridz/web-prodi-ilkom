<footer class="site-footer relative overflow-hidden mt-24 pt-[118px] text-white bg-blue-dark max-[1024px]:pt-24 max-[560px]:mt-16 max-[560px]:pt-[78px] [&_h3]:m-0 [&_h3]:mb-5 [&_h3]:text-yellow [&_h3]:text-[17px] [&_h3]:font-extrabold [&_h3]:tracking-normal [&_h3]:normal-case">
    @php
        $whatsappNumber = $contactInfo->phone ? '62'.ltrim(preg_replace('/\D/', '', $contactInfo->phone), '0') : '6282141554377';
    @endphp
    <div class="footer-dots absolute top-[118px] right-[-24px] w-[140px] h-[420px] pointer-events-none opacity-[0.42] max-[1024px]:top-24 max-[1024px]:right-[-70px] max-[1024px]:opacity-20" aria-hidden="true"></div>
    <div class="container footer-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[2] grid grid-cols-[minmax(250px,1.08fr)_minmax(168px,0.62fr)_minmax(168px,0.62fr)_minmax(240px,0.82fr)] gap-[clamp(28px,4vw,56px)] pb-[76px] max-[1024px]:grid-cols-1 max-[1024px]:gap-[30px] max-[1024px]:pb-14">
        <div class="footer-info">
            <a class="brand footer-brand inline-flex items-center self-center w-fit mb-[34px] max-[560px]:mb-7 [&_.brand-logo]:!h-[76px] [&_.brand-logo]:!max-w-[min(100%,250px)] max-[1024px]:[&_.brand-logo]:!h-16 max-[1024px]:[&_.brand-logo]:!max-w-[min(100%,240px)] max-[560px]:[&_.brand-logo]:!h-[60px] max-[560px]:[&_.brand-logo]:!max-w-[min(100%,220px)]" href="{{ route('home') }}" aria-label="Beranda">
                <img class="brand-logo block w-auto max-w-none h-[70px] object-contain max-[1024px]:h-12 max-[1024px]:max-w-[calc(100vw_-_90px)]" src="{{ asset($siteSetting?->logo ? 'storage/'.$siteSetting->logo : 'assets/images/logo/logo.png') }}" alt="Logo Program Studi">
            </a>
            <a class="footer-phone w-fit text-[#9fb0be] text-[21px] font-extrabold leading-[1.1] max-[560px]:text-[19px]" href="tel:+{{ $whatsappNumber }}">{{ $contactInfo->phone ?? '0821-4155-4377' }}</a>
            <p class="footer-address my-0 mt-[26px] mb-4 text-[#9fb0be] text-[15px] font-normal leading-[1.55]">{!! nl2br(e($contactInfo->address ?? "Jl. Ki Hajar Dewantara No. 27-29\nPasuruan, Jawa Timur")) !!}</p>
            <a class="footer-contact w-fit text-[#c7d1d9] text-[13px] underline underline-offset-[3px] hover:text-yellow" href="mailto:{{ $contactInfo->email ?? 'univ.pgriwiranegara@gmail.com' }}">{{ $contactInfo->email ?? 'univ.pgriwiranegara@gmail.com' }}</a>
            <div class="footer-social flex flex-wrap gap-7 mt-9 max-[560px]:gap-5 max-[560px]:mt-[30px] [&_a]:inline-flex [&_a]:w-[30px] [&_a]:h-[30px] [&_a]:items-center [&_a]:justify-center [&_a]:text-[#477599] [&_a]:text-[27px] [&_a]:leading-none [&_a]:transition [&_a]:duration-[180ms] [&_a:hover]:text-yellow [&_a:hover]:-translate-y-0.5" aria-label="Media sosial">
                <a class="inline-flex items-center gap-1.5 text-[#eef4f8] text-[13px] font-light leading-none" href="{{ $contactInfo->instagram ?? '#' }}" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>
                <a class="inline-flex items-center gap-1.5 text-[#eef4f8] text-[13px] font-light leading-none" href="{{ $contactInfo->youtube ?? '#' }}" aria-label="YouTube"><i class="fa-brands fa-youtube" aria-hidden="true"></i></a>
                <a href="https://wa.me/{{ $whatsappNumber }}" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp" aria-hidden="true"></i></a>
                <a href="mailto:{{ $contactInfo->email ?? 'univ.pgriwiranegara@gmail.com' }}" aria-label="Email"><i class="fa-solid fa-envelope" aria-hidden="true"></i></a>
                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contactInfo->address ?? 'Pasuruan') }}" aria-label="Lokasi"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></a>
            </div>
        </div>
        <nav class="footer-column pt-[52px] max-[1024px]:pt-0 [&_a]:block [&_a]:text-[#c7d1d9] [&_a]:text-base max-[560px]:[&_a]:text-[15px] [&_a]:font-semibold [&_a]:leading-[1.3] [&_a:hover]:text-yellow [&_a+a]:mt-5 max-[560px]:[&_a+a]:mt-4" aria-label="Akademik">
            <h3>Akademik</h3>
            @forelse ($site->footer_academic_links ?? [] as $link)
                <a href="{{ $link['url'] }}" target="_blank" rel="noopener">{{ $link['label'] }}</a>
            @empty
                <a href="https://wiraakademik.uniwara.ac.id" target="_blank" rel="noopener">Wiraakademik</a>
                <a href="https://student.wiraakademik.uniwara.ac.id" target="_blank" rel="noopener">Student Wiraakademik</a>
                <a href="https://wiramerdeka.uniwara.ac.id" target="_blank" rel="noopener">Wiramerdeka MBKM</a>
                <a href="https://wiraakademik.uniwara.ac.id/kalender-akademik" target="_blank" rel="noopener">Calender Akademik</a>
                <a href="https://wiralearning.uniwara.ac.id" target="_blank" rel="noopener">Wiralearning</a>
            @endforelse
        </nav>
        <nav class="footer-column pt-[52px] max-[1024px]:pt-0 [&_a]:block [&_a]:text-[#c7d1d9] [&_a]:text-base max-[560px]:[&_a]:text-[15px] [&_a]:font-semibold [&_a]:leading-[1.3] [&_a:hover]:text-yellow [&_a+a]:mt-5 max-[560px]:[&_a+a]:mt-4" aria-label="Menu cepat">
            <h3>Menu Cepat</h3>
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ route('lecturers') }}">Dosen</a>
            <a href="{{ route('alumni') }}">Alumni</a>
            <a href="{{ route('documents') }}">Dokumen</a>
            <a href="{{ route('home') }}#kontak-section">Kontak Prodi</a>
            <a href="https://wa.me/{{ $whatsappNumber }}">WhatsApp</a>
        </nav>
        <div class="footer-column footer-map-column pt-[52px] max-[1024px]:pt-0 min-w-0 [&_a]:block [&_a]:text-[#c7d1d9] [&_a]:text-base [&_a]:font-semibold [&_a]:leading-[1.3]" aria-label="Lokasi kampus">
            <h3>Lokasi</h3>
            <div class="contact-map footer-map relative min-h-[190px] w-[min(100%,320px)] min-h-[164px] m-0 aspect-[4/3] max-[560px]:aspect-auto max-[560px]:min-h-44">
                @if ($contactInfo->map_embed)
                    <iframe class="absolute inset-0 w-full h-full border border-[rgba(255,255,255,0.26)]" src="{{ $contactInfo->map_embed }}" title="Peta lokasi kampus Program Studi Ilmu Komputer" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                @else
                    <div class="contact-map-placeholder absolute inset-0 overflow-hidden text-white border border-[rgba(255,255,255,0.26)]" role="img" aria-label="Peta lokasi kampus Program Studi Ilmu Komputer">
                        <span class="contact-map-pin absolute top-1/2 left-1/2 inline-flex w-[54px] h-[54px] items-center justify-center text-blue-dark bg-yellow rounded-full -translate-x-1/2 -translate-y-1/2"><i class="fa-solid fa-location-dot" aria-hidden="true"></i></span>
                        <span class="contact-map-label absolute right-5 bottom-[18px] text-[rgba(255,255,255,0.82)] text-xs font-extrabold tracking-[0.08em] uppercase">Lokasi Kampus</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="footer-bottom relative z-[2] py-[34px] bg-blue-deep [&_a]:block [&_a]:text-[#9fb0be] [&_a]:text-xs [&_a]:font-medium [&_a:hover]:text-yellow [&_p]:m-0 [&_p]:text-[#9fb0be] [&_p]:text-xs [&_p]:font-medium">
        <div class="container footer-bottom-inner w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] flex items-center justify-between gap-6 max-[1024px]:items-start max-[560px]:grid max-[560px]:gap-4">
            <div class="footer-legal flex flex-wrap gap-7 max-[560px]:gap-[18px]">
                <a href="{{ route('public.privacy-policy') }}">Kebijakan Privasi</a>
                <a href="{{ route('public.accessibility') }}">Aksesibilitas</a>
            </div>
            <p>{{ $site->footer_text ?? '© '.date('Y').' Program Studi Ilmu Komputer.' }}</p>
        </div>
    </div>
</footer>
