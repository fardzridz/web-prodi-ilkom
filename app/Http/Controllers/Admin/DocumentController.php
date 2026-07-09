<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Daftar Dokumen',
            'section' => 'Publikasi',
            'helper' => 'Kelola dokumen akademik, kategori, berkas, dan status tayang.',
            'prototype' => 'dokumen.html',
        ]);
    }

    public function create(): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Dokumen',
            'section' => 'Publikasi',
            'helper' => 'Tambah dokumen akademik dan berkas unduhan.',
            'prototype' => 'dokumen-form.html',
        ]);
    }

    public function edit(string $document): View
    {
        return view('admin.placeholder', [
            'title' => 'Formulir Dokumen',
            'section' => 'Publikasi',
            'helper' => "Ubah dokumen dengan ID {$document}.",
            'prototype' => 'dokumen-form.html',
        ]);
    }
}
