@props(['icon' => 'fa-search', 'heading' => 'Tidak ada data ditemukan', 'message' => 'Coba ubah kata kunci atau filter yang digunakan.'])

<div class="hidden flex-col items-center justify-center gap-4 rounded-2xl border-2 border-dashed border-line px-6 py-16 text-center" id="empty-state">
    <i class="fa-solid {{ $icon }} text-5xl text-muted/40"></i>
    <h3 class="font-display text-xl font-bold text-muted">{{ $heading }}</h3>
    <p class="text-sm text-muted/70">{{ $message }}</p>
</div>
