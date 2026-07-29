@extends('layouts.admin')

@section('title', $title.' - Pengelola Situs Prodi')
@section('page-section', $section)
@section('page-heading', $title)
@section('page-helper', $helper)

@section('content')
    <section class="admin-panel">
        <div class="admin-panel-heading">
            <div>
                <p class="admin-panel-kicker">Rujukan prototipe</p>
                <h2>Area Modul Siap Digunakan</h2>
            </div>
            <code>prototipe/admin/{{ $prototype }}</code>
        </div>

        <x-admin.empty-state
            title="Modul {{ $title }} belum berisi data"
            message="Layout dan route sudah siap. Form, tabel, validasi, serta aksi data akan dikerjakan pada task P0 modul ini."
            icon="fa-layer-group"
        />
    </section>
@endsection
