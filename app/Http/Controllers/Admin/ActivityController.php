<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexActivityRequest;
use App\Http\Requests\Admin\StoreActivityRequest;
use App\Http\Requests\Admin\UpdateActivityRequest;
use App\Models\Activity;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

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

    public function create(): View
    {
        return view('admin.activities.create', [
            'activity' => new Activity(['status' => Activity::STATUS_DRAFT]),
        ]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $data = $this->normalizePublication($request->validated());
        $data['user_id'] = $request->user()->getAuthIdentifier();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('activities', 'public');
        }

        Activity::query()->create($data);

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function edit(Activity $activity): View
    {
        return view('admin.activities.edit', [
            'activity' => $activity,
        ]);
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $data = $this->normalizePublication($request->validated(), $activity);
        $oldImage = $activity->image;
        $storedImage = null;

        if ($request->hasFile('image')) {
            $storedImage = $request->file('image')->store('activities', 'public');
            $data['image'] = $storedImage;
        }

        try {
            $activity->update($data);
        } catch (Throwable $exception) {
            if ($storedImage !== null) {
                Storage::disk('public')->delete($storedImage);
            }

            throw $exception;
        }

        if ($storedImage !== null && $oldImage !== null && $oldImage !== $storedImage) {
            Storage::disk('public')->delete($oldImage);
        }

        return redirect()
            ->route('admin.kegiatan.index')
            ->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        $image = $activity->image;
        $activity->delete();

        if ($image) {
            Storage::disk('public')->delete($image);
        }

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
