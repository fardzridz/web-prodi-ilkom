<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexAlumniRequest;
use App\Http\Requests\Admin\StoreAlumniRequest;
use App\Http\Requests\Admin\UpdateAlumniRequest;
use App\Models\Alumni;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AlumniController extends Controller
{
    public function index(IndexAlumniRequest $request): View
    {
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

    public function create(IndexAlumniRequest $request): View
    {
        return view('admin.alumni.create', [
            'alumni' => new Alumni([
                'status' => Alumni::STATUS_ACTIVE,
            ]),
        ]);
    }

    public function store(StoreAlumniRequest $request): RedirectResponse
    {
        Alumni::query()->create($request->validated());

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil ditambahkan.');
    }

    public function edit(IndexAlumniRequest $request, Alumni $alumni): View
    {
        return view('admin.alumni.edit', [
            'alumni' => $alumni,
        ]);
    }

    public function update(UpdateAlumniRequest $request, Alumni $alumni): RedirectResponse
    {
        $alumni->update($request->validated());

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil diperbarui.');
    }

    public function toggleStatus(IndexAlumniRequest $request, Alumni $alumni): RedirectResponse
    {
        $alumni->update([
            'status' => $alumni->status === Alumni::STATUS_ACTIVE
                ? Alumni::STATUS_INACTIVE
                : Alumni::STATUS_ACTIVE,
        ]);

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', "Status {$alumni->name} berhasil diubah menjadi {$alumni->statusLabel()}.");
    }

    public function destroy(IndexAlumniRequest $request, Alumni $alumni): RedirectResponse
    {
        $alumni->delete();

        return redirect()
            ->route('admin.alumni.index')
            ->with('success', 'Data alumni berhasil dihapus.');
    }
}
