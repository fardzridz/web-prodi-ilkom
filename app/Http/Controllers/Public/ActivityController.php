<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ActivityIndexRequest;
use App\Services\Public\PublicDataService;
use Illuminate\Contracts\View\View;

class ActivityController extends Controller
{
    public function __construct(private readonly PublicDataService $dataService) {}

    public function index(ActivityIndexRequest $request): View
    {
        $search = $request->search();
        $category = $request->category();

        if ($category !== null && ! $this->dataService->cachedCategories()->contains($category)) {
            $category = null;
        }

        return view('public.activities.index', [
            'activities' => $this->dataService->activitiesData(search: $search, category: $category),
            'categories' => $this->dataService->cachedCategories(),
            'filters' => ['q' => $search, 'category' => $category],
            'seoTitle' => 'Kegiatan S1 Ilmu Komputer UNIWARA | Seminar, Workshop & Prestasi',
            'seoDesc' => 'Kegiatan S1 Ilmu Komputer UNIWARA Pasuruan: seminar nasional, workshop AI, kompetisi, dan prestasi mahasiswa Ilmu Komputer.',
            'canonical' => route('activities.index'),
        ]);
    }

    public function show(string $slug): View
    {
        $activity = $this->dataService->findActivity($slug);

        return view('public.activities.show', [
            'activity' => $activity,
            'otherActivities' => $this->dataService->otherActivitiesData($slug),
            'seoTitle' => ($activity['title'] ?? 'Kegiatan').' | S1 Ilmu Komputer UNIWARA Pasuruan',
            'seoDesc' => $activity['excerpt'] ?? config('seo.description'),
            'canonical' => route('activities.show', $slug),
        ]);
    }
}
