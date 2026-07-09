@php
    $variant ??= '';
    $kicker ??= 'Program Studi Ilmu Komputer';
    $title ??= 'Program Studi Ilmu Komputer';
    $description ??= null;
    $active ??= null;
@endphp

<section class="page-hero {{ $variant }} relative h-[360px] min-h-[360px] overflow-hidden text-white max-[560px]:h-[320px] max-[560px]:min-h-[320px] [&_h1]:max-w-[820px] [&_h1]:m-0 [&_h1]:font-display [&_h1]:text-[length:var(--hero-heading-size)] [&_h1]:font-medium [&_h1]:leading-[0.95] [&_h1]:tracking-normal [&_p:not(.page-kicker)]:max-w-[620px] [&_p:not(.page-kicker)]:mt-[22px] [&_p:not(.page-kicker)]:mb-0 [&_p:not(.page-kicker)]:text-[#dbe6ed] [&_p:not(.page-kicker)]:text-lg [&_p:not(.page-kicker)]:font-light [&_p:not(.page-kicker)]:leading-[1.65]">
    <div class="container page-hero-content w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[1] grid h-full min-h-0 content-center py-[72px] max-[560px]:py-[52px]">
        <p class="page-kicker m-0 mb-3 text-yellow text-[13px] font-bold tracking-[0.08em] uppercase">{{ $kicker }}</p>
        <h1>{{ $title }}</h1>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>
</section>

@include('partials.public.floating-nav', ['active' => $active])
