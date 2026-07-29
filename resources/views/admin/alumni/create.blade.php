@extends('layouts.admin')

@section('title', 'Tambah Alumni - Pengelola Situs Prodi')
@section('page-section', 'Data Prodi')
@section('page-heading', 'Tambah Alumni')
@section('page-helper', 'Tambahkan profil alumni dan atur status tampilnya pada situs publik.')

@section('content')
    @include('admin.alumni._form')
@endsection
