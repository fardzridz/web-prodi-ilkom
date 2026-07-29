@extends('layouts.admin')

@section('title', 'Edit Dokumen - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Edit Dokumen')
@section('page-helper', 'Perbarui informasi, berkas, dan status publikasi dokumen.')

@section('content')
    @include('admin.documents._form')
@endsection
