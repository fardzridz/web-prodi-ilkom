<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IndexLecturerRequest;
use App\Http\Requests\Admin\StoreLecturerRequest;
use App\Http\Requests\Admin\UpdateLecturerRequest;
use App\Models\Lecturer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Throwable;

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
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('uploads/lecturers', 'public');
        } else {
            unset($data['photo']);
        }

        Lecturer::query()->create($data);

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
        $data = $request->validated();
        $oldPhoto = $lecturer->photo;
        $storedPhoto = null;

        if ($request->hasFile('photo')) {
            $storedPhoto = $request->file('photo')->store('uploads/lecturers', 'public');
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

            throw $exception;
        }

        if ($storedPhoto !== null && $oldPhoto !== null && $oldPhoto !== $storedPhoto) {
            Storage::disk('public')->delete($oldPhoto);
        }

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
        $photo = $lecturer->photo;
        $lecturer->delete();

        if ($photo) {
            Storage::disk('public')->delete($photo);
        }

        return redirect()
            ->route('admin.dosen.index')
            ->with('success', 'Data dosen berhasil dihapus.');
    }
}
