<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLecturerRequest;
use App\Http\Requests\Admin\UpdateLecturerRequest;
use App\Models\Lecturer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DosenController extends Controller
{
    public function index(): View
    {
        $lecturers = Lecturer::orderBy('sort_order')
            ->paginate(10);

        return view('admin.dosen.index', compact('lecturers'));
    }

    public function create(): View
    {
        return view('admin.dosen.create');
    }

    public function store(StoreLecturerRequest $request): RedirectResponse
    {
        $lecturer = Lecturer::create($request->validated());

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil ditambahkan.');
    }

    public function edit(Lecturer $lecturer): View
    {
        return view('admin.dosen.edit', compact('lecturer'));
    }

    public function update(UpdateLecturerRequest $request, Lecturer $lecturer): RedirectResponse
    {
        $lecturer->update($request->validated());

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil diperbarui.');
    }

    public function destroy(Lecturer $lecturer): RedirectResponse
    {
        $lecturer->delete();

        return redirect()->route('admin.dosen.index')
            ->with('success', 'Dosen berhasil dihapus.');
    }

    public function toggleStatus(Request $request, Lecturer $lecturer): RedirectResponse
    {
        $lecturer->update(['status' => $lecturer->status === 'active' ? 'inactive' : 'active']);

        return redirect()->back();
    }
}
