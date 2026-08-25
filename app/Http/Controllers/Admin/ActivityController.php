<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexActivityRequest;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Models\Activity;
use App\Services\DashboardCache;
use App\Services\ImageOptimizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ActivityController extends Controller
{
    public function index(IndexActivityRequest $request): View
    {
        Gate::authorize('viewAny', Activity::class);

        $filters = $request->validated();

        $activities = Activity::query()
            ->with('user:id,name')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['activity_date'] ?? null, fn ($query, string $date) => $query->whereDate('activity_date', $date))
            ->orderByDesc('activity_date')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.activities.index', [
            'activities' => $activities,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Activity::class);

        return view('admin.activities.create', [
            'activity' => new Activity(['status' => Activity::STATUS_DRAFT]),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        Gate::authorize('create', Activity::class);

        $data = $this->normalizePublication($request->validated());
        $data['user_id'] = $request->user()->getAuthIdentifier();

        $storedImage = null;
        $storedThumb = null;
        $optimizer = new ImageOptimizer;

        try {
            $activity = DB::transaction(function () use ($request, $data, &$storedImage, &$storedThumb, $optimizer): Activity {
                $payload = $data;

                if ($request->hasFile('image')) {
                    $result = $optimizer->optimize($request->file('image'), 'activities');
                    $storedImage = $result['path'];
                    $storedThumb = $result['thumb'];
                    $payload['image'] = $storedImage;
                }

                return Activity::query()->create($payload);
            });
        } catch (Throwable $exception) {
            if ($storedImage !== null) {
                Storage::disk('public')->delete($storedImage);
            }
            if ($storedThumb !== null) {
                Storage::disk('public')->delete($storedThumb);
            }

            throw $exception;
        }

        Log::info('admin.activity.created', [
            'activity_id' => $activity->id,
            'user_id' => $request->user()->getAuthIdentifier(),
            'slug' => $activity->slug,
            'title' => $activity->title,
            'status' => $activity->status,
            'ip' => $request->ip(),
        ]);

        Cache::forget('public:activity_categories');
        DashboardCache::forgetActivity();

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Activity $activity): View
    {
        Gate::authorize('update', $activity);

        return view('admin.activities.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        Gate::authorize('update', $activity);

        $data = $this->normalizePublication($request->validated(), $activity);
        $oldImage = $activity->image;
        $oldThumb = $oldImage ? ImageOptimizer::thumbPath($oldImage) : null;
        $storedImage = null;
        $storedThumb = null;
        $optimizer = new ImageOptimizer;

        try {
            DB::transaction(function () use ($request, $activity, $data, &$storedImage, &$storedThumb, $optimizer): void {
                $payload = $data;

                if ($request->hasFile('image')) {
                    $result = $optimizer->optimize($request->file('image'), 'activities');
                    $storedImage = $result['path'];
                    $storedThumb = $result['thumb'];
                    $payload['image'] = $storedImage;
                }

                $activity->update($payload);
            });
        } catch (Throwable $exception) {
            if ($storedImage !== null) {
                Storage::disk('public')->delete($storedImage);
            }
            if ($storedThumb !== null) {
                Storage::disk('public')->delete($storedThumb);
            }

            throw $exception;
        }

        if ($storedImage !== null && $oldImage !== null && $oldImage !== $storedImage) {
            Storage::disk('public')->delete($oldImage);
            if ($oldThumb) {
                Storage::disk('public')->delete($oldThumb);
            }
        }

        Log::info('admin.activity.updated', [
            'activity_id' => $activity->id,
            'user_id' => $request->user()->getAuthIdentifier(),
            'slug' => $activity->slug,
            'title' => $activity->title,
            'status' => $activity->status,
            'image_replaced' => $storedImage !== null,
            'ip' => $request->ip(),
        ]);

        Cache::forget('public:activity_categories');
        DashboardCache::forgetActivity();

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        Gate::authorize('delete', $activity);

        $image = $activity->image;
        $thumb = $image ? ImageOptimizer::thumbPath($image) : null;
        $activityId = $activity->id;
        $activitySlug = $activity->slug;
        $activityTitle = $activity->title;
        $activityUserId = $activity->user_id;

        DB::transaction(function () use ($activity): void {
            $activity->delete();
        });

        if ($image) {
            Storage::disk('public')->delete($image);
        }
        if ($thumb) {
            Storage::disk('public')->delete($thumb);
        }

        Log::info('admin.activity.deleted', [
            'activity_id' => $activityId,
            'user_id' => $activityUserId,
            'slug' => $activitySlug,
            'title' => $activityTitle,
            'had_image' => $image !== null,
        ]);

        Cache::forget('public:activity_categories');
        DashboardCache::forgetActivity();

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil dihapus.');
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function normalizePublication(array $data, ?Activity $activity = null): array
    {
        $data['published_at'] = match ($data['status']) {
            Activity::STATUS_SCHEDULED => $data['published_at'],
            Activity::STATUS_PUBLISHED => $activity?->status === Activity::STATUS_PUBLISHED
                ? ($activity->published_at ?? now())
                : now(),
            default => null,
        };

        return $data;
    }
}
