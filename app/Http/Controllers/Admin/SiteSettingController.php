<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class SiteSettingController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Pengaturan Situs',
            'section' => 'Pengaturan',
            'helper' => 'Atur identitas situs, logo, ikon, tautan e-jurnal, teks bawah halaman, dan tautan akademik.',
            'prototype' => 'pengaturan.html',
        ]);
    }

    public function journal(): View
    {
        return view('admin.placeholder', [
            'title' => 'Tautan E-Jurnal',
            'section' => 'Publikasi',
            'helper' => 'Atur tautan menuju situs e-jurnal resmi universitas.',
            'prototype' => 'jurnal.html',
        ]);
    }
}
