<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Public\SiteService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    public function __construct(private readonly SiteService $siteService) {}

    public function index(): View
    {
        return view('public.profile', [
            'programProfile' => $this->siteService->getProgramProfile(),
            'seoTitle' => 'Profil S1 Ilmu Komputer UNIWARA Pasuruan | Visi Misi & Sejarah',
            'seoDesc' => 'Profil Program Studi S1 Ilmu Komputer Universitas PGRI Wiranegara Pasuruan: visi, misi, sejarah, akreditasi, dan keunggulan kurikulum AI & rekayasa perangkat lunak.',
            'canonical' => route('profile'),
        ]);
    }

    public function visionMission(): RedirectResponse
    {
        return redirect('/profil#visi-misi-page');
    }
}
