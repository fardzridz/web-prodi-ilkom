<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Akun Pengelola',
            'section' => 'Pengaturan',
            'helper' => 'Atur nama, surel, dan kata sandi akun pengelola situs.',
            'prototype' => 'akun-admin.html',
        ]);
    }
}
