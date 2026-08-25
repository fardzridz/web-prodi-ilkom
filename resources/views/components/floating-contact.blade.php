<div id="floating-contact" class="fixed bottom-6 right-6 z-40">
    <div id="contact-popover" class="fc-popover absolute bottom-full right-0 mb-4 w-[19rem] origin-bottom-right rounded-2xl bg-white p-5 shadow-[0_20px_50px_-12px_rgba(10,17,29,.28)] ring-1 ring-ink/5" role="dialog" aria-label="Kontak cepat">
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold/20 text-primary">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z"/></svg>
                </span>
                <div>
                    <h3 class="font-display text-sm font-bold leading-snug text-primary">Hubungi Kami</h3>
                    <p class="text-xs text-muted">Ada yang bisa kami bantu?</p>
                </div>
            </div>
            <button type="button" data-contact-close aria-label="Tutup" class="-mr-1 -mt-1 flex h-8 w-8 shrink-0 cursor-pointer items-center justify-center rounded-full text-muted transition-colors hover:bg-line hover:text-primary">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="mt-4 space-y-1">
            @if($contactInfo?->phone)
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactInfo->phone) }}" target="_blank" rel="noopener" class="group flex items-center gap-3 rounded-xl p-2.5 transition-colors hover:bg-line">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#25D366] text-white transition-transform group-hover:scale-105">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-xs font-semibold text-primary">WhatsApp</span>
                    <span class="block truncate text-sm text-muted">{{ $contactInfo->phone }}</span>
                </span>
            </a>
            @endif
            @if($contactInfo?->email)
            <a href="mailto:{{ $contactInfo->email }}" class="group flex items-center gap-3 rounded-xl p-2.5 transition-colors hover:bg-line">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-primary text-cream transition-transform group-hover:scale-105">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-xs font-semibold text-primary">Email</span>
                    <span class="block truncate text-sm text-muted">{{ $contactInfo->email }}</span>
                </span>
            </a>
            @endif
            @if($contactInfo?->phone)
            <a href="tel:{{ $contactInfo->phone }}" class="group flex items-center gap-3 rounded-xl p-2.5 transition-colors hover:bg-line">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gold text-primary transition-transform group-hover:scale-105">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                </span>
                <span class="min-w-0">
                    <span class="block text-xs font-semibold text-primary">Telepon</span>
                    <span class="block truncate text-sm text-muted">{{ $contactInfo->phone }}</span>
                </span>
            </a>
            @endif
        </div>

        @if($contactInfo?->facebook || $contactInfo?->instagram || $contactInfo?->youtube)
        <div class="mt-4 border-t border-line pt-4">
            <p class="text-[0.7rem] font-semibold uppercase tracking-wider text-muted">Ikuti Kami</p>
            <div class="mt-2.5 flex items-center gap-2">
                @if($contactInfo?->facebook)
                <a href="{{ $contactInfo->facebook }}" aria-label="Facebook" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-light text-primary transition-colors hover:bg-primary hover:text-cream">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M22 12a10 10 0 1 0-11.56 9.88v-6.75H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.9h-2.34v6.99A10 10 0 0 0 22 12Z"/></svg>
                </a>
                @endif
                @if($contactInfo?->instagram)
                <a href="{{ $contactInfo->instagram }}" aria-label="Instagram" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-light text-primary transition-colors hover:bg-primary hover:text-cream">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3.5" y="3.5" width="17" height="17" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17" cy="7" r="1.1" fill="currentColor" stroke="none"/></svg>
                </a>
                @endif
                @if($contactInfo?->youtube)
                <a href="{{ $contactInfo->youtube }}" aria-label="YouTube" target="_blank" rel="noopener" class="flex h-9 w-9 items-center justify-center rounded-full bg-primary-light text-primary transition-colors hover:bg-primary hover:text-cream">
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M23.5 7.19a3.02 3.02 0 0 0-2.12-2.14C19.5 4.55 12 4.55 12 4.55s-7.5 0-9.38.5A3.02 3.02 0 0 0 .5 7.2 31.6 31.6 0 0 0 0 12a31.6 31.6 0 0 0 .5 4.81 3.02 3.02 0 0 0 2.12 2.14c1.87.5 9.38.5 9.38.5s7.5 0 9.38-.5a3.02 3.02 0 0 0 2.12-2.14A31.6 31.6 0 0 0 24 12a31.6 31.6 0 0 0-.5-4.81ZM9.55 15.02V8.98L15.82 12l-6.27 3.02Z"/></svg>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    <button type="button" data-contact-toggle aria-expanded="false" aria-controls="contact-popover" aria-label="Buka kontak cepat" class="relative flex h-14 w-14 cursor-pointer items-center justify-center rounded-full bg-[#25D366] text-white shadow-[0_12px_28px_-6px_rgba(37,211,102,.55)] transition-all duration-200 hover:scale-110 active:scale-95">
        <span class="fc-ping pointer-events-none absolute inset-0 rounded-full bg-[#25D366]" aria-hidden="true"></span>
        <svg class="relative h-7 w-7" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
    </button>
</div>
