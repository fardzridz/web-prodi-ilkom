@extends('layouts.public')

@section('title', 'Dosen - Program Studi Ilmu Komputer')
@section('description', 'Daftar dosen aktif Program Studi Ilmu Komputer.')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'lecturers',
        'variant' => 'page-hero-lecturers',
        'kicker' => 'Data Prodi',
        'title' => 'Dosen Program Studi',
        'description' => 'Daftar dosen aktif beserta bidang keahlian dan kontak akademik Program Studi Ilmu Komputer.',
    ])

    <section class="lecturer-intro-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container internal-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-[minmax(0,1.05fr)_minmax(320px,0.95fr)] gap-[clamp(34px,6vw,72px)] items-start max-[1024px]:grid-cols-1">
            <div class="content-panel bg-white p-[clamp(28px,4vw,46px)] [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-normal [&_p]:text-grey-2 [&_p]:leading-[1.65]">
                <h2>Bidang Keahlian Dosen</h2>
                <p class="mt-[22px] mb-0">Dosen prodi mendukung pembelajaran pada bidang rekayasa perangkat lunak, jaringan komputer, sistem cerdas, data, multimedia, dan sistem informasi.</p>
            </div>
        </div>
    </section>

    <section class="lecturer-list-section section-space bg-white py-20 max-[560px]:py-16">
        <div class="container w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))]">
            <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Data Dosen</p>
            <h2 class="internal-heading m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">Dosen Aktif</h2>
            <p class="lead-copy mt-[22px] mb-0 text-[17px] font-light">Dosen aktif yang mengampu perkuliahan dan membimbing mahasiswa di Program Studi Ilmu Komputer.</p>

            <div class="lecturer-grid grid grid-cols-3 gap-6 mt-9 max-[1024px]:grid-cols-1">
                @foreach ($lecturers as $lecturer)
                    <article
                        class="lecturer-card grid gap-5 min-h-[330px] p-7 bg-[#f0f2f4] [&_h3]:m-0 [&_h3]:text-blue-dark [&_h3]:font-body [&_h3]:text-xl [&_h3]:font-bold [&_h3]:leading-[1.15] [&_p]:m-0 [&_p]:text-grey-2 [&_p]:leading-[1.6]"
                        data-lecturer-status="active"
                        data-lecturer-sort-order="{{ $lecturer['sort_order'] }}"
                        data-lecturer-name="{{ $lecturer['name'] }}"
                        data-lecturer-nidn="{{ $lecturer['nidn'] }}"
                        data-lecturer-position="{{ $lecturer['position'] }}"
                        data-lecturer-expertise="{{ $lecturer['expertise'] }}"
                        data-lecturer-education="{{ $lecturer['education'] }}"
                        data-lecturer-email="{{ $lecturer['email'] }}"
                        data-lecturer-photo="{{ $lecturer['image'] }}"
                    >
                        <div
                            class="lecturer-avatar w-[74px] h-[74px] overflow-hidden bg-[linear-gradient(135deg,var(--blue-mid),var(--yellow))] bg-center bg-cover"
                            role="img"
                            aria-label="Foto {{ $lecturer['name'] }}"
                            style="background-image: linear-gradient(rgba(0, 36, 58, 0.08), rgba(0, 36, 58, 0.08)), url('{{ asset($lecturer['image']) }}');"
                        ></div>
                        <div>
                            <span class="lecturer-role inline-block text-red text-xs font-bold tracking-[0.06em] uppercase">{{ $lecturer['position'] }}</span>
                            <h3>{{ $lecturer['name'] }}</h3>
                        </div>
                        <p>{{ $lecturer['description'] }}</p>
                        <div class="lecturer-meta grid gap-1.5 text-grey-2 text-[13px]">
                            <span><i class="fa-solid fa-id-badge" aria-hidden="true"></i> NIDN {{ $lecturer['nidn'] }}</span>
                            <span><i class="{{ $lecturer['icon'] }}" aria-hidden="true"></i> {{ $lecturer['expertise_short'] ?? $lecturer['expertise'] }}</span>
                            <span><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i> {{ $lecturer['education'] }}</span>
                            <a href="mailto:{{ $lecturer['email'] }}"><i class="fa-solid fa-envelope" aria-hidden="true"></i> {{ $lecturer['email'] }}</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    @include('partials.public.contact-cta', [
        'title' => 'Hubungi Program Studi Ilmu Komputer',
        'secondaryLabel' => 'Lihat Dokumen',
        'secondaryHref' => route('documents'),
    ])
@endsection
