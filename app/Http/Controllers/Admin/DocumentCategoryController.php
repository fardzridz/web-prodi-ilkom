<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class DocumentCategoryController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Kategori Dokumen',
            'section' => 'Publikasi',
            'helper' => 'Kelola kelompok dokumen agar pengunjung mudah menemukan berkas akademik.',
            'prototype' => 'kategori-dokumen.html',
        ]);
    }
}
