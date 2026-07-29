<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexLecturerRequest;
use App\Http\Requests\Admin\StoreLecturerRequest;
use App\Http\Requests\Admin\UpdateLecturerRequest;
use App\Models\Lecturer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class LecturerController extends Controller
{
    public function index(IndexLecturerRequest $request): View
    {
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

    public function create(IndexLecturerRequest $request): View
    {
        return view('admin.lecturers.create', [
            'lecturer' => new Lecturer([
                'status' => Lecturer::STATUS_ACTIVE,
                'sort_order' => 0,
            ]),
        ]);
    }

    public function store(StoreLecturerRequest $request): RedirectResponse
    {
        Lecturer::query()->create($request->validated());

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit(IndexLecturerRequest $request, Lecturer $lecturer): View
    {
        return view('admin.lecturers.edit', [
            'lecturer' => $lecturer,
        ]);
    }

    public function update(UpdateLecturerRequest $request, Lecturer $lecturer): RedirectResponse
    {
        $lecturer->update($request->validated());

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function toggleStatus(IndexLecturerRequest $request, Lecturer $lecturer): RedirectResponse
    {
        $lecturer->update([
            'status' => $lecturer->status === Lecturer::STATUS_ACTIVE
                ? Lecturer::STATUS_INACTIVE
                : Lecturer::STATUS_ACTIVE,
        ]);

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', "Status {$lecturer->name} berhasil diubah menjadi {$lecturer->statusLabel()}.");
    }

    public function destroy(IndexLecturerRequest $request, Lecturer $lecturer): RedirectResponse
    {
        $lecturer->delete();

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
