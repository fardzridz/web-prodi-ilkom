<?php

namespace App\View\Composers;

use App\Services\Public\SiteService;
use Illuminate\View\View;

class SiteComposer
{
    public function __construct(private readonly SiteService $siteService) {}

    /**
     * Share `site` and `contactInfo` with every rendered view.
     *
     * Resolution is delegated to the singleton-scoped SiteService, so repeated
     * composer instantiation (Laravel resolves a fresh composer per view event)
     * still results in a single cache read per request.
     */
    public function compose(View $view): void
    {
        $view->with('site', $this->siteService->getSiteSetting())
            ->with('contactInfo', $this->siteService->getContact());
    }
}
