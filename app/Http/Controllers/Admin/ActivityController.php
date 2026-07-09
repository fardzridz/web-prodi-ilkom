<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Daftar Kegiatan',
            'section' => 'Publikasi',
            'helper' => 'Kelola berita kegiatan, jadwal tayang, lokasi, dan gambar pendukung.',
            'prototype' => 'kegiatan.html',
        ]);
    }

    public function create(): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Kegiatan',
            'section' => 'Publikasi',
            'helper' => 'Tambah kegiatan, isi berita, gambar, lokasi, dan jadwal tayang.',
            'prototype' => 'kegiatan-form.html',
        ]);
    }

    public function edit(string $activity): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Kegiatan',
            'section' => 'Publikasi',
            'helper' => "Ubah kegiatan dengan ID {$activity}.",
            'prototype' => 'kegiatan-form.html',
        ]);
    }
}
