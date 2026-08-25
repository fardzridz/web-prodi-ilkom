<?php

use App\Models\Activity;
use App\Models\Alumni;
use App\Models\Contact;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\HomeSection;
use App\Models\Lecturer;
use App\Models\ProgramProfile;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();

    SiteSetting::factory()->create();
    Contact::factory()->create();
    HomeSection::factory()->create();
    ProgramProfile::factory()->create();
});

/**
 * @return list<string>
 */
function adminGetRoutes(): array
{
    return [
        'admin.dashboard',
        'admin.beranda',
        'admin.profil',
        'admin.kontak',
        'admin.pengaturan',
        'admin.akun-admin',
        'admin.halaman',
        'admin.dosen.index',
        'admin.kegiatan.index',
        'admin.dokumen.index',
        'admin.kategori-dokumen.index',
        'admin.alumni.index',
    ];
}

// ─── Guest ────────────────────────────────────────────────────────────────

test('guest dialihkan ke halaman login admin', function (string $routeName): void {
    $this->get(route($routeName))->assertRedirect(route('admin.login'));
})->with(adminGetRoutes());

test('halaman login admin dapat diakses guest', function (): void {
    $this->get(route('admin.login'))->assertSuccessful();
});

// ─── Non-admin ────────────────────────────────────────────────────────────

test('user non-admin mendapat 403 di seluruh panel', function (string $routeName): void {
    $this->actingAs(User::factory()->nonAdmin()->create())
        ->get(route($routeName))
        ->assertForbidden();
})->with(adminGetRoutes());

// ─── Admin ────────────────────────────────────────────────────────────────

test('admin dapat membuka seluruh halaman panel', function (string $routeName): void {
    Lecturer::factory()->count(2)->create();
    Activity::factory()->count(2)->create();
    Document::factory()->count(2)->create();
    Alumni::factory()->count(2)->create();
    DocumentCategory::factory()->count(2)->create();

    $this->actingAs(User::factory()->admin()->create())
        ->get(route($routeName))
        ->assertSuccessful();
})->with(adminGetRoutes());

test('admin dapat membuka form create dan edit resource', function (string $routeName, ?string $factory): void {
    $admin = User::factory()->admin()->create();

    $parameters = $factory !== null ? [$factory::factory()->create()] : [];

    $this->actingAs($admin)->get(route($routeName, $parameters))->assertSuccessful();
})->with([
    ['admin.dosen.create', null],
    ['admin.kegiatan.create', null],
    ['admin.dokumen.create', null],
    ['admin.dosen.edit', Lecturer::class],
    ['admin.kegiatan.edit', Activity::class],
    ['admin.dokumen.edit', Document::class],
    ['admin.alumni.edit', Alumni::class],
]);

test('admin diarahkan dari root panel ke dashboard', function (): void {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.home'))
        ->assertRedirect(route('admin.dashboard'));
});

// ─── Autentikasi ──────────────────────────────────────────────────────────

test('admin dapat masuk dengan kredensial yang benar', function (): void {
    $admin = User::factory()->admin()->create(['email' => 'admin@uniwara.test']);

    $this->post(route('admin.login.store'), [
        'email' => 'admin@uniwara.test',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($admin);
});

test('kredensial salah tidak mengautentikasi', function (): void {
    User::factory()->admin()->create(['email' => 'admin@uniwara.test']);

    $this->post(route('admin.login.store'), [
        'email' => 'admin@uniwara.test',
        'password' => 'kata-sandi-salah',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('user non-admin tidak dapat masuk lewat form login panel', function (): void {
    User::factory()->nonAdmin()->create(['email' => 'user@uniwara.test']);

    $this->post(route('admin.login.store'), [
        'email' => 'user@uniwara.test',
        'password' => 'password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

test('percobaan login dibatasi setelah lima kegagalan', function (): void {
    User::factory()->admin()->create(['email' => 'admin@uniwara.test']);

    $payload = ['email' => 'admin@uniwara.test', 'password' => 'salah'];

    for ($i = 0; $i < 5; $i++) {
        $this->post(route('admin.login.store'), $payload);
    }

    $this->post(route('admin.login.store'), $payload)
        ->assertSessionHasErrorsIn('default', ['email']);

    expect(session('errors')->get('email')[0])->toContain('Terlalu banyak percobaan masuk');
});

test('admin dapat keluar dari panel', function (): void {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'));

    $this->assertGuest();
});
