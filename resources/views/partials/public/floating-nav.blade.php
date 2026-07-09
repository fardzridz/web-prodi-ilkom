@php
    $active ??= null;
    $links = [
        ['key' => 'home', 'label' => 'Beranda', 'href' => route('home'), 'icon' => 'fa-house'],
        ['key' => 'profile', 'label' => 'Profil', 'href' => route('profile'), 'icon' => 'fa-id-card'],
        ['key' => 'activities', 'label' => 'Kegiatan', 'href' => route('activities.index'), 'icon' => 'fa-calendar-days'],
        ['key' => 'lecturers', 'label' => 'Dosen', 'href' => route('lecturers'), 'icon' => 'fa-chalkboard-user'],
        ['key' => 'alumni', 'label' => 'Alumni', 'href' => route('alumni'), 'icon' => 'fa-user-graduate'],
        ['key' => 'journal', 'label' => 'Jurnal', 'href' => route('journal'), 'icon' => 'fa-book-open', 'external' => true],
        ['key' => 'documents', 'label' => 'Dokumen', 'href' => route('documents'), 'icon' => 'fa-file-lines'],
    ];
@endphp

<nav class="floating-page-nav sticky top-0 z-[45] w-full -mt-[30px] -mb-[30px] bg-blue-dark border-t-[3px] border-t-[rgba(255,255,255,0.88)] max-[1024px]:hidden [&_a]:inline-flex [&_a]:flex-1 [&_a]:items-center [&_a]:justify-center [&_a]:min-w-0 [&_a]:gap-2.5 [&_a]:px-[18px] [&_a]:text-white [&_a]:text-[15px] [&_a]:font-bold [&_a]:leading-none [&_a]:text-center [&_a]:border-r [&_a]:border-[rgba(255,255,255,0.14)] [&_a:hover]:bg-blue-mid [&_a[aria-current=page]]:bg-blue-mid [&_i]:text-[17px] [&_i]:leading-none" aria-label="Navigasi halaman">
    <div class="floating-page-nav-inner flex items-stretch justify-center w-[min(100%_-_48px,var(--container))] min-h-[60px] mx-auto overflow-hidden">
        @foreach ($links as $link)
            <a href="{{ $link['href'] }}" @if ($active === $link['key']) aria-current="page" @endif @if ($link['external'] ?? false) target="_blank" rel="noopener" aria-label="Buka web jurnal" @endif>
                <i class="fa-solid {{ $link['icon'] }}" aria-hidden="true"></i>
                <span>{{ $link['label'] }} @if ($link['external'] ?? false)<i class="fa-solid fa-arrow-up-right-from-square nav-external-icon !w-auto ml-1.5 text-[11px] opacity-[0.78]" aria-hidden="true"></i>@endif</span>
            </a>
        @endforeach
    </div>
</nav>
