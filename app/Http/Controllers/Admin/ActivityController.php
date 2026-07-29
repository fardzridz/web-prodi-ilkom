<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexActivityRequest;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivityController extends Controller
{
    public function index(IndexActivityRequest $request): View
    {
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

    public function create(IndexActivityRequest $request): View
    {
        return view('admin.activities.create', [
            'activity' => new Activity(['status' => Activity::STATUS_DRAFT]),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $data = $this->normalizePublication($request->validated());
        $data['user_id'] = $request->user()->getAuthIdentifier();

        Activity::query()->create($data);

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(IndexActivityRequest $request, Activity $activity): View
    {
        return view('admin.activities.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->update($this->normalizePublication($request->validated(), $activity));

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(IndexActivityRequest $request, Activity $activity): RedirectResponse
    {
        $activity->delete();

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
