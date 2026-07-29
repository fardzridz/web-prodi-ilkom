<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ManageHomeSectionRequest;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Models\HomeSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HomeSectionController extends Controller
{
    public function index(ManageHomeSectionRequest $request): View
    {
        return view('admin.home-section.index', [
            'homeSection' => HomeSection::query()->first() ?? new HomeSection([
                'hero_slides' => [],
            ]),
        ]);
    }

    public function update(UpdateHomeSectionRequest $request): RedirectResponse
    {
        $homeSection = HomeSection::query()->first() ?? new HomeSection;
        $validated = $request->validated();
        $submittedSlides = $validated['slides'] ?? [];
        $existingSlides = collect($homeSection->hero_slides ?? [])
            ->filter(fn (mixed $slide): bool => is_array($slide) && isset($slide['path']))
            ->keyBy('path');
        $slides = [];
        $storedPaths = [];
        $pathsToDelete = [];

        try {
            foreach ($submittedSlides as $slide) {
                $existingPath = $slide['existing_path'] ?? null;

                if ($existingPath && ! $existingSlides->has($existingPath)) {
                    continue;
                }

                if (($slide['remove'] ?? false) === true) {
                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                    }

                    continue;
                }

                $path = $existingPath;

                if (isset($slide['file'])) {
                    $path = $slide['file']->store('uploads/home', 'public');
                    $storedPaths[] = $path;

                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                    }
                }

                if ($path) {
                    $slides[] = [
                        'path' => $path,
                        'alt' => $slide['alt'],
                    ];
                }
            }

            unset($validated['slides']);

            $homeSection->fill($validated);
            $homeSection->hero_slides = $slides;
            $homeSection->save();

            Storage::disk('public')->delete(array_values(array_unique($pathsToDelete)));
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        return redirect()
            ->route('admin.beranda')
            ->with('success', 'Konten beranda berhasil diperbarui.');
    }
}
