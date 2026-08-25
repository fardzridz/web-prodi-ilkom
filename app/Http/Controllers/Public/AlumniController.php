<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\AlumniIndexRequest;
use App\Services\Public\PublicDataService;
use Illuminate\Contracts\View\View;

class AlumniController extends Controller
{
    public function __construct(private readonly PublicDataService $dataService) {}

    public function index(AlumniIndexRequest $request): View
    {
        $search = $request->search();
        $job = $request->job();

        if ($job !== null && ! $this->dataService->cachedJobPositions()->contains($job)) {
            $job = null;
        }

        return view('public.alumni', [
            'alumni' => $this->dataService->alumniData(search: $search, jobPosition: $job),
            'jobPositions' => $this->dataService->cachedJobPositions(),
            'filters' => ['q' => $search, 'job' => $job],
            'seoTitle' => 'Alumni S1 Ilmu Komputer UNIWARA Pasuruan | Jejak Karier Lulusan',
            'seoDesc' => 'Jejak karier alumni S1 Ilmu Komputer UNIWARA Pasuruan: lulusan berkarya di industri teknologi, startup, dan riset. Lihat testimoni & profil.',
            'canonical' => route('alumni'),
        ]);
    }
}
