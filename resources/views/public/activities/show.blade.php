@extends('layouts.public')

@push('scripts')
<script>
document.getElementById('copy-link-btn')?.addEventListener('click', function () {
    navigator.clipboard.writeText(window.location.href).then(() => {
        const icon = this.querySelector('[data-copy-icon]');
        const check = this.querySelector('[data-check-icon]');
        icon.classList.add('hidden');
        check.classList.remove('hidden');
        this.classList.add('border-gold', 'text-gold');
        setTimeout(() => {
            icon.classList.remove('hidden');
            check.classList.add('hidden');
            this.classList.remove('border-gold', 'text-gold');
        }, 1800);
    });
});
</script>
@endpush

@section('content')
<x-hero title="{{ $activity['title'] }}" :breadcrumbs="['Kegiatan' => route('activities.index'), 'Detail Kegiatan' => null]" :image="asset('assets/images/hero/hero-2.webp')" />

<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div class="grid gap-12 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            {{-- Main Content --}}
            <div>
                <figure class="mb-8 border border-line rounded-xl overflow-hidden bg-white">
                    <img src="{{ $activity['image'] }}" alt="{{ $activity['title'] }}" class="w-full object-cover aspect-video">
                    <figcaption class="px-5 py-4 flex flex-wrap items-center gap-3 text-sm">
                        @if($activity['category'])
                        <span class="rounded-full bg-gold-light px-3 py-1 text-xs font-semibold uppercase tracking-wide text-gold-deep">{{ $activity['category'] }}</span>
                        @endif
                        <span class="text-muted">{{ $activity['date_label'] }}</span>
                        @if($activity['location'])
                        <span class="text-muted">· {{ $activity['location'] }}</span>
                        @endif
                    </figcaption>
                </figure>

                @if($activity['excerpt'])
                <p class="mb-8 text-lg leading-relaxed text-ink/80 font-medium">{{ $activity['excerpt'] }}</p>
                @endif

                @if(!empty($activity['content_blocks']))
                <div class="rich-text">
                    @foreach($activity['content_blocks'] as $block)
                        @if($block['type'] === 'html')
                            {!! $block['html'] !!}
                        @elseif($block['type'] === 'paragraph')
                            <p>{{ $block['text'] }}</p>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Sidebar — sticky --}}
            <aside>
                <div style="position: sticky; top: 7rem;">
                    <div class="rounded-xl border border-line bg-white p-5">
                        <h4 class="font-display text-lg font-bold text-primary mb-4">Informasi Kegiatan</h4>
                        <dl class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <dt class="text-muted">Tanggal</dt>
                                <dd class="font-medium text-ink">{{ $activity['date_label'] }}</dd>
                            </div>
                            @if($activity['location'])
                            <div class="flex justify-between">
                                <dt class="text-muted">Lokasi</dt>
                                <dd class="font-medium text-ink">{{ $activity['location'] }}</dd>
                            </div>
                            @endif
                            @if($activity['category'])
                            <div class="flex justify-between">
                                <dt class="text-muted">Kategori</dt>
                                <dd class="font-medium text-ink">{{ $activity['category'] }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    <div class="mt-6 rounded-xl bg-primary p-6 text-cream">
                        <h4 class="font-display text-lg font-bold">Siap Bergabung?</h4>
                        <p class="mt-2 text-sm text-cream/75">Jadi bagian dari komunitas {{ $site?->site_name ?: 'Ilmu Komputer' }}.</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactInfo?->phone ?: '') }}" target="_blank" rel="noopener" class="btn btn-gold btn-md mt-4 w-full">Hubungi via WhatsApp</a>
                    </div>

                    <div class="mt-6 rounded-xl border border-line bg-white p-5">
                        <h4 class="font-display text-sm font-bold text-primary uppercase tracking-wide mb-4">Bagikan Kegiatan</h4>
                        <div class="flex items-center gap-3">
                            <a href="https://wa.me/?text={{ urlencode($activity['title'] . ' — ' . request()->url()) }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#25D366] text-white transition-transform hover:scale-110" aria-label="Bagikan ke WhatsApp" title="WhatsApp">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                            </a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#1877F2] text-white transition-transform hover:scale-110" aria-label="Bagikan ke Facebook" title="Facebook">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </a>
                            <a href="https://www.instagram.com/" target="_blank" rel="noopener" class="flex h-10 w-10 items-center justify-center rounded-full bg-[#E4405F] text-white transition-transform hover:scale-110" aria-label="Bagikan ke Instagram" title="Instagram">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069ZM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0Zm0 5.838a6.162 6.162 0 1 0 0 12.324 6.162 6.162 0 0 0 0-12.324ZM12 16a4 4 0 1 1 0-8 4 4 0 0 1 0 8Zm6.406-11.845a1.44 1.44 0 1 0 0 2.881 1.44 1.44 0 0 0 0-2.881Z"/></svg>
                            </a>
                            <button type="button" id="copy-link-btn" class="flex h-10 w-10 items-center justify-center rounded-full border border-muted/30 text-muted transition-all hover:border-primary hover:text-primary" aria-label="Salin tautan" title="Salin Tautan">
                                <svg data-copy-icon class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/></svg>
                                <svg data-check-icon class="hidden h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>

@if(!empty($otherActivities))
<section class="bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-8 lg:px-16 xl:px-0">
        <div data-reveal class="mb-10 anim-fade-up">
            <h3 class="inline-flex items-center gap-2.5 uppercase tracking-widest text-primary text-base font-semibold">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/></svg>
                Kegiatan Lainnya
            </h3>
            <h2 class="mt-4 font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl">Rekomendasi Untuk Anda</h2>
        </div>
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
            @foreach($otherActivities as $other)
                <x-activity-card :activity="$other" />
            @endforeach
        </div>
    </div>
</section>
@endif

<x-cta-banner />
@endsection
