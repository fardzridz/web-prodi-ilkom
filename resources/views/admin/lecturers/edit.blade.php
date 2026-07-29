@extends('layouts.admin')

@section('title', 'Edit Dosen - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Edit Dosen')
@section('page-helper', 'Perbarui profil, status, dan urutan tampil dosen.')

@section('content')
    @include('admin.lecturers._form')
@endsection
