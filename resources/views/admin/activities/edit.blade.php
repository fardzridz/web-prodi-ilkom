@extends('layouts.admin')

@section('title', 'Edit Kegiatan - Pengelola Situs Prodi')
@section('page-section', 'Publikasi')
@section('page-heading', 'Edit Kegiatan')
@section('page-helper', 'Perbarui isi, lokasi, status, atau jadwal tayang kegiatan.')

@section('content')
    @include('admin.activities._form')
@endsection
