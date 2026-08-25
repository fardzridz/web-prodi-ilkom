<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexLecturerRequest;
use App\Http\Requests\Admin\StoreLecturerRequest;
use App\Http\Requests\Admin\UpdateLecturerRequest;
use App\Models\Lecturer;
use App\Services\DashboardCache;
use App\Services\ImageOptimizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class LecturerController extends Controller
{
    public function index(IndexLecturerRequest $request): View
    {
        Gate::authorize('viewAny', Lecturer::class);
        $filters = $request->validated();

        $lecturers = Lecturer::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('nidn', 'like', "%{$search}%")
                        ->orWhere('position', 'like', "%{$search}%")
                        ->orWhere('expertise', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->orderBy('sort_order')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.lecturers.index', [
            'lecturers' => $lecturers,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Lecturer::class);

        return view('admin.lecturers.create', [
            'lecturer' => new Lecturer([
                'status' => Lecturer::STATUS_ACTIVE,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(StoreLecturerRequest $request): RedirectResponse
    {
        Gate::authorize('create', Lecturer::class);

        $data = $request->validated();
        $storedThumb = null;

        if ($request->hasFile('photo')) {
            $optimizer = new ImageOptimizer;
            $result = $optimizer->optimize($request->file('photo'), 'uploads/lecturers');
            $data['photo'] = $result['path'];
            $storedThumb = $result['thumb'];
        } else {
            unset($data['photo']);
        }

        try {
            Lecturer::query()->create($data);
        } catch (Throwable $e) {
            if (isset($result['path'])) {
                Storage::disk('public')->delete($result['path']);
            }
            if ($storedThumb) {
                Storage::disk('public')->delete($storedThumb);
            }
            throw $e;
        }

        Cache::forget('public:expertises');
        DashboardCache::forgetLecturer();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(Lecturer $lecturer): View
    {
        Gate::authorize('update', $lecturer);

        return view('admin.lecturers.edit', [
            'lecturer' => $lecturer,
        ]);
    }

    public function update(UpdateLecturerRequest $request, Lecturer $lecturer): RedirectResponse
    {
        Gate::authorize('update', $lecturer);

        $data = $request->validated();
        $oldPhoto = $lecturer->photo;
        $oldThumb = $oldPhoto ? ImageOptimizer::thumbPath($oldPhoto) : null;
        $storedPhoto = null;
        $storedThumb = null;

        if ($request->hasFile('photo')) {
            $optimizer = new ImageOptimizer;
            $result = $optimizer->optimize($request->file('photo'), 'uploads/lecturers');
            $storedPhoto = $result['path'];
            $storedThumb = $result['thumb'];
            $data['photo'] = $storedPhoto;
        } else {
            unset($data['photo']);
        }

        try {
            $lecturer->update($data);
        } catch (Throwable $exception) {
            if ($storedPhoto !== null) {
                Storage::disk('public')->delete($storedPhoto);
            }
            if ($storedThumb !== null) {
                Storage::disk('public')->delete($storedThumb);
            }

            throw $exception;
        }

        if ($storedPhoto !== null && $oldPhoto !== null && $oldPhoto !== $storedPhoto) {
            Storage::disk('public')->delete($oldPhoto);
            if ($oldThumb) {
                Storage::disk('public')->delete($oldThumb);
            }
        }

        Cache::forget('public:expertises');
        DashboardCache::forgetLecturer();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function toggleStatus(Lecturer $lecturer): RedirectResponse
    {
        Gate::authorize('update', $lecturer);

        $lecturer->update([
            'status' => $lecturer->status === Lecturer::STATUS_ACTIVE
                ? Lecturer::STATUS_INACTIVE
                : Lecturer::STATUS_ACTIVE,
        ]);

        Cache::forget('public:expertises');
        DashboardCache::forgetLecturer();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', "Status {$lecturer->name} berhasil diubah menjadi {$lecturer->statusLabel()}.");
    }

    public function destroy(Lecturer $lecturer): RedirectResponse
    {
        Gate::authorize('delete', $lecturer);
        $photo = $lecturer->photo;
        $thumb = $photo ? ImageOptimizer::thumbPath($photo) : null;
        $lecturer->delete();

        if ($photo) {
            Storage::disk('public')->delete($photo);
        }
        if ($thumb) {
            Storage::disk('public')->delete($thumb);
        }

        Cache::forget('public:expertises');
        DashboardCache::forgetLecturer();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
