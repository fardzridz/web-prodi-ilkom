@extends('layouts.admin')

@section('title', 'Tambah Dosen - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Tambah Dosen')
@section('page-helper', 'Tambahkan profil dosen dan atur status serta urutan tampilnya.')

@section('content')
    @include('admin.lecturers._form')
@endsection
