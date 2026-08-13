@props([
    'size' => 'md',
    'variant' => 'primary',
    'startIcon' => null,
    'endIcon' => null,
    'className' => '',
    'disabled' => false,
    'loading' => false,
])

@php
    $base = 'inline-flex items-center justify-center font-medium gap-2 rounded-lg transition';

    $sizeMap = [
        'sm' => 'px-4 py-3 text-sm',
        'md' => 'px-5 py-3.5 text-sm',
    ];
    $sizeClass = $sizeMap[$size] ?? $sizeMap['md'];

    $variantMap = [
        'primary' => 'bg-brand-500 text-white shadow-theme-xs hover:bg-brand-600 disabled:bg-brand-300',
        'outline' => 'bg-white text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03] dark:hover:text-gray-300',
    ];
    $variantClass = $variantMap[$variant] ?? $variantMap['primary'];

    $isDisabled = $disabled || $loading;
    $disabledClass = $isDisabled ? 'cursor-not-allowed opacity-50' : '';

    $classes = trim("{$base} {$sizeClass} {$variantClass} {$className} {$disabledClass}");
@endphp

<button
    {{ $attributes->merge(['class' => $classes, 'type' => $attributes->get('type', 'button')]) }}
    @if($isDisabled) disabled @endif>

    @if($loading)
        <x-admin.spinner />
    @elseif($startIcon)
        <span class="flex items-center">{!! $startIcon !!}</span>
    @endif

    {{ $slot }}

    @isset($endIcon)
        <span class="flex items-center">{!! $endIcon !!}</span>
    @endisset
</button>
