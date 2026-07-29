@extends('layouts.public')

@section('title', $activity['title'].' | Kegiatan Program Studi Ilmu Komputer')
@section('description', $activity['excerpt'])

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'activities',
        'variant' => 'page-hero-activities activity-detail-hero',
        'kicker' => 'Detail Kegiatan',
        'title' => $activity['title'],
        'description' => $activity['excerpt'],
    ])

    <section class="activity-detail-section internal-section section-space bg-white bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
            <div class="image-frame image-frame-large activity-detail-image relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]" style="background-image: linear-gradient(rgba(0, 36, 58, 0.16), rgba(0, 36, 58, 0.16)), url('{{ asset($activity['image']) }}');" aria-label="Gambar kegiatan {{ $activity['title'] }}"></div>
            <div class="activity-detail-summary relative z-[1] max-w-[640px] pt-2 [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-normal">
                <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Informasi Kegiatan</p>
                <h2>{{ $activity['title'] }}</h2>
                <div class="activity-detail-meta grid gap-2.5 mt-6 text-grey-2 text-[15px] leading-[1.45]" aria-label="Informasi utama kegiatan">
                    <time datetime="{{ $activity['date'] }}" class="flex items-center gap-2">
                        <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                        <span>{{ $activity['date_label'] }}</span>
                    </time>
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-location-dot" aria-hidden="true"></i>
                        <span>{{ $activity['location'] }}</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <i class="fa-solid fa-tag" aria-hidden="true"></i>
                        <span>{{ $activity['category'] ?: 'Umum' }}</span>
                    </span>
                    <div class="activity-share-buttons flex items-center gap-1 mt-1">
                        <button type="button" class="share-button share-button-copy" data-share-copy="{{ route('activities.show', $activity['slug']) }}" title="Salin tautan">
                            <i class="fa-regular fa-copy"></i>
                        </button>
                        <a class="share-button share-button-wa" href="https://wa.me/?text={{ urlencode($activity['title'].' - '.route('activities.show', $activity['slug'])) }}" target="_blank" rel="noopener" title="Bagikan ke WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a class="share-button share-button-ig" href="https://www.instagram.com/share?url={{ urlencode(route('activities.show', $activity['slug'])) }}" target="_blank" rel="noopener" title="Bagikan ke Instagram">
                            <i class="fa-brands fa-instagram"></i>
                        </a>
                        <a class="share-button share-button-fb" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('activities.show', $activity['slug'])) }}" target="_blank" rel="noopener" title="Bagikan ke Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
                <p class="lead-copy mt-[22px] mb-0 text-[17px] font-light">{{ $activity['excerpt'] }}</p>
                <div class="contact-actions activity-detail-actions flex flex-wrap items-center gap-3.5 justify-end max-[1024px]:justify-start max-[560px]:grid max-[560px]:w-full max-[560px]:justify-items-start justify-start mt-[30px]">
                    <a class="button button-blue inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase bg-blue-mid" href="{{ route('activities.index') }}">Kembali ke Kegiatan</a>
                    <a class="button button-light-outline inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase border-2 border-current bg-transparent !bg-transparent" href="mailto:univ.pgriwiranegara@gmail.com">Tanya Panitia</a>
                </div>
            </div>
        </div>
    </section>

    <section class="activity-content-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container activity-content-layout w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[1]">
            <div class="profile-rich-copy activity-content-copy relative z-[1] max-w-[760px] pt-2 max-w-[820px] [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
                <div class="rich-text-content activity-rich-text grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 max-w-[760px] [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold">
                    @foreach ($activity['content_blocks'] as $block)
                        @if ($block['type'] === 'html')
                            {!! $block['html'] !!}
                        @elseif ($block['type'] === 'paragraph')
                            <p>{{ $block['text'] }}</p>
                        @elseif ($block['type'] === 'heading')
                            <h3>{{ $block['text'] }}</h3>
                        @elseif ($block['type'] === 'list')
                            @if (($block['style'] ?? 'ul') === 'ol')
                                <ol>
                                    @foreach ($block['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ol>
                            @else
                                <ul>
                                    @foreach ($block['items'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            @endif
                        @elseif ($block['type'] === 'quote')
                            <blockquote>
                                <p>{{ $block['text'] }}</p>
                            </blockquote>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @include('partials.public.contact-cta', [
        'title' => 'Ingin mengikuti kegiatan berikutnya?',
        'primaryLabel' => 'Lihat Semua Kegiatan',
        'primaryHref' => route('activities.index'),
        'secondaryLabel' => 'Hubungi Prodi',
        'secondaryHref' => 'mailto:univ.pgriwiranegara@gmail.com',
    ])
@endsection

<script>
(function () {
    document.addEventListener('click', function (event) {
        var btn = event.target.closest('[data-share-copy]');
        if (!btn) return;
        var url = btn.getAttribute('data-share-copy');
        navigator.clipboard.writeText(url).then(function () {
            var span = btn.querySelector('span');
            var orig = span.textContent;
            span.textContent = 'Tersalin!';
            setTimeout(function () { span.textContent = orig; }, 2000);
        });
    });
})();
</script>
