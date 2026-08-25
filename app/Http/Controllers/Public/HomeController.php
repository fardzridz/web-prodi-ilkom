<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Public\PublicDataService;
use App\Services\Public\SiteService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __construct(
        private readonly SiteService $siteService,
        private readonly PublicDataService $dataService,
    ) {}

    public function __invoke(): View
    {
        $homeSection = $this->siteService->getHomeSection();
        $programProfile = $this->siteService->getProgramProfile();

        return view('public.home', [
            'homeSection' => $homeSection,
            'heroSlides' => $this->siteService->heroSlidesForPublic($homeSection),
            'heroCtaUrl' => $this->siteService->resolvePublicLink($homeSection->cta_link, route('profile')),
            'cta_text' => $this->siteService->ctaText($homeSection),
            'advantageSection' => $this->siteService->advantageSectionData($homeSection),
            'activities' => $this->dataService->activitiesData(limit: 3),
            'alumni' => $this->dataService->alumniData(limit: 3),
            'programProfile' => $programProfile,
            'seoTitle' => config('seo.title'),
            'seoDesc' => config('seo.description'),
            'canonical' => route('home'),
        ]);
    }
}
