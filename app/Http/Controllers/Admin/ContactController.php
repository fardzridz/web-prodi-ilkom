<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Kontak',
            'section' => 'Pengaturan',
            'helper' => 'Atur alamat, kanal komunikasi, media sosial, dan peta lokasi kampus.',
            'prototype' => 'kontak.html',
        ]);
    }
}
