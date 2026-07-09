<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class LecturerController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Daftar Dosen',
            'section' => 'Data Prodi',
            'helper' => 'Kelola profil dosen, status tampil, dan urutan pada halaman public.',
            'prototype' => 'dosen.html',
        ]);
    }

    public function create(): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Dosen',
            'section' => 'Data Prodi',
            'helper' => 'Tambah profil dosen yang tampil pada halaman public.',
            'prototype' => 'dosen-form.html',
        ]);
    }

    public function edit(string $lecturer): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Dosen',
            'section' => 'Data Prodi',
            'helper' => "Ubah profil dosen dengan ID {$lecturer}.",
            'prototype' => 'dosen-form.html',
        ]);
    }
}
