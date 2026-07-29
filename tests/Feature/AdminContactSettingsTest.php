<?php

use App\Models\Contact;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);

    beforeEach(function () {
        $this->admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $this->actingAs($this->admin);
    });
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

$contactPayload = fn (array $overrides = []): array => array_merge([
    'address' => 'Jl. Ki Hajar Dewantara No. 27-29, Pasuruan, Jawa Timur',
    'email' => 'prodi@example.ac.id',
    'phone' => '+62 821-4155-4377',
    'instagram' => 'https://instagram.com/uniwara',
    'youtube' => 'https://youtube.com/@uniwara',
    'facebook' => 'https://facebook.com/uniwara',
    'map_embed' => 'https://www.google.com/maps/embed?pb=data-peta',
], $overrides);

test('admin can open the real single record contact editor', function () use ($contactPayload) {
    Contact::query()->create($contactPayload());

    $this->get(route('admin.kontak'))
        ->assertOk()
        ->assertSee('Kontak Resmi Program Studi')
        ->assertSee('name="address"', escape: false)
        ->assertSee('name="email"', escape: false)
        ->assertSee('name="phone"', escape: false)
        ->assertSee('name="instagram"', escape: false)
        ->assertSee('name="youtube"', escape: false)
        ->assertSee('name="facebook"', escape: false)
        ->assertSee('name="map_embed"', escape: false)
        ->assertSee(route('admin.kontak.update'), escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('admin can update normalized contact data and iframe is reduced to a safe Google Maps URL', function () use ($contactPayload) {
    Contact::query()->create($contactPayload());

    $this->put(route('admin.kontak.update'), $contactPayload([
        'address' => '  Alamat Kampus Data Uji Task 19  ',
        'email' => '  KONTAK@EXAMPLE.AC.ID  ',
        'phone' => '  +62 (821) 4155-4377  ',
        'instagram' => '  https://www.instagram.com/uniwara/  ',
        'map_embed' => '  <iframe src="https://www.google.com/maps/embed?pb=lokasi-kampus" onload="alert(1)"></iframe><script>alert(2)</script>  ',
    ]))
        ->assertRedirect(route('admin.kontak'))
        ->assertSessionHas('success', 'Kontak program studi berhasil diperbarui.');

    $this->assertDatabaseHas('contacts', [
        'address' => 'Alamat Kampus Data Uji Task 19',
        'email' => 'kontak@example.ac.id',
        'phone' => '+62 (821) 4155-4377',
        'instagram' => 'https://www.instagram.com/uniwara/',
        'map_embed' => 'https://www.google.com/maps/embed?pb=lokasi-kampus',
    ]);
    $this->assertDatabaseCount('contacts', 1);

    expect(Contact::query()->sole()->map_embed)
        ->not->toContain('<iframe', '<script', 'onload');
});

test('first and repeated updates keep only one contact row', function () use ($contactPayload) {
    expect(Contact::query()->count())->toBe(0);

    $this->put(route('admin.kontak.update'), $contactPayload())
        ->assertRedirect(route('admin.kontak'));

    $this->put(route('admin.kontak.update'), $contactPayload([
        'phone' => '0343-123456',
    ]))->assertRedirect(route('admin.kontak'));

    $this->assertDatabaseCount('contacts', 1);
    $this->assertDatabaseHas('contacts', [
        'phone' => '0343-123456',
    ]);
});

test('optional social links and map can be cleared', function () use ($contactPayload) {
    Contact::query()->create($contactPayload());

    $this->put(route('admin.kontak.update'), $contactPayload([
        'instagram' => '',
        'youtube' => '',
        'facebook' => '',
        'map_embed' => '',
    ]))->assertRedirect(route('admin.kontak'));

    $contact = Contact::query()->sole();

    expect($contact->instagram)->toBeNull()
        ->and($contact->youtube)->toBeNull()
        ->and($contact->facebook)->toBeNull()
        ->and($contact->map_embed)->toBeNull();
});

test('contact validation rejects invalid channels unsafe domains and non embed map URLs', function () use ($contactPayload) {
    $contact = Contact::query()->create($contactPayload());

    $this->put(route('admin.kontak.update'), $contactPayload([
        'address' => '',
        'email' => 'bukan-email',
        'phone' => 'telepon-rahasia',
        'instagram' => 'https://evil.example/instagram',
        'youtube' => 'javascript:alert(1)',
        'facebook' => 'https://example.com/facebook',
        'map_embed' => 'https://www.google.com/maps/place/kampus',
    ]))->assertSessionHasErrors([
        'address',
        'email',
        'phone',
        'instagram',
        'youtube',
        'facebook',
        'map_embed',
    ]);

    expect($contact->fresh()->email)->toBe('prodi@example.ac.id')
        ->and($contact->fresh()->map_embed)->toBe('https://www.google.com/maps/embed?pb=data-peta');
});

test('authenticated users without admin role cannot manage contact settings', function () use ($contactPayload) {
    Contact::query()->create($contactPayload());
    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.kontak'))->assertForbidden();
    $this->put(route('admin.kontak.update'), $contactPayload([
        'email' => 'tidak-boleh@example.com',
    ]))->assertForbidden();

    $this->assertDatabaseHas('contacts', [
        'email' => 'prodi@example.ac.id',
    ]);
});
