<aside id="admin-sidebar" class="admin-sidebar" aria-label="Navigasi pengelola">
    <div class="admin-sidebar-header">
        <a class="admin-brand" href="{{ route('admin.dashboard') }}" aria-label="Dashboard pengelola Program Studi Ilmu Komputer">
            <img src="{{ asset('assets/images/logo/logo.png') }}" alt="Logo Program Studi Ilmu Komputer">
        </a>

        <button class="admin-icon-button admin-sidebar-close" type="button" data-sidebar-close aria-label="Tutup menu pengelola">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>

    <nav class="admin-nav">
        <div class="admin-nav-group">
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.dashboard')]) href="{{ route('admin.dashboard') }}" @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                <i class="fa-solid fa-gauge-high" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-label">Konten Situs</p>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.beranda*')]) href="{{ route('admin.beranda') }}" @if (request()->routeIs('admin.beranda*')) aria-current="page" @endif>
                <i class="fa-solid fa-house" aria-hidden="true"></i>
                <span>Beranda</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.profil*')]) href="{{ route('admin.profil') }}" @if (request()->routeIs('admin.profil*')) aria-current="page" @endif>
                <i class="fa-solid fa-id-card" aria-hidden="true"></i>
                <span>Profil Prodi</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-label">Data Prodi</p>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.dosen.*')]) href="{{ route('admin.dosen.index') }}" @if (request()->routeIs('admin.dosen.*')) aria-current="page" @endif>
                <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
                <span>Dosen</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.alumni.*')]) href="{{ route('admin.alumni.index') }}" @if (request()->routeIs('admin.alumni.*')) aria-current="page" @endif>
                <i class="fa-solid fa-user-graduate" aria-hidden="true"></i>
                <span>Alumni</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-label">Publikasi</p>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.kegiatan.*')]) href="{{ route('admin.kegiatan.index') }}" @if (request()->routeIs('admin.kegiatan.*')) aria-current="page" @endif>
                <i class="fa-solid fa-calendar-days" aria-hidden="true"></i>
                <span>Kegiatan</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.dokumen.*')]) href="{{ route('admin.dokumen.index') }}" @if (request()->routeIs('admin.dokumen.*')) aria-current="page" @endif>
                <i class="fa-solid fa-file-lines" aria-hidden="true"></i>
                <span>Dokumen</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.kategori-dokumen.*')]) href="{{ route('admin.kategori-dokumen.index') }}" @if (request()->routeIs('admin.kategori-dokumen.*')) aria-current="page" @endif>
                <i class="fa-solid fa-folder-tree" aria-hidden="true"></i>
                <span>Kategori Dokumen</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.jurnal*')]) href="{{ route('admin.jurnal') }}" @if (request()->routeIs('admin.jurnal*')) aria-current="page" @endif>
                <i class="fa-solid fa-book-open" aria-hidden="true"></i>
                <span>Tautan E-Jurnal</span>
            </a>
        </div>

        <div class="admin-nav-group">
            <p class="admin-nav-label">Pengaturan</p>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.kontak*')]) href="{{ route('admin.kontak') }}" @if (request()->routeIs('admin.kontak*')) aria-current="page" @endif>
                <i class="fa-solid fa-address-book" aria-hidden="true"></i>
                <span>Kontak</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.pengaturan*')]) href="{{ route('admin.pengaturan') }}" @if (request()->routeIs('admin.pengaturan*')) aria-current="page" @endif>
                <i class="fa-solid fa-gear" aria-hidden="true"></i>
                <span>Pengaturan Situs</span>
            </a>
            <a @class(['admin-nav-link', 'is-active' => request()->routeIs('admin.akun-admin*')]) href="{{ route('admin.akun-admin') }}" @if (request()->routeIs('admin.akun-admin*')) aria-current="page" @endif>
                <i class="fa-solid fa-user-shield" aria-hidden="true"></i>
                <span>Akun Pengelola</span>
            </a>
        </div>
    </nav>

    <div class="admin-sidebar-footer">
        <span class="admin-environment-dot" aria-hidden="true"></span>
        <div>
            <strong>Panel Pengelola Prodi</strong>
            <small>Versi {{ config('app.version', '1.0') }}</small>
        </div>
    </div>
</aside>
