@extends('layouts.admin')

@section('title', 'Tambah Kegiatan - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Tambah Kegiatan')
@section('page-helper', 'Isi berita, lokasi, status, dan jadwal tayang kegiatan secara lengkap.')

@section('content')
    @include('admin.activities._form')
@endsection
