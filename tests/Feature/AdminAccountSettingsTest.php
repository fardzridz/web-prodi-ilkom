<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->admin = User::factory()->create([
            'name' => 'Administrator Lama',
            'email' => 'admin-lama@example.test',
            'password' => 'CurrentPass123',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($this->admin);
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

$accountPayload = fn (array $overrides = []): array => array_merge([
    'name' => 'Administrator Prodi',
    'email' => 'admin@example.test',
    'current_password' => 'CurrentPass123',
    'password' => null,
    'password_confirmation' => null,
], $overrides);

test('admin can open the real account editor', function () {
    $this->get(route('admin.akun-admin'))
        ->assertOk()
        ->assertSee('Informasi Akun')
        ->assertSee('Ganti Kata Sandi')
        ->assertSee('name="name"', escape: false)
        ->assertSee('name="email"', escape: false)
        ->assertSee('name="current_password"', escape: false)
        ->assertSee('name="password"', escape: false)
        ->assertSee('name="password_confirmation"', escape: false)
        ->assertSee('readonly', escape: false)
        ->assertSee(route('admin.akun-admin.update'), escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false)
        ->assertDontSee('Halaman ini masih berupa placeholder');
});

test('admin can update normalized identity without changing password or role', function () use ($accountPayload) {
    $oldPassword = $this->admin->password;

    $this->put(route('admin.akun-admin.update'), $accountPayload([
        'name' => '  Administrator Ilmu Komputer  ',
        'email' => '  ADMIN.BARU@EXAMPLE.TEST  ',
    ]))
        ->assertRedirect(route('admin.akun-admin'))
        ->assertSessionHas('success', 'Akun pengelola berhasil diperbarui.');

    $admin = $this->admin->fresh();

    expect($admin->name)->toBe('Administrator Ilmu Komputer')
        ->and($admin->email)->toBe('admin.baru@example.test')
        ->and($admin->password)->toBe($oldPassword)
        ->and($admin->role)->toBe(User::ROLE_ADMIN);

    $this->assertAuthenticatedAs($admin);
});

test('admin can replace the password with a strong confirmed password', function () use ($accountPayload) {
    $this->put(route('admin.akun-admin.update'), $accountPayload([
        'password' => 'NewSecurePass456',
        'password_confirmation' => 'NewSecurePass456',
    ]))
        ->assertRedirect(route('admin.akun-admin'))
        ->assertSessionHasNoErrors();

    $admin = $this->admin->fresh();

    expect(Hash::check('NewSecurePass456', $admin->password))->toBeTrue()
        ->and($admin->role)->toBe(User::ROLE_ADMIN);

    $this->assertAuthenticatedAs($admin);
});

test('wrong current password rejects every account change', function () use ($accountPayload) {
    $this->from(route('admin.akun-admin'))
        ->put(route('admin.akun-admin.update'), $accountPayload([
            'name' => 'Nama Tidak Boleh Tersimpan',
            'email' => 'tidak-boleh@example.test',
            'current_password' => 'WrongCurrentPass123',
        ]))
        ->assertRedirect(route('admin.akun-admin'))
        ->assertSessionHasErrors('current_password');

    $admin = $this->admin->fresh();

    expect($admin->name)->toBe('Administrator Lama')
        ->and($admin->email)->toBe('admin-lama@example.test')
        ->and(Hash::check('CurrentPass123', $admin->password))->toBeTrue();
});

test('duplicate email weak password and mismatched confirmation are rejected', function () use ($accountPayload) {
    User::factory()->create(['email' => 'dipakai@example.test']);

    $this->put(route('admin.akun-admin.update'), $accountPayload([
        'email' => 'dipakai@example.test',
        'password' => 'lemah',
        'password_confirmation' => 'berbeda',
    ]))->assertSessionHasErrors(['email', 'password']);

    $this->put(route('admin.akun-admin.update'), $accountPayload([
        'password' => 'StrongPassword789',
        'password_confirmation' => 'StrongPassword780',
    ]))->assertSessionHasErrors('password');

    expect($this->admin->fresh()->email)->toBe('admin-lama@example.test')
        ->and(Hash::check('CurrentPass123', $this->admin->fresh()->password))->toBeTrue();
});

test('authenticated users without admin role cannot manage the account', function () use ($accountPayload) {
    $editor = User::factory()->create([
        'password' => 'EditorPass123',
        'role' => 'editor',
    ]);
    $this->actingAs($editor);

    $this->get(route('admin.akun-admin'))->assertForbidden();
    $this->put(route('admin.akun-admin.update'), $accountPayload([
        'name' => 'Tidak Boleh Diubah',
        'email' => 'editor-baru@example.test',
        'current_password' => 'EditorPass123',
    ]))->assertForbidden();

    expect($editor->fresh()->name)->not->toBe('Tidak Boleh Diubah')
        ->and($editor->fresh()->email)->not->toBe('editor-baru@example.test');
});
