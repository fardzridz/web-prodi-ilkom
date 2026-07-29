<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageSiteSettingRequest;
use App\Http\Requests\Admin\UpdateJournalUrlRequest;
use App\Http\Requests\Admin\UpdateSiteSettingRequest;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SiteSettingController extends Controller
{
    public function index(ManageSiteSettingRequest $request): View
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
            foreach (['logo', 'favicon'] as $fileField) {
                $removeField = "remove_{$fileField}";
                $currentPath = $siteSetting->{$fileField};

                if (isset($validated[$fileField])) {
                    $newPath = $validated[$fileField]->store('uploads/settings', 'public');
                    $storedPaths[] = $newPath;
                    $validated[$fileField] = $newPath;

                    if ($currentPath) {
                        $oldPaths[] = $currentPath;
                    }
                } elseif (($validated[$removeField] ?? false) === true) {
                    $validated[$fileField] = null;

                    if ($currentPath) {
                        $oldPaths[] = $currentPath;
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
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('admin.pengaturan')
            ->with('success', 'Pengaturan website berhasil diperbarui.');
    }

    public function journal(ManageSiteSettingRequest $request): View
    {
        return view('admin.site-setting.journal', [
            'siteSetting' => SiteSetting::query()->first() ?? new SiteSetting,
        ]);
    }

    public function updateJournal(UpdateJournalUrlRequest $request): RedirectResponse
    {
        $siteSetting = SiteSetting::query()->first() ?? new SiteSetting([
            'site_name' => 'Program Studi Ilmu Komputer',
            'university_name' => 'Universitas PGRI Wiranegara',
            'faculty_name' => 'Fakultas Teknologi dan Sains',
        ]);

        $siteSetting->fill($request->validated())->save();

        return redirect()
            ->route('admin.jurnal')
            ->with('success', 'Tautan e-jurnal berhasil diperbarui.');
    }
}
