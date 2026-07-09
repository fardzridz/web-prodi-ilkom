<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class AuthController extends Controller
{
    public function login(): View
    {
        return view('admin.auth.login');
    }
}
