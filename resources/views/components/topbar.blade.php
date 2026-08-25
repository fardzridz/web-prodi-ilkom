<div class="relative z-10 bg-primary text-white text-xs transition-transform duration-300 ease-out will-change-transform" data-topbar>
    <div class="anim-fade-down anim-delay-1 mx-4 px-4 py-1 flex flex-wrap items-center justify-end gap-x-5 gap-y-1 sm:mx-8 sm:px-8 sm:gap-x-8 lg:mx-16 lg:px-20">
        @if($contactInfo?->phone)
        <a href="tel:{{ $contactInfo->phone }}" class="inline-flex items-center gap-1.5 text-white hover:text-gold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
            </svg>
            {{ $contactInfo->phone }}
        </a>
        @endif
        @if($contactInfo?->email)
        <a href="mailto:{{ $contactInfo->email }}" class="inline-flex items-center gap-1.5 text-white hover:text-gold transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-3.5 w-3.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
            </svg>
            {{ $contactInfo->email }}
        </a>
        @endif
    </div>
</div>
