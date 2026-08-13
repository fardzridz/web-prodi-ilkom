@props(['heading' => 'Siap Membangun Masa Depan di Ilmu Komputer?'])

<section class="relative z-10 bg-line py-16 lg:py-24">
    <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-16 xl:px-0">
        <div class="rounded-2xl bg-primary px-6 py-12 text-left text-cream sm:px-12 lg:flex lg:items-center lg:justify-between lg:gap-12 lg:px-14 anim-fade-up anim-delay-1" data-reveal>
            <div class="max-w-3xl">
                <p class="inline-flex items-center gap-2.5 uppercase tracking-widest text-gold text-base font-semibold">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5" />
                    </svg>
                    Penerimaan Mahasiswa Baru
                </p>
                <h2 class="mt-4 font-display text-2xl font-bold leading-tight uppercase tracking-wide text-cream sm:text-3xl">
                    {{ $heading }}
                </h2>
            </div>
            <a href="https://admisi.uniwara.ac.id" target="_blank" rel="noopener" class="btn btn-gold btn-lg mt-8 shrink-0 lg:mt-0">
                <span class="btn-label">Daftar Disini</span>
                <svg class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                </svg>
            </a>
        </div>
    </div>
</section>
