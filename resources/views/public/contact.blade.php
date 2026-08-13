@extends('layouts.public')

@section('title', 'Kontak — ' . ($site?->site_name ?: 'Program Studi Ilmu Komputer'))

@push('scripts')
<script src="{{ asset('js/app.js') }}"></script>
@endpush

@section('content')
<x-hero title="Kontak" :breadcrumbs="['Kontak' => null]">
    Hubungi kami untuk informasi lebih lanjut tentang {{ $site?->site_name ?: 'Program Studi Ilmu Komputer' }}.
</x-hero>

<section class="bg-line py-16 lg:py-24">
    <div class="mx-4 px-4 sm:mx-8 sm:px-8 lg:mx-16 lg:px-20">
        <div class="mx-auto grid max-w-6xl gap-12 md:grid-cols-2 md:gap-16">
            <div data-reveal class="anim-fade-up anim-delay-1">
                <h2 class="font-display text-3xl font-bold uppercase tracking-wide text-primary sm:text-4xl">Hubungi Kami</h2>
                <p class="mt-4 text-ink/70">Isi formulir di samping atau kunjungi kami langsung di kampus.</p>

                <div class="mt-8 space-y-6">
                    @if($contactInfo?->address)
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-light text-primary">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-primary">Alamat</h4>
                            <p class="mt-1 text-sm text-muted">{{ $contactInfo->address }}</p>
                        </div>
                    </div>
                    @endif
                    @if($contactInfo?->email)
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-light text-primary">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-primary">Email</h4>
                            <a href="mailto:{{ $contactInfo->email }}" class="mt-1 block text-sm text-muted hover:text-primary transition-colors">{{ $contactInfo->email }}</a>
                        </div>
                    </div>
                    @endif
                    @if($contactInfo?->phone)
                    <div class="flex items-start gap-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary-light text-primary">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-primary">Telepon / WhatsApp</h4>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactInfo->phone) }}" target="_blank" rel="noopener" class="mt-1 block text-sm text-muted hover:text-primary transition-colors">{{ $contactInfo->phone }}</a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div data-reveal class="anim-fade-up anim-delay-2">
                <form action="{{ route('contact.store') }}" method="POST" class="rounded-xl border border-line bg-white p-6 sm:p-8">
                    @csrf
                    @if(session('success'))
                    <div class="mb-6 rounded-lg bg-success-light px-4 py-3 text-sm font-medium text-success-dark">{{ session('success') }}</div>
                    @endif

                    <div class="space-y-5">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-primary">Nama Lengkap</label>
                            <input type="text" name="name" id="name" required class="mt-1.5 w-full rounded-md border border-line bg-white px-4 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition" value="{{ old('name') }}">
                            @error('name')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-semibold text-primary">Email</label>
                            <input type="email" name="email" id="email" required class="mt-1.5 w-full rounded-md border border-line bg-white px-4 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition" value="{{ old('email') }}">
                            @error('email')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="message" class="block text-sm font-semibold text-primary">Pesan</label>
                            <textarea name="message" id="message" rows="5" required class="mt-1.5 w-full rounded-md border border-line bg-white px-4 py-2.5 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">{{ old('message') }}</textarea>
                            @error('message')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary btn-md w-full" id="contact-submit-btn">
                            <span class="btn-label" id="contact-btn-text">Kirim Pesan</span>
                            <svg id="contact-btn-icon" class="btn-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                            <svg id="contact-btn-spinner" class="hidden h-5 w-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </button>
                </form>
            </div>
        </div>
    </div>
</section>

<x-cta-banner />
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.querySelector('form[action*="contact"]');
    if (!form) return;
    form.addEventListener('submit', function () {
        var btn = document.getElementById('contact-submit-btn');
        var text = document.getElementById('contact-btn-text');
        var icon = document.getElementById('contact-btn-icon');
        var spinner = document.getElementById('contact-btn-spinner');
        btn.disabled = true;
        btn.classList.add('opacity-70', 'cursor-not-allowed');
        if (icon) icon.classList.add('hidden');
        if (spinner) spinner.classList.remove('hidden');
    });
});
</script>
@endpush
