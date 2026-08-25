<?php

namespace App\Services;

use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Cache;

final class DashboardCache
{
    public static function forgetAll(): void
    {
        Cache::forget('dashboard:counts:activity');
        Cache::forget('dashboard:counts:lecturer');
        Cache::forget('dashboard:counts:document');
        Cache::forget('dashboard:counts:alumni');
        Cache::forget('dashboard:latest_content');
        Cache::forget('dashboard:readiness');
        Cache::forget('dashboard:chart_activity_monthly');
        Cache::forget('dashboard:chart_combined_monthly');
        Cache::forget(SitemapController::CACHE_KEY);
    }

    public static function forgetActivity(): void
    {
        Cache::forget('dashboard:counts:activity');
        Cache::forget('dashboard:latest_content');
        Cache::forget('dashboard:chart_activity_monthly');
        Cache::forget('dashboard:chart_combined_monthly');
        Cache::forget(SitemapController::CACHE_KEY);
    }

    public static function forgetLecturer(): void
    {
        Cache::forget('dashboard:counts:lecturer');
        Cache::forget('dashboard:latest_content');
    }

    public static function forgetDocument(): void
    {
        Cache::forget('dashboard:counts:document');
        Cache::forget('dashboard:latest_content');
    }

    public static function forgetAlumni(): void
    {
        Cache::forget('dashboard:counts:alumni');
        Cache::forget('dashboard:latest_content');
        Cache::forget('dashboard:chart_combined_monthly');
    }

    public static function forgetReadiness(): void
    {
        Cache::forget('dashboard:readiness');
    }
}
