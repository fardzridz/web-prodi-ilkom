<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Models\HomeSection;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HomeSectionController extends Controller
{
    public function index(): View
    {
        return view('admin.home-section.index', [
            'homeSection' => HomeSection::query()->first() ?? new HomeSection([
                'hero_slides' => [],
                'advantages' => [],
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

            $submittedAdvantages = $validated['advantages'] ?? [];
            $existingAdvantagePaths = collect(HomeSection::advantageItems($homeSection->advantages))
                ->pluck('image')
                ->filter()
                ->all();
            $advantages = [];
            $advantageHeading = trim((string) ($validated['advantages_heading'] ?? '')) ?: HomeSection::DEFAULT_ADVANTAGE_HEADING;

            foreach ($submittedAdvantages as $advantage) {
                $existingPath = $advantage['existing_path'] ?? null;

                if ($existingPath && ! in_array($existingPath, $existingAdvantagePaths, true)) {
                    continue;
                }

                if (($advantage['remove'] ?? false) === true) {
                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                    }

                    continue;
                }

                $imagePath = $existingPath;

                if (isset($advantage['image'])) {
                    $imagePath = $advantage['image']->store('uploads/home', 'public');
                    $storedPaths[] = $imagePath;

                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                    }
                }

                $advantages[] = [
                    'order' => (int) ($advantage['order'] ?? count($advantages) + 1),
                    'title' => $advantage['title'],
                    'description' => $advantage['description'],
                    'image' => $imagePath,
                ];
            }

            // Whitelist eksplisit — hanya kolom teks yang boleh diisi via mass assignment.
            $homeSection->fill(Arr::only($validated, [
                'hero_title',
                'hero_subtitle',
                'cta_text',
                'cta_link',
                'welcome_title',
                'welcome_description',
            ]));
            $homeSection->hero_slides = $slides;
            $homeSection->advantages = ['heading' => $advantageHeading, 'items' => $advantages];
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
