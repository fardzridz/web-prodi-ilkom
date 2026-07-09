@extends('layouts.admin')

@section('title', 'Dashboard Admin - Website Prodi')

@section('content')
    <section class="admin-heading">
        <p class="eyebrow">Dashboard</p>
        <h1>Ringkasan CMS Website Prodi</h1>
        <p>Halaman awal admin untuk memantau kegiatan, dosen, dokumen, alumni, dan status konten public.</p>
    </section>

    <section class="admin-grid">
        <article class="stat-card">
            <span>Kegiatan</span>
            <strong>0</strong>
            <small>Belum terhubung database</small>
        </article>
        <article class="stat-card">
            <span>Dosen</span>
            <strong>0</strong>
            <small>Belum terhubung database</small>
        </article>
        <article class="stat-card">
            <span>Dokumen</span>
            <strong>0</strong>
            <small>Belum terhubung database</small>
        </article>
        <article class="stat-card">
            <span>Alumni</span>
            <strong>0</strong>
            <small>Belum terhubung database</small>
        </article>
    </section>

    <section class="content-panel">
        <h2>Status Foundation</h2>
        <ul class="check-list">
            <li>Layout public dan admin siap dipakai ulang.</li>
            <li>Route public dasar sudah mengikuti dokumen teknis.</li>
            <li>CRUD, auth, database, dan upload dikerjakan pada task P0 berikutnya.</li>
        </ul>
    </section>
@endsection
