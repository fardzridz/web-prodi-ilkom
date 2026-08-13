@props([
    'options' => [],
    'selected' => '',
    'placeholder' => 'Pilih...',
    'required' => false,
    'name' => '',
    'id' => '',
])

@php
    $jsonOptions = json_encode($options, JSON_HEX_APOS | JSON_HEX_QUOT);
    $selKey = (string) $selected;
    $selLabel = '';
    if ($selKey !== '') {
        foreach ($options as $k => $v) {
            if ((string) $k === $selKey) {
                $selLabel = $v;
                break;
            }
        }
    }
@endphp

<div x-data="{ open: false, val: '{{ $selKey }}', lbl: '{!! addcslashes($selLabel, "'") !!}' }" class="relative" @click.outside="open = false">
    <input type="hidden" name="{{ $name }}" x-model="val" @if($required) required @endif />

    <button type="button" @click="open = !open"
        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 flex h-11 w-full items-center justify-between rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
        :class="!lbl && 'text-gray-400 dark:text-white/30'">
        <span x-text="lbl || '{{ $placeholder }}'"></span>
        <svg class="h-5 w-5 shrink-0 text-gray-500 transition-transform duration-200 dark:text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div x-show="open" x-cloak
        class="absolute z-50 mt-1 w-full overflow-hidden rounded-lg border border-gray-200 bg-white shadow-theme-lg dark:border-gray-700 dark:bg-gray-dark"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 -translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-1">
        <div class="max-h-60 overflow-y-auto custom-scrollbar p-1">
            @foreach($options as $value => $label)
                <button type="button"
                    @click="val = '{{ $value }}'; lbl = '{!! addcslashes($label, "'") !!}'; open = false; $dispatch('select-change', { value: '{{ $value }}', name: '{{ $name }}' })"
                    class="flex w-full items-center rounded-md px-3 py-2.5 text-sm font-medium transition-colors"
                    :class="val === '{{ $value }}'
                        ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'
                        : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5'">
                    <span class="mr-auto">{{ $label }}</span>
                    <svg x-show="val === '{{ $value }}'" x-cloak class="h-4 w-4 shrink-0 fill-current text-brand-500" viewBox="0 0 24 24" fill="none">
                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19l12-12-1.4-1.4L9 16.2z" fill=""/>
                    </svg>
                </button>
            @endforeach
        </div>
    </div>
</div>
