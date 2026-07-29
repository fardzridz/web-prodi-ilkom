@extends('layouts.admin')

@section('title', 'Edit Alumni - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Edit Alumni')
@section('page-helper', 'Perbarui profil, pekerjaan, testimoni, dan status alumni.')

@section('content')
    @include('admin.alumni._form')
@endsection
