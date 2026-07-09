<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class HomeSectionController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Beranda',
            'section' => 'Konten Situs',
            'helper' => 'Atur tampilan utama, tombol ajakan, gambar, dan sambutan pada halaman beranda.',
            'prototype' => 'beranda.html',
        ]);
    }
}
