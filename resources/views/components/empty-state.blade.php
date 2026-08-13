@props(['heading' => 'Tidak ada data ditemukan', 'message' => 'Coba ubah kata kunci atau filter yang digunakan.'])

<div class="hidden flex-col items-center justify-center gap-4 rounded-2xl border-2 border-dashed border-line px-6 py-16 text-center" id="empty-state">
    <svg class="h-12 w-12 text-muted/40" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
    <h3 class="font-display text-xl font-bold text-muted">{{ $heading }}</h3>
    <p class="text-sm text-muted/70">{{ $message }}</p>
</div>
