@extends('layouts.admin')

@section('title', $title.' - Pengelola Situs Prodi')

@section('content')
    <section class="admin-heading">
        <p class="eyebrow">{{ $section }}</p>
        <h1>{{ $title }}</h1>
        <p>{{ $helper }}</p>
    </section>

    <section class="content-panel">
        <h2>Route Planning Siap</h2>
        <p>
            Halaman ini adalah placeholder route untuk menyesuaikan struktur Laravel dengan prototype
            <code>prototipe/admin/{{ $prototype }}</code>.
        </p>
        <p>
            Implementasi form, tabel, validasi, dan aksi CRUD akan dikerjakan pada task modul terkait.
        </p>
    </section>
@endsection
