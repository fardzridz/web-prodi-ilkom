<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateProgramProfileRequest;
use App\Models\ProgramProfile;
use App\Services\DashboardCache;
use App\Services\ImageOptimizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ProgramProfileController extends Controller
{
    private const IMAGE_FIELDS = [
        'description_image',
        'history_image',
        'goals_image',
        'advantages_image',
    ];

    private const TEXT_FIELDS = [
        'history',
        'description',
        'vision',
        'mission',
        'goals',
        'accreditation',
        'advantages',
    ];

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
        $storedPaths = [];
        $pathsToDelete = [];

        try {
            $optimizer = new ImageOptimizer;
            DB::transaction(function () use ($validated, $request, &$storedPaths, &$pathsToDelete, $optimizer): void {
                $programProfile = ProgramProfile::query()->lockForUpdate()->first() ?? new ProgramProfile;

                foreach (self::IMAGE_FIELDS as $field) {
                    $currentPath = $programProfile->{$field};

                    if (filter_var($validated["{$field}_remove"] ?? false, FILTER_VALIDATE_BOOL)) {
                        if ($currentPath) {
                            $pathsToDelete[] = $currentPath;
                            $pathsToDelete[] = ImageOptimizer::thumbPath($currentPath);
                        }

                        $programProfile->{$field} = null;

                        continue;
                    }

                    if ($request->hasFile($field)) {
                        $result = $optimizer->optimize($request->file($field), 'uploads/profile');
                        $path = $result['path'];
                        $storedPaths[] = $path;
                        if ($result['thumb']) {
                            $storedPaths[] = $result['thumb'];
                        }

                        if ($currentPath) {
                            $pathsToDelete[] = $currentPath;
                            $pathsToDelete[] = ImageOptimizer::thumbPath($currentPath);
                        }

                        $programProfile->{$field} = $path;
                    }
                }

                // Whitelist eksplisit — hanya kolom teks yang boleh diisi via mass assignment.
                $programProfile->fill(Arr::only($validated, self::TEXT_FIELDS));
                $programProfile->save();
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        Log::info('admin.program_profile.updated', [
            'user_id' => $userId,
            'fields' => array_keys($validated),
            'ip' => $request->ip(),
        ]);

        Storage::disk('public')->delete(array_values(array_unique($pathsToDelete)));

        Cache::forget('program_profile');
        DashboardCache::forgetReadiness();

        return redirect()
            ->route('admin.profil')
            ->with('success', 'Profil program studi berhasil diperbarui.');
    }
}
