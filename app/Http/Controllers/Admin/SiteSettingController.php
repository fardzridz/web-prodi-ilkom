<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use App\Services\ImageOptimizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SiteSettingController extends Controller
{
    public function index(): View
    {
        return view('admin.site-setting.index', [
            'siteSetting' => SiteSetting::query()->first() ?? new SiteSetting([
                'footer_academic_links' => [],
            ]),
        ]);
    }

    public function update(UpdateSiteSettingRequest $request): RedirectResponse
    {
        $siteSetting = SiteSetting::query()->first() ?? new SiteSetting;
        $validated = $request->validated();
        $oldPaths = [];
        $storedPaths = [];

        try {
            $optimizer = new ImageOptimizer;
            foreach (['logo', 'favicon'] as $fileField) {
                $removeField = "remove_{$fileField}";
                $currentPath = $siteSetting->{$fileField};

                if (isset($validated[$fileField])) {
                    $result = $optimizer->optimize($validated[$fileField], 'uploads/settings');
                    $newPath = $result['path'];
                    $storedPaths[] = $newPath;
                    if ($result['thumb']) {
                        $storedPaths[] = $result['thumb'];
                    }
                    $validated[$fileField] = $newPath;

                    if ($currentPath) {
                        $oldPaths[] = $currentPath;
                        $oldPaths[] = ImageOptimizer::thumbPath($currentPath);
                    }
                } elseif (($validated[$removeField] ?? false) === true) {
                    $validated[$fileField] = null;

                    if ($currentPath) {
                        $oldPaths[] = $currentPath;
                        $oldPaths[] = ImageOptimizer::thumbPath($currentPath);
                    }
                } else {
                    unset($validated[$fileField]);
                }

                unset($validated[$removeField]);
            }

            $validated['footer_academic_links'] = array_values($validated['footer_links'] ?? []);
            unset($validated['footer_links']);

            $siteSetting->fill($validated)->save();
            Storage::disk('public')->delete(array_values(array_unique($oldPaths)));
            Cache::forget('site_setting');
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('admin.pengaturan')
            ->with('success', 'Pengaturan website berhasil diperbarui.');
    }
}
