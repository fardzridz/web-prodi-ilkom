<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProgramProfileRequest;
use App\Models\ProgramProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProgramProfileController extends Controller
{
    public function index(): View
    {
        return view('admin.program-profile.index', [
            'programProfile' => ProgramProfile::query()->first() ?? new ProgramProfile,
        ]);
    }

    public function update(UpdateProgramProfileRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $userId = $request->user()?->id;

        DB::transaction(function () use ($validated): void {
            $programProfile = ProgramProfile::query()->lockForUpdate()->first() ?? new ProgramProfile;
            $programProfile->fill($validated)->save();
        });

        Log::info('admin.program_profile.updated', [
            'user_id' => $userId,
            'fields' => array_keys($validated),
            'ip' => $request->ip(),
        ]);

        return redirect()
            ->route('admin.profil')
            ->with('success', 'Profil program studi berhasil diperbarui.');
    }
}
