<?php

namespace App\Providers;

use App\Services\Public\ImageService;
use App\Services\Public\SiteService;
use App\View\Composers\SiteComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Request-scoped memoization inside these services only pays off when a
        // single instance is shared across the composer, controllers, and views.
        $this->app->scoped(ImageService::class);
        $this->app->scoped(SiteService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', SiteComposer::class);
    }
}
