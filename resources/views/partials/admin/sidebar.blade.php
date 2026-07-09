<aside id="admin-sidebar" class="admin-sidebar">
    <a class="admin-brand" href="{{ route('admin.dashboard') }}">
        <span class="brand-mark">IK</span>
        <span>
            <strong>Admin CMS</strong>
            <small>Ilmu Komputer</small>
        </span>
    </a>

    <nav class="admin-nav" aria-label="Navigasi admin">
        <span>Dashboard</span>
        <a href="{{ route('admin.dashboard') }}">Ringkasan</a>

        <span>Konten Website</span>
        <a href="{{ route('admin.beranda') }}">Beranda</a>
        <a href="{{ route('admin.profil') }}">Profil Prodi</a>

        <span>Data Prodi</span>
        <a href="{{ route('admin.dosen.index') }}">Dosen</a>
        <a href="{{ route('admin.alumni.index') }}">Alumni</a>

        <span>Publikasi</span>
        <a href="{{ route('admin.kegiatan.index') }}">Kegiatan</a>
        <a href="{{ route('admin.dokumen.index') }}">Dokumen</a>
        <a href="{{ route('admin.kategori-dokumen') }}">Kategori Dokumen</a>
        <a href="{{ route('admin.jurnal') }}">Link E-Jurnal</a>

        <span>Pengaturan</span>
        <a href="{{ route('admin.kontak') }}">Kontak</a>
        <a href="{{ route('admin.pengaturan') }}">Pengaturan Website</a>
        <a href="{{ route('admin.akun-admin') }}">Akun Admin</a>
    </nav>
</aside>
