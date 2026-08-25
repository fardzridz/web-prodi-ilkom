@props([
    'active' => false,
    'variant' => 'switch',
    'labelActive' => 'Aktifkan',
    'labelInactive' => 'Nonaktifkan',
    'titleActive' => null,
    'titleInactive' => null,
])

@php
    $isActive = (bool) $active;
    $label = $isActive ? ($titleInactive ?? $labelInactive) : ($titleActive ?? $labelActive);
    // base color: active = success (on), inactive = gray
    $baseClass = $isActive ? 'text-success-600 dark:text-success-500' : 'text-gray-400 dark:text-gray-500';
    $hoverClass = $isActive
        ? 'hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-white/10 dark:hover:text-gray-300'
        : 'hover:text-success-600 hover:bg-success-50 dark:hover:bg-success-500/10 dark:hover:text-success-400';
@endphp

<button type="submit"
    title="{{ $label }}"
    aria-label="{{ $label }}"
    aria-pressed="{{ $isActive ? 'true' : 'false' }}"
    class="inline-flex items-center justify-center h-8 w-8 rounded-lg transition {{ $baseClass }} {{ $hoverClass }}"
>
    @if($variant === 'eye')
        @if($isActive)
            {{-- eye - published / visible (Heroicons outline) --}}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M2.036 12.322a1.193 1.193 0 010-.684A12.18 12.18 0 0112 5c4.5 0 8.332 2.67 9.964 6.322a1.193 1.193 0 010 .684A12.18 12.18 0 0112 19c-4.5 0-8.332-2.67-9.964-6.322z"/>
                <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        @else
            {{-- eye-off - draft / hidden --}}
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.5a10.523 10.523 0 01-4.293 5.207M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88"/>
            </svg>
        @endif
    @else
        {{-- switch --}}
        @if($isActive)
            {{-- on - knob right, brand success --}}
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="2" y="7" width="20" height="10" rx="5" fill="currentColor" opacity="0.18" stroke="currentColor" stroke-width="1.6"/>
                <circle cx="17" cy="12" r="3.2" fill="currentColor"/>
            </svg>
        @else
            {{-- off - knob left, gray outline --}}
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <rect x="2" y="7" width="20" height="10" rx="5" fill="none" stroke="currentColor" stroke-width="1.6" opacity="0.9"/>
                <circle cx="7" cy="12" r="3.2" fill="currentColor" opacity="0.9"/>
            </svg>
        @endif
    @endif
</button>
