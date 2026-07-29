@php($adminUser = auth()->user())

<header class="admin-topbar">
    <div class="admin-topbar-context">
        <button class="admin-icon-button admin-menu-button" type="button" data-sidebar-open aria-controls="admin-sidebar" aria-expanded="false" aria-label="Buka menu pengelola">
            <i class="fa-solid fa-bars" aria-hidden="true"></i>
        </button>

        <div>
            <strong>Pengelola Situs Prodi</strong>
            <small>Program Studi Ilmu Komputer</small>
        </div>
    </div>

    <div class="admin-topbar-actions">
        <a class="admin-view-site" href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" aria-label="Buka situs publik di tab baru">
            <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
            <span>Lihat Situs</span>
        </a>

        <div class="admin-profile">
            <button class="admin-profile-toggle" type="button" data-profile-toggle aria-expanded="false" aria-controls="admin-profile-menu" aria-label="Menu akun pengelola">
                <span class="admin-profile-avatar" aria-hidden="true">{{ $adminUser ? mb_strtoupper(mb_substr($adminUser->name, 0, 2)) : 'KP' }}</span>
                <span class="admin-profile-copy">
                    <strong>{{ $adminUser?->name ?? 'Pengelola Prodi' }}</strong>
                    <small>{{ $adminUser?->role === \App\Models\User::ROLE_ADMIN ? 'Administrator' : 'Pengelola' }}</small>
                </span>
                <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
            </button>

            <div id="admin-profile-menu" class="admin-profile-menu" data-profile-menu hidden>
                <a href="{{ route('admin.akun-admin') }}">
                    <i class="fa-solid fa-user-gear" aria-hidden="true"></i>
                    Akun Pengelola
                </a>
                @auth
                    <form action="{{ route('admin.logout') }}" method="post">
                        @csrf
                        <button type="submit">
                            <i class="fa-solid fa-right-from-bracket" aria-hidden="true"></i>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('admin.login') }}">
                        <i class="fa-solid fa-right-to-bracket" aria-hidden="true"></i>
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </div>
</header>
