<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ProgramProfileController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Profil Prodi',
            'section' => 'Konten Situs',
            'helper' => 'Atur seluruh isi halaman profil public, termasuk visi, misi, tujuan, dan keunggulan.',
            'prototype' => 'profil.html',
        ]);
    }
}
