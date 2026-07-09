@extends('layouts.public')

@section('title', 'Dokumen Program Studi Ilmu Komputer')
@section('description', 'Dokumen Program Studi Ilmu Komputer untuk kurikulum, panduan akademik, SOP, dan akreditasi.')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'documents',
        'variant' => 'page-hero-documents',
        'kicker' => 'Dokumen Program Studi',
        'title' => 'Pusat Dokumen Ilmu Komputer',
        'description' => 'Kumpulan dokumen untuk kurikulum, panduan akademik, SOP, akreditasi, pelatihan TIK, dan administrasi Program Studi Ilmu Komputer.',
    ])

    <section class="documents-intro-section internal-section section-space relative overflow-hidden py-20 max-[560px]:py-16">
        <div class="container internal-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] gap-[clamp(34px,6vw,72px)] items-start max-[1024px]:grid-cols-1">
            <div class="content-panel p-[clamp(28px,4vw,46px)] [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-normal [&_p]:text-grey-2 [&_p]:leading-[1.65]">
                <h2>Dokumen Resmi Prodi</h2>
                <p class="lead-copy mt-[22px] mb-0 text-[17px] font-light">Halaman ini disiapkan sebagai pusat dokumen Program Studi Ilmu Komputer, termasuk kebutuhan kurikulum, peminatan, sertifikasi, PKL, dan layanan akademik.</p>
                <p>Struktur dokumen dikelompokkan berdasarkan kebutuhan akademik, administrasi, kurikulum, kegiatan, dan akreditasi agar mudah dicari kembali.</p>
            </div>
        </div>
    </section>

    <section class="documents-list-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
        <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
            <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Daftar Dokumen</p>
            <h2 class="internal-heading m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">Berkas yang Tersedia</h2>
            <div class="filter-row flex flex-wrap gap-3 mt-7" aria-label="Kategori dokumen">
                @foreach (['Semua', 'Kurikulum', 'Akademik', 'SOP', 'Akreditasi', 'Kegiatan', 'Alumni'] as $filter)
                    <span class="filter-pill{{ $loop->first ? ' is-active' : '' }} inline-flex items-center min-h-10 px-[18px] text-blue-dark text-[13px] font-bold bg-white border border-[rgba(0,36,58,0.12)]">{{ $filter }}</span>
                @endforeach
            </div>

            <div class="document-grid grid grid-cols-3 gap-6 mt-9 max-[1024px]:grid-cols-1">
                @foreach ($documents as $document)
                    <article class="document-card grid gap-[18px] min-h-[280px] p-7 bg-[#f0f2f4] [&_h3]:m-0 [&_h3]:text-blue-dark [&_h3]:font-body [&_h3]:text-xl [&_h3]:font-bold [&_h3]:leading-[1.15] [&_p]:m-0 [&_p]:text-grey-2 [&_p]:leading-[1.6]" data-document-status="published" data-document-category="{{ $document['category'] }}" data-document-file="{{ $document['file'] }}" data-document-file-type="{{ $document['file_type'] }}" data-document-file-size="{{ $document['file_size'] }}" data-document-updated-at="{{ $document['updated_at'] }}">
                        <i class="fa-solid {{ $document['icon'] }} inline-block w-auto h-auto text-blue-mid text-[32px] bg-transparent" aria-hidden="true"></i>
                        <div>
                            <span class="document-type inline-block text-red text-xs font-bold tracking-[0.06em] uppercase">{{ $document['category'] }}</span>
                            <h3>{{ $document['title'] }}</h3>
                        </div>
                        <p>{{ $document['description'] }}</p>
                        <div class="document-meta grid gap-1.5 text-grey-2 text-[13px]">
                            <span class="document-updated"><i class="fa-regular fa-calendar" aria-hidden="true"></i> Diperbarui {{ $document['updated_label'] }}</span>
                            <span class="document-file-type"><i class="fa-solid {{ $document['file_icon'] }}" aria-hidden="true"></i> {{ $document['file_type'] }}</span>
                            <span class="document-file-size"><i class="fa-solid fa-hard-drive" aria-hidden="true"></i> {{ $document['file_size'] }}</span>
                        </div>
                        <div class="document-actions flex flex-wrap gap-2.5 items-center mt-1">
                            <a class="document-link inline-flex items-center gap-2 min-h-[38px] px-3.5 text-white text-xs font-bold bg-blue-mid" href="#" aria-label="Unduh {{ $document['title'] }}"><i class="fa-solid fa-download" aria-hidden="true"></i> Unduh</a>
                            <a class="document-link secondary inline-flex items-center gap-2 min-h-[38px] px-3.5 text-white text-xs font-bold bg-blue-mid text-blue-dark bg-grey-5" href="#" aria-label="Lihat {{ $document['title'] }}"><i class="fa-regular fa-eye" aria-hidden="true"></i> Lihat</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.public.contact-cta', [
        'title' => 'Butuh dokumen lain?',
        'primaryLabel' => 'Hubungi Prodi',
        'secondaryLabel' => 'Lihat Profil',
        'secondaryHref' => route('profile'),
    ])
@endsection
