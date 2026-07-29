<?php

use App\Models\User;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Route;

test('only login routes remain public inside the management prefix', function () {
    $publicRouteNames = ['admin.login', 'admin.login.store'];

    $managementRoutes = collect(Route::getRoutes()->getRoutes())
        ->filter(fn ($route): bool => str_starts_with($route->uri(), 'komi-panel'));

    expect($managementRoutes->pluck('action.as')->all())
        ->toContain(...$publicRouteNames);

    $managementRoutes->each(function ($route) use ($publicRouteNames): void {
        if (in_array($route->getName(), $publicRouteNames, true)) {
            expect($route->gatherMiddleware())->not->toContain('auth');

            return;
        }

        expect($route->gatherMiddleware())->toContain('auth');
    });
});

test('guest is redirected to login and the requested admin page is remembered', function () {
    $this->get(route('admin.beranda'))
        ->assertRedirect(route('admin.login'));

    expect(session('url.intended'))->toBe(route('admin.beranda'));
    $this->assertGuest();
});

test('authenticated admin can open a protected management page', function () {
    $admin = User::factory()->make([
        'id' => 1,
        'role' => User::ROLE_ADMIN,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.beranda'))
        ->assertOk()
        ->assertSee('Editor Beranda');
});

test('public pages stay accessible without authentication', function () {
    $this->get(route('home'))
        ->assertOk();

    $this->assertGuest();
});

test('logout is post only and protected by the web csrf middleware', function () {
    $logoutRoute = Route::getRoutes()->getByName('admin.logout');
    $webMiddleware = app(Kernel::class)->getMiddlewareGroups()['web'];

    expect($logoutRoute)->not->toBeNull()
        ->and($logoutRoute->methods())->toBe(['POST'])
        ->and($logoutRoute->gatherMiddleware())->toContain('web', 'auth')
        ->and($webMiddleware)->toContain(PreventRequestForgery::class);
});
