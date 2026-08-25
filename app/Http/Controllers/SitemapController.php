<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public const CACHE_KEY = 'sitemap';

    public function __invoke(): Response
    {
        $urls = Cache::remember(self::CACHE_KEY, 3600, function (): Collection {
            $today = now()->toDateString();

            $static = collect([
                ['loc' => route('home'), 'lastmod' => $today, 'priority' => '1.0'],
                ['loc' => route('profile'), 'lastmod' => $today, 'priority' => '0.8'],
                ['loc' => route('vision-mission'), 'lastmod' => $today, 'priority' => '0.8'],
                ['loc' => route('lecturers'), 'lastmod' => $today, 'priority' => '0.7'],
                ['loc' => route('activities.index'), 'lastmod' => $today, 'priority' => '0.8'],
                ['loc' => route('alumni'), 'lastmod' => $today, 'priority' => '0.7'],
                ['loc' => route('documents'), 'lastmod' => $today, 'priority' => '0.7'],
                ['loc' => route('contact'), 'lastmod' => $today, 'priority' => '0.6'],
                ['loc' => route('public.privacy-policy'), 'lastmod' => $today, 'priority' => '0.3'],
                ['loc' => route('public.accessibility'), 'lastmod' => $today, 'priority' => '0.3'],
            ]);

            $activities = Activity::query()
                ->select(['slug', 'updated_at', 'activity_date'])
                ->where('status', Activity::STATUS_PUBLISHED)
                ->latest('activity_date')
                ->limit(100)
                ->get()
                ->map(fn (Activity $activity): array => [
                    'loc' => route('activities.show', $activity->slug),
                    'lastmod' => ($activity->updated_at ?? $activity->activity_date ?? now())->toDateString(),
                    'priority' => '0.6',
                ]);

            return $static->concat($activities)->unique('loc')->values();
        });

        return response()->view('sitemap', ['urls' => $urls])->header('Content-Type', 'text/xml');
    }
}
