@php
    $title ??= 'Hubungi Program Studi Ilmu Komputer';
    $primaryLabel ??= 'Kirim Email';
    $primaryHref ??= 'mailto:univ.pgriwiranegara@gmail.com';
    $secondaryLabel ??= 'WhatsApp Prodi';
    $secondaryHref ??= 'https://wa.me/6282141554377';
@endphp

<section id="{{ $id ?? 'kontak-section' }}" class="contact-section scholarship-section relative overflow-hidden h-[188px] min-h-0 my-16 text-white bg-[rgba(150,26,38,0.92)] max-[1024px]:h-[220px] max-[560px]:h-[256px]">
    <div class="scholarship-media contact-media absolute inset-0 z-0" aria-hidden="true"></div>
    <div class="container scholarship-content contact-content w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[2] grid h-full min-h-0 grid-cols-[minmax(0,1fr)_auto] gap-[clamp(22px,4vw,56px)] items-center py-0 max-[1024px]:grid-cols-1 max-[1024px]:content-center max-[1024px]:gap-5">
        <div class="min-w-0">
            <h2 class="contact-title internal-heading m-0 text-white font-display text-[clamp(24px,2vw,30px)] font-medium leading-[1.05] tracking-normal whitespace-nowrap max-[1024px]:whitespace-normal max-[560px]:text-[24px]">{{ $title }}</h2>
        </div>
        <div class="contact-actions flex flex-nowrap items-center gap-3 justify-end max-[1024px]:justify-start max-[560px]:flex-wrap max-[560px]:gap-2.5">
            <a class="contact-button contact-button-primary inline-flex h-11 items-center justify-center px-6 text-blue-dark text-[13px] font-bold leading-none tracking-[0.03em] uppercase bg-yellow transition-[color,background-color,border-color,transform] duration-[180ms] ease-[ease] max-[560px]:w-full" href="{{ $primaryHref }}">{{ $primaryLabel }}</a>
            <a class="contact-button contact-button-secondary inline-flex h-11 items-center justify-center px-6 text-yellow text-[13px] font-bold leading-none tracking-[0.03em] uppercase border-2 border-yellow bg-transparent transition-[color,background-color,border-color,transform] duration-[180ms] ease-[ease] max-[560px]:w-full" href="{{ $secondaryHref }}">{{ $secondaryLabel }}</a>
        </div>
    </div>
</section>
