<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Services\Public\SiteService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PageController extends Controller
{
    public function __construct(private readonly SiteService $siteService) {}

    public function privacyPolicy(): View
    {
        return view('public.kebijakan-privasi', [
            'page' => Page::query()->where('slug', 'kebijakan-privasi')->first()
                ?? new Page(['title' => 'Kebijakan Privasi']),
            'seoTitle' => 'Kebijakan Privasi | S1 Ilmu Komputer UNIWARA',
            'seoDesc' => 'Kebijakan privasi website S1 Ilmu Komputer UNIWARA Pasuruan: pengelolaan data, cookies, dan hak pengguna.',
            'canonical' => route('public.privacy-policy'),
        ]);
    }

    public function accessibility(): View
    {
        return view('public.aksesibilitas', [
            'page' => Page::query()->where('slug', 'aksesibilitas')->first()
                ?? new Page(['title' => 'Aksesibilitas']),
            'seoTitle' => 'Aksesibilitas | S1 Ilmu Komputer UNIWARA',
            'seoDesc' => 'Pernyataan aksesibilitas website S1 Ilmu Komputer UNIWARA: komitmen inklusivitas dan akses konten bagi semua pengguna.',
            'canonical' => route('public.accessibility'),
        ]);
    }

    public function journalRedirect(): RedirectResponse
    {
        return redirect()->away($this->siteService->journalUrl());
    }
}
