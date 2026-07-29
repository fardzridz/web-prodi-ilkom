@props([
    'title' => 'Belum ada data',
    'message' => 'Data akan muncul di area ini setelah tersedia.',
    'icon' => 'fa-inbox',
    'actionLabel' => null,
    'actionUrl' => null,
])

<section {{ $attributes->class(['admin-empty-state']) }}>
    <span class="admin-empty-state-icon" aria-hidden="true">
        <i class="fa-solid {{ $icon }}"></i>
    </span>
    <div>
        <h2>{{ $title }}</h2>
        <p>{{ $message }}</p>
    </div>

    @if ($actionLabel && $actionUrl)
        <a class="admin-button admin-button-primary" href="{{ $actionUrl }}">{{ $actionLabel }}</a>
    @endif
</section>
