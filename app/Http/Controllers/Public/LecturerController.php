<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\LecturerIndexRequest;
use App\Services\Public\PublicDataService;
use Illuminate\Contracts\View\View;

class LecturerController extends Controller
{
    public function __construct(private readonly PublicDataService $dataService) {}

    public function index(LecturerIndexRequest $request): View
    {
        $search = $request->search();
        $expertise = $request->expertise();

        if ($expertise !== null && ! $this->dataService->cachedExpertises()->contains($expertise)) {
            $expertise = null;
        }

        return view('public.lecturers', [
            'lecturers' => $this->dataService->lecturersData(search: $search, expertise: $expertise),
            'expertises' => $this->dataService->cachedExpertises(),
            'filters' => ['q' => $search, 'expertise' => $expertise],
            'seoTitle' => 'Dosen S1 Ilmu Komputer UNIWARA Pasuruan | Tenaga Pengajar',
            'seoDesc' => 'Daftar dosen S1 Ilmu Komputer UNIWARA Pasuruan: keahlian AI, rekayasa perangkat lunak, visi komputer, dan web. Lihat profil & riset dosen.',
            'canonical' => route('lecturers'),
        ]);
    }
}
