<?php

use App\Http\Middleware\VerifyCsrfToken;
use App\Models\User;

// ─── 404 ──────────────────────────────────────────────
test('halaman tidak ditemukan menampilkan error 404', function () {
    $this->get('/halaman-yang-tidak-ada')
        ->assertNotFound()
        ->assertSee('404')
        ->assertSee('Hmmm...')
        ->assertSee('Beranda');
});

// ─── 403 ──────────────────────────────────────────────
test('user non-admin mengakses komi-panel mendapat 403', function () {
    $user = User::factory()->create(['role' => 'user']);

    $this->actingAs($user)
        ->get('/komi-panel/dashboard')
        ->assertForbidden()
        ->assertSee('403')
        ->assertSee('Ups!');
});

// ─── 419 ──────────────────────────────────────────────
test('view 419 menampilkan pesan sesi habis', function () {
    $content = view('errors.419')->render();

    expect($content)->toContain('419')
        ->toContain('Waduh!')
        ->toContain('Sesi kamu sudah habis');
});

// ─── 429 ──────────────────────────────────────────────
test('throttle 5 request/menit di form kontak memunculkan 429', function () {
    $this->withoutMiddleware(VerifyCsrfToken::class);

    for ($i = 0; $i < 5; $i++) {
        $this->post('/kontak', [
            'name' => 'Test',
            'email' => 'test@example.com',
            'subject' => 'Test Subject',
            'message' => 'Test message body.',
        ])->assertRedirect();
    }

    $this->post('/kontak', [
        'name' => 'Test',
        'email' => 'test@example.com',
        'subject' => 'Test Subject',
        'message' => 'Test message body.',
    ])->assertStatus(429);
});

// ─── View existence ───────────────────────────────────
test('semua view error tersedia', function (int $code) {
    $view = view("errors.{$code}");

    expect($view->render())->toContain("{$code}");
})->with([403, 404, 419, 429, 500, 503]);

// ─── Semua route public valid ─────────────────────────
test('semua halaman public mengembalikan 200', function (string $route) {
    $this->get($route)->assertSuccessful();
})->with([
    '/',
    '/profil',
    '/dosen',
    '/kegiatan',
    '/dokumen',
    '/alumni',
    '/kontak',
    '/kebijakan-privasi',
    '/aksesibilitas',
]);

test('route redirect bekerja dengan benar', function (string $route) {
    $this->get($route)->assertRedirect();
})->with([
    '/visi-misi',
    '/jurnal',
]);
