@extends('layouts.admin')

@section('title', 'Tambah Dokumen - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Tambah Dokumen')
@section('page-helper', 'Unggah berkas akademik dan atur kategori serta status publikasinya.')

@section('content')
    @include('admin.documents._form')
@endsection
