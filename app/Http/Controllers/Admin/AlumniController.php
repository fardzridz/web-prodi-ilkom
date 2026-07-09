<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AlumniController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Daftar Alumni',
            'section' => 'Data Prodi',
            'helper' => 'Kelola profil alumni yang dapat ditampilkan pada halaman public.',
            'prototype' => 'alumni.html',
        ]);
    }

    public function create(): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Alumni',
            'section' => 'Data Prodi',
            'helper' => 'Tambah profil alumni yang tampil pada halaman public.',
            'prototype' => 'alumni-form.html',
        ]);
    }

    public function edit(string $alumni): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Alumni',
            'section' => 'Data Prodi',
            'helper' => "Ubah alumni dengan ID {$alumni}.",
            'prototype' => 'alumni-form.html',
        ]);
    }
}
