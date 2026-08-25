<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactMessageRequest;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('public.contact', [
            'seoTitle' => 'Kontak S1 Ilmu Komputer UNIWARA Pasuruan | Alamat & WhatsApp',
            'seoDesc' => 'Hubungi S1 Ilmu Komputer UNIWARA Pasuruan: alamat Jl. Ki Hajar Dewantara 27-29, email, WhatsApp, dan lokasi kampus.',
            'canonical' => route('contact'),
        ]);
    }

    public function store(ContactMessageRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Blade `public/contact.blade.php` currently has no `subject` field,
        // but the table requires it. Provide a sensible default so the form works
        // without forcing the view to add a field.
        if (blank($validated['subject'] ?? null)) {
            $validated['subject'] = 'Pesan dari Website';
        }

        Message::query()->create($validated);

        return redirect()
            ->route('contact')
            ->with('success', 'Pesan Anda berhasil terkirim. Terima kasih atas masukannya.');
    }
}
