@props([
    'for',
    'value' => '',
    'placeholder' => '',
    'dark' => false,
])

@php($inputTheme = $dark
    ? 'border-white/20 bg-white/10 text-cream placeholder:text-cream/40 focus:border-gold focus:ring-gold/20'
    : 'border-line bg-white text-ink placeholder:text-muted focus:border-primary focus:ring-primary/20')

@php($buttonTheme = $dark
    ? 'bg-gold text-primary hover:bg-gold/90 focus-visible:ring-gold/40'
    : 'bg-primary text-white hover:bg-primary/90 focus-visible:ring-primary/40')

<div {{ $attributes->class(['relative w-full max-w-xl']) }}>
    <label class="sr-only" for="{{ $for }}">{{ $placeholder }}</label>
    <input id="{{ $for }}" type="search" name="q" maxlength="100" value="{{ $value }}" placeholder="{{ $placeholder }}" class="h-12 w-full rounded-full border pl-5 pr-14 text-sm focus:outline-none focus:ring-2 transition-shadow [&::-webkit-search-cancel-button]:appearance-none {{ $inputTheme }}">
    <button type="submit" aria-label="Cari" class="absolute right-1.5 top-1/2 inline-flex h-9 w-9 -translate-y-1/2 items-center justify-center rounded-full shadow-sm transition-colors focus:outline-none focus-visible:ring-2 {{ $buttonTheme }}">
        <svg class="pointer-events-none h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
    </button>
</div>
