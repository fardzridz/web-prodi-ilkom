<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        foreach (['admin@uniwara.ac.id', 'editor@uniwara.ac.id', 'missing@uniwara.ac.id'] as $email) {
            RateLimiter::clear(Str::transliterate($email.'|127.0.0.1'));
        }
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

test('admin login page renders a secure post form without preset credentials', function () {
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertSee('Masuk Pengelola')
        ->assertSee('action="'.route('admin.login.store').'"', escape: false)
        ->assertSee('method="post"', escape: false)
        ->assertSee('name="email"', escape: false)
        ->assertSee('name="password"', escape: false)
        ->assertSee('name="remember"', escape: false)
        ->assertDontSee('value="password"', escape: false);
});

test('admin login validates email and password', function () {
    $this->post(route('admin.login.store'))
        ->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

test('admin cannot login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'admin@uniwara.ac.id',
        'password' => 'correct-password',
        'role' => User::ROLE_ADMIN,
    ]);

    $this->from(route('admin.login'))
        ->post(route('admin.login.store'), [
            'email' => 'admin@uniwara.ac.id',
            'password' => 'wrong-password',
        ])
        ->assertRedirect(route('admin.login'))
        ->assertSessionHasErrors(['email'])
        ->assertSessionHasInput('email', 'admin@uniwara.ac.id');

    $this->assertGuest();
});

test('non admin user cannot login to the management panel', function () {
    User::factory()->create([
        'email' => 'editor@uniwara.ac.id',
        'password' => 'correct-password',
        'role' => 'editor',
    ]);

    $this->post(route('admin.login.store'), [
        'email' => 'editor@uniwara.ac.id',
        'password' => 'correct-password',
    ])->assertSessionHasErrors(['email']);

    $this->assertGuest();
});

test('admin can login with normalized credentials and is redirected to dashboard', function () {
    $admin = User::factory()->create([
        'email' => 'admin@uniwara.ac.id',
        'password' => 'correct-password',
        'role' => User::ROLE_ADMIN,
    ]);

    $this->post(route('admin.login.store'), [
        'email' => ' ADMIN@UNIWARA.AC.ID ',
        'password' => 'correct-password',
        'remember' => '1',
    ])->assertRedirect(route('admin.dashboard', absolute: false));

    $this->assertAuthenticatedAs($admin);
});

test('authenticated admin is redirected away from login page', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->get(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));
});

test('successful login honors the intended admin destination', function () {
    $admin = User::factory()->create([
        'email' => 'admin@uniwara.ac.id',
        'password' => 'correct-password',
        'role' => User::ROLE_ADMIN,
    ]);

    $this->withSession(['url.intended' => route('admin.beranda')])
        ->post(route('admin.login.store'), [
            'email' => 'admin@uniwara.ac.id',
            'password' => 'correct-password',
        ])
        ->assertRedirect(route('admin.beranda'));

    $this->assertAuthenticatedAs($admin);
});

test('repeated failed logins are rate limited', function () {
    $payload = [
        'email' => 'missing@uniwara.ac.id',
        'password' => 'wrong-password',
    ];

    foreach (range(1, 5) as $attempt) {
        $this->post(route('admin.login.store'), $payload)
            ->assertSessionHasErrors(['email']);
    }

    $throttleKey = Str::transliterate('missing@uniwara.ac.id|127.0.0.1');

    expect(RateLimiter::tooManyAttempts($throttleKey, 5))->toBeTrue();

    $this->post(route('admin.login.store'), $payload)
        ->assertSessionHasErrors(['email']);

    expect(session('errors')->first('email'))->toContain('Terlalu banyak percobaan masuk');
});

test('authenticated admin can logout and receives a fresh guest session', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->post(route('admin.logout'))
        ->assertRedirect(route('admin.login'))
        ->assertSessionHas('status', 'Anda telah keluar dari panel pengelola.');

    $this->assertGuest();
});
