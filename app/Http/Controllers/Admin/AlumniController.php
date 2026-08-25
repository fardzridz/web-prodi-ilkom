<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexAlumniRequest;
use App\Http\Requests\Admin\StoreAlumniRequest;
use App\Http\Requests\Admin\UpdateAlumniRequest;
use App\Models\Alumni;
use App\Services\DashboardCache;
use App\Services\ImageOptimizer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AlumniController extends Controller
{
    public function index(IndexAlumniRequest $request): View
    {
        Gate::authorize('viewAny', Alumni::class);
        $filters = $request->validated();

        $alumni = Alumni::query()
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('job_position', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%")
                        ->orWhere('testimonial', 'like', "%{$search}%");
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when(
                $filters['batch_year'] ?? null,
                fn ($query, int $batchYear) => $query->where('batch_year', $batchYear),
            )
            ->orderByDesc('batch_year')
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.alumni.index', [
            'alumni' => $alumni,
            'batchYears' => Alumni::query()
                ->select('batch_year')
                ->distinct()
                ->orderByDesc('batch_year')
                ->pluck('batch_year'),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        Gate::authorize('create', Alumni::class);

        return view('admin.alumni.create', [
            'alumni' => new Alumni([
                'status' => Alumni::STATUS_ACTIVE,
            ]),
        ]);
    }

    public function store(StoreAlumniRequest $request): RedirectResponse
    {
        Gate::authorize('create', Alumni::class);

        $data = $request->validated();
        $storedThumb = null;

        if ($request->hasFile('photo')) {
            $optimizer = new ImageOptimizer;
            $result = $optimizer->optimize($request->file('photo'), 'uploads/alumni');
            $data['photo'] = $result['path'];
            $storedThumb = $result['thumb'];
        } else {
            unset($data['photo']);
        }

        try {
            Alumni::query()->create($data);
        } catch (Throwable $e) {
            if (isset($result['path'])) {
                Storage::disk('public')->delete($result['path']);
            }
            if ($storedThumb) {
                Storage::disk('public')->delete($storedThumb);
            }
            throw $e;
        }

        Cache::forget('public:job_positions');
        DashboardCache::forgetAlumni();

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(Alumni $alumni): View
    {
        Gate::authorize('update', $alumni);

        return view('admin.alumni.edit', [
            'alumni' => $alumni,
        ]);
    }

    public function update(UpdateAlumniRequest $request, Alumni $alumni): RedirectResponse
    {
        Gate::authorize('update', $alumni);

        $data = $request->validated();
        $oldPhoto = $alumni->photo;
        $oldThumb = $oldPhoto ? ImageOptimizer::thumbPath($oldPhoto) : null;
        $storedPhoto = null;
        $storedThumb = null;

        if ($request->hasFile('photo')) {
            $optimizer = new ImageOptimizer;
            $result = $optimizer->optimize($request->file('photo'), 'uploads/alumni');
            $storedPhoto = $result['path'];
            $storedThumb = $result['thumb'];
            $data['photo'] = $storedPhoto;
        } else {
            unset($data['photo']);
        }

        try {
            $alumni->update($data);
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

        Cache::forget('public:job_positions');
        DashboardCache::forgetAlumni();

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function toggleStatus(Alumni $alumni): RedirectResponse
    {
        Gate::authorize('update', $alumni);

        $alumni->update([
            'status' => $alumni->status === Alumni::STATUS_ACTIVE
                ? Alumni::STATUS_INACTIVE
                : Alumni::STATUS_ACTIVE,
        ]);

        Cache::forget('public:job_positions');
        DashboardCache::forgetAlumni();

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', "Status {$alumni->name} berhasil diubah menjadi {$alumni->statusLabel()}.");
    }

    public function destroy(Alumni $alumni): RedirectResponse
    {
        Gate::authorize('delete', $alumni);
        $photo = $alumni->photo;
        $thumb = $photo ? ImageOptimizer::thumbPath($photo) : null;
        $alumni->delete();

        if ($photo) {
            Storage::disk('public')->delete($photo);
        }
        if ($thumb) {
            Storage::disk('public')->delete($thumb);
        }

        Cache::forget('public:job_positions');
        DashboardCache::forgetAlumni();

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil dihapus.');
    }
}
