@extends('layouts.public')

@section('title', 'Profil Program Studi Ilmu Komputer')
@section('description', 'Profil Program Studi Ilmu Komputer, misi, tujuan, kompetensi lulusan, dan program unggulan.')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'profile',
        'kicker' => 'Profil Program Studi',
        'title' => 'Ilmu Komputer',
        'description' => 'Mengenal identitas, misi, tujuan, kompetensi, dan program unggulan Program Studi Ilmu Komputer Universitas PGRI Wiranegara.',
    ])

    <section class="profile-intro-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
        <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
            <div class="image-frame image-frame-large placeholder-campus relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
            <div class="copy-block [&_h2]:m-0 [&_h2]:text-blue-dark [&_h2]:font-display [&_h2]:text-[length:var(--hero-heading-size)] [&_h2]:font-medium [&_h2]:leading-[0.95] [&_h2]:tracking-[-0.035em] max-[560px]:[&_h2]:text-[34px] [&>p]:max-w-[450px] [&>p]:my-6 [&>p]:text-grey-2 [&>p]:text-[17px] [&>p]:leading-[1.65] [&>p]:tracking-[-0.01em]">
                <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Tentang Prodi</p>
                <h2>Profil Singkat</h2>
                <div class="rich-text-content grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="description">
                    <p>Program Studi Ilmu Komputer menyiapkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, dapat dipercaya, mampu bekerja sama, dan siap berkontribusi secara nasional maupun internasional.</p>
                    <p>Pembelajaran diarahkan pada pengembangan ilmu komputer melalui pendidikan, penelitian, pengabdian, kerja sama, serta penerapan teknologi informasi yang bermanfaat bagi masyarakat.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-history-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
            <div class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
                <h2>Sejarah Prodi</h2>
                <div class="rich-text-content grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="history">
                    <p>Program Studi Ilmu Komputer berkembang sebagai ruang akademik yang menjawab kebutuhan tenaga teknologi informasi di Pasuruan dan sekitarnya. Kurikulum, aktivitas mahasiswa, dan kerja sama terus disesuaikan dengan kebutuhan dunia digital.</p>
                    <blockquote>
                        <p>Perjalanan prodi dibangun melalui pendidikan, riset, pengabdian, dan kolaborasi yang dekat dengan kebutuhan masyarakat.</p>
                    </blockquote>
                    <p>Penguatan laboratorium, kegiatan praktikum, dan pembelajaran berbasis proyek menjadi bagian dari perkembangan prodi untuk menyiapkan mahasiswa menghadapi perubahan teknologi.</p>
                </div>
            </div>
            <div class="profile-visual-stack relative z-[1] grid gap-5 content-start">
                <div class="image-frame image-frame-large profile-history-visual relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
            </div>
        </div>
    </section>

    <section id="visi-misi-page" class="profile-core-section core-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container core-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-start max-[1024px]:grid-cols-1">
            <div class="profile-core-copy">
                <h2>Visi <span>Prodi</span></h2>
                <div class="rich-text-content grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="vision">
                    <blockquote>
                        <p>Menjadi Program Studi Ilmu Komputer yang unggul dalam pengembangan teknologi informasi, berjiwa entrepreneur, dan berkontribusi bagi masyarakat.</p>
                    </blockquote>
                    <p>Visi ini menjadi arah pengembangan akademik, riset, pengabdian, dan kerja sama prodi dalam membentuk lulusan yang adaptif terhadap perkembangan teknologi.</p>
                </div>
            </div>
            <div class="profile-rich-copy profile-mission-box relative z-[1] max-w-[760px] pt-2 self-center py-2 max-[560px]:py-1 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
                <p class="eyebrow m-0 mb-2.5 text-red text-[13px] font-bold tracking-[0.045em] uppercase">Misi Utama</p>
                <div class="rich-text-content grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="mission">
                    <ul>
                        <li>Menghasilkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, dapat dipercaya, dan mampu bekerja sama.</li>
                        <li>Mengembangkan ilmu pengetahuan teknologi informasi dan komputer melalui tridarma perguruan tinggi.</li>
                        <li>Memberikan pelayanan kepada civitas akademika dan masyarakat melalui pendidikan, penelitian, dan pengabdian.</li>
                        <li>Menjalin kerja sama serta menghasilkan produk inovasi dan kreasi di bidang teknologi informasi dan komputer.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-band scholarship-section relative overflow-hidden min-h-[252px] text-blue-dark bg-[rgba(253,185,19,0.9)] max-[560px]:min-h-[236px]">
        <div class="scholarship-media absolute inset-0 z-0" aria-hidden="true"></div>
        <div class="container scholarship-content profile-band-content w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] relative z-[2] grid min-h-[252px] content-center py-[34px] max-[560px]:min-h-[236px] max-[560px]:py-8 grid-cols-[minmax(0,1fr)_minmax(280px,0.64fr)] gap-[clamp(24px,5vw,64px)] items-center max-[560px]:grid-cols-1 max-[560px]:gap-4 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>p]:max-w-[590px] [&>p]:mt-3.5 [&>p]:mb-0 [&>p]:text-[rgba(0,36,58,0.84)] [&>p]:text-[17px]">
            <h2>Badge Akreditasi</h2>
            <div class="profile-band-accreditation max-w-[520px] text-blue-dark">
                <span class="accreditation-kicker inline-block text-[rgba(0,36,58,0.72)] text-xs font-extrabold tracking-[0.08em] uppercase">Status Mutu</span>
                <div class="rich-text-content profile-band-rich grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 gap-2 mt-2.5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="accreditation">
                    <p><strong>Terakreditasi Baik Sekali</strong></p>
                    <p>Status akreditasi menjadi penanda komitmen prodi dalam menjaga mutu akademik, layanan pembelajaran, dan pengembangan kegiatan tridarma.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="profile-goals-section internal-section section-space relative overflow-hidden bg-[#f8f9fa] py-20 max-[560px]:py-16">
        <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
            <div class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
                <h2 class="internal-heading m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">Arah Pembelajaran</h2>
                <div class="rich-text-content grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="goals">
                    <ol>
                        <li>Menghasilkan lulusan berjiwa Pancasila, berintegritas, dan memiliki jiwa entrepreneurship.</li>
                        <li>Membekali mahasiswa dengan pengetahuan dan keterampilan teknologi sesuai bidang keahliannya.</li>
                        <li>Mendorong penelitian, pengabdian kepada masyarakat, dan kerja sama yang memberi manfaat nyata.</li>
                    </ol>
                    <p>Tujuan prodi diterjemahkan dalam pembelajaran terapan, kegiatan akademik, dan penguatan portofolio mahasiswa.</p>
                </div>
            </div>
            <div class="profile-visual-stack relative z-[1] grid gap-5 content-start">
                <div class="image-frame image-frame-large profile-goals-visual relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
            </div>
        </div>
    </section>

    <section class="profile-advantages-section profile-focus-section section-space relative overflow-hidden bg-white py-20 max-[560px]:py-16">
        <div class="container split-grid w-[min(100%_-_48px,var(--container))] mx-auto max-[560px]:w-[min(100%_-_32px,var(--container))] grid grid-cols-2 gap-[clamp(38px,6vw,76px)] items-center max-[1024px]:grid-cols-1">
            <div class="profile-visual-stack relative z-[1] grid gap-5 content-start">
                <div class="image-frame image-frame-large profile-advantages-visual relative min-h-[250px] overflow-hidden bg-[linear-gradient(135deg,rgba(255,255,255,0.18),transparent_42%),linear-gradient(135deg,#244761,#7aa8bb)] min-h-[520px] max-[1024px]:min-h-[360px]"></div>
            </div>
            <div class="profile-rich-copy relative z-[1] max-w-[760px] pt-2 [&>h2]:m-0 [&>h2]:text-blue-dark [&>h2]:font-display [&>h2]:text-[length:var(--hero-heading-size)] [&>h2]:font-medium [&>h2]:leading-[0.95] [&>h2]:tracking-normal">
                <h2 class="internal-heading m-0 text-blue-dark font-display text-[length:var(--hero-heading-size)] font-medium leading-[0.95] tracking-normal">Keunggulan, Rumpun Peminatan, Program Unggulan</h2>
                <div class="rich-text-content grid gap-4 mt-6 text-grey-2 max-[560px]:gap-3.5 max-[560px]:mt-5 [&>*]:m-0 [&_p]:text-grey-2 [&_p]:text-[17px] max-[560px]:[&_p]:text-base [&_p]:leading-[1.72] [&_p]:tracking-[-0.01em] [&_strong]:text-blue-dark [&_strong]:font-extrabold" data-profile-field="advantages">
                    <h3>Keunggulan</h3>
                    <ul>
                        <li>Pembelajaran diarahkan pada penguatan kompetensi teknologi informasi dan komputer yang relevan dengan kebutuhan industri.</li>
                        <li>Mahasiswa didorong memiliki portofolio, pengalaman kerja sama, dan kemampuan menyelesaikan masalah berbasis teknologi.</li>
                    </ul>
                    <h3>Rumpun Peminatan</h3>
                    <ul>
                        <li>Komputer berbasis jaringan dan infrastruktur teknologi informasi.</li>
                        <li>Komputasi cerdas, visualisasi, dan pengolahan informasi.</li>
                        <li>Rekayasa perangkat lunak, sistem informasi, multimedia, dan game.</li>
                    </ul>
                    <h3>Program Unggulan</h3>
                    <p>Mahasiswa memperoleh pengalaman belajar terapan melalui pemilihan bidang kompetensi, training, sertifikasi, magang, serta praktik kerja lapangan di instansi atau perusahaan yang relevan.</p>
                </div>
                <div class="profile-section-cta flex flex-wrap gap-3.5 mt-7 max-[560px]:grid max-[560px]:justify-items-start">
                    <a class="button button-red inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase bg-red" href="{{ route('activities.index') }}">Lihat Kegiatan Prodi</a>
                    <a class="button button-light-outline inline-flex min-h-[54px] items-center justify-center pt-[19px] pr-[37px] pb-[14px] pl-[37px] text-white text-[15px] font-bold leading-[1.1] tracking-[0.03em] uppercase border-2 border-current bg-transparent !bg-transparent" href="{{ route('documents') }}">Dokumen Akademik</a>
                </div>
            </div>
        </div>
    </section>

    @include('partials.public.contact-cta', [
        'title' => 'Ingin mengenal prodi lebih lanjut?',
        'secondaryLabel' => 'Lihat Kegiatan',
        'secondaryHref' => route('activities.index'),
    ])
@endsection
