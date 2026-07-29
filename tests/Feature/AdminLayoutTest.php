<?php

use App\Models\User;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

beforeEach(function () {
    $this->actingAs(User::factory()->make([
        'id' => 1,
        'role' => User::ROLE_ADMIN,
    ]));
});

test('admin panel uses the komi panel prefix', function () {
    expect(route('admin.dashboard', absolute: false))->toBe('/komi-panel/dashboard');

    $this->get('/komi-panel')
        ->assertRedirect('/komi-panel/dashboard');

    $this->get('/admin')
        ->assertNotFound();
});

test('admin dashboard renders the reusable management layout', function () use ($databaseDriverIsAvailable) {
    if (! $databaseDriverIsAvailable) {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    }

    $this->get('/komi-panel/dashboard')
        ->assertOk()
        ->assertSee('id="admin-sidebar"', escape: false)
        ->assertSee('Pengelola Situs Prodi')
        ->assertSee('Ringkasan pekerjaan konten')
        ->assertSee('Belum ada aktivitas konten')
        ->assertSee('Kesiapan Halaman Publik');
});

test('admin home module renders the real editor and active navigation', function () {
    $this->get('/komi-panel/beranda')
        ->assertOk()
        ->assertSee('Editor Beranda')
        ->assertSee('name="hero_title"', escape: false)
        ->assertSee('aria-current="page"', escape: false);
});

test('admin layout renders dismissible flash messages', function () use ($databaseDriverIsAvailable) {
    if (! $databaseDriverIsAvailable) {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    }

    $this->withSession(['success' => 'Perubahan berhasil disimpan.'])
        ->get('/komi-panel/dashboard')
        ->assertOk()
        ->assertSee('Perubahan berhasil disimpan.')
        ->assertSee('data-flash-alert', escape: false)
        ->assertSee('data-alert-dismiss', escape: false);
});
