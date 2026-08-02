<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAccountRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function index(): View
    {
        return view('admin.account.index', [
            'admin' => auth()->user(),
        ]);
    }

    public function update(UpdateAccountRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'password']);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $request->user()->update($data);
        $request->session()->regenerate();

        return redirect()
            ->route('admin.akun-admin')
            ->with('success', 'Akun pengelola berhasil diperbarui.');
    }
}
