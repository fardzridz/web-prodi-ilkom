@extends('layouts.public')

@section('title', 'Kontak - Program Studi Ilmu Komputer')

@section('content')
    @include('partials.public.page-hero', [
        'active' => 'home',
        'kicker' => 'Kontak',
        'title' => 'Kontak Program Studi',
        'description' => 'Informasi alamat, email, telepon, media sosial, dan lokasi Program Studi Ilmu Komputer.',
    ])

    @include('partials.public.contact-cta', ['id' => 'kontak-section'])
@endsection
