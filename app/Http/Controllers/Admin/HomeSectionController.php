<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateHomeSectionRequest;
use App\Models\HomeSection;
use App\Services\DashboardCache;
use App\Services\ImageOptimizer;
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
            $optimizer = new ImageOptimizer;
            $welcomeImage = $homeSection->welcome_image;

            if (filter_var($request->input('welcome_remove'), FILTER_VALIDATE_BOOL)) {
                if ($welcomeImage) {
                    $pathsToDelete[] = $welcomeImage;
                    $pathsToDelete[] = ImageOptimizer::thumbPath($welcomeImage);
                }

                $welcomeImage = null;
            } elseif ($request->hasFile('welcome_image')) {
                $result = $optimizer->optimize($request->file('welcome_image'), 'uploads/home');
                $welcomeImage = $result['path'];
                $storedPaths[] = $welcomeImage;
                if ($result['thumb']) {
                    $storedPaths[] = $result['thumb'];
                }

                if ($homeSection->welcome_image) {
                    $pathsToDelete[] = $homeSection->welcome_image;
                    $pathsToDelete[] = ImageOptimizer::thumbPath($homeSection->welcome_image);
                }
            }

            foreach ($submittedSlides as $slide) {
                $existingPath = $slide['existing_path'] ?? null;

                if ($existingPath && ! $existingSlides->has($existingPath)) {
                    continue;
                }

                if (($slide['remove'] ?? false) === true) {
                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                        $pathsToDelete[] = ImageOptimizer::thumbPath($existingPath);
                    }

                    continue;
                }

                $path = $existingPath;

                if (isset($slide['file'])) {
                    $result = $optimizer->optimize($slide['file'], 'uploads/home');
                    $path = $result['path'];
                    $storedPaths[] = $path;
                    if ($result['thumb']) {
                        $storedPaths[] = $result['thumb'];
                    }

                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                        $pathsToDelete[] = ImageOptimizer::thumbPath($existingPath);
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
                        $pathsToDelete[] = ImageOptimizer::thumbPath($existingPath);
                    }

                    continue;
                }

                $imagePath = $existingPath;

                if (isset($advantage['image'])) {
                    $result = $optimizer->optimize($advantage['image'], 'uploads/home');
                    $imagePath = $result['path'];
                    $storedPaths[] = $imagePath;
                    if ($result['thumb']) {
                        $storedPaths[] = $result['thumb'];
                    }

                    if ($existingPath) {
                        $pathsToDelete[] = $existingPath;
                        $pathsToDelete[] = ImageOptimizer::thumbPath($existingPath);
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
            $homeSection->welcome_image = $welcomeImage;
            $homeSection->advantages = ['heading' => $advantageHeading, 'items' => $advantages];
            $homeSection->save();

            Storage::disk('public')->delete(array_values(array_unique($pathsToDelete)));
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        DashboardCache::forgetReadiness();

        return redirect()
            ->route('admin.beranda')
            ->with('success', 'Konten beranda berhasil diperbarui.');
    }
}
