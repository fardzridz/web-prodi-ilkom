<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexProgramProfileRequest;
use App\Http\Requests\Admin\UpdateProgramProfileRequest;
use App\Models\ProgramProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProgramProfileController extends Controller
{
    public function index(IndexProgramProfileRequest $request): View
    {
        return view('admin.program-profile.index', [
            'programProfile' => ProgramProfile::query()->first() ?? new ProgramProfile,
        ]);
    }

    public function update(UpdateProgramProfileRequest $request): RedirectResponse
    {
        $programProfile = ProgramProfile::query()->first() ?? new ProgramProfile;
        $programProfile->fill($request->validated())->save();

        return redirect()
            ->route('admin.profil')
            ->with('success', 'Profil program studi berhasil diperbarui.');
    }
}
