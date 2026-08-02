<?php

use App\Models\Page;
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

test('admin can open the static pages index', function () {
    $this->get(route('admin.halaman'))
        ->assertOk()
        ->assertSee('Kebijakan Privasi')
        ->assertSee('Aksesibilitas')
        ->assertSee(route('admin.halaman.edit', ['slug' => 'kebijakan-privasi']), escape: false);
});

test('admin can open a static page editor with the quill integration', function () {
    $this->get(route('admin.halaman.edit', ['slug' => 'kebijakan-privasi']))
        ->assertOk()
        ->assertSee('class="quill-editor"', escape: false)
        ->assertSee('name="content"', escape: false)
        ->assertSee('name="_method" value="PUT"', escape: false);
});

test('admin can update a static page content', function () {
    $html = '<h2>Judul Baru</h2><p>Isi kebijakan diperbarui.</p>';

    $this->put(route('admin.halaman.update', ['slug' => 'kebijakan-privasi']), [
        'content' => $html,
    ])
        ->assertRedirect(route('admin.halaman.edit', ['slug' => 'kebijakan-privasi']))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('pages', [
        'slug' => 'kebijakan-privasi',
        'content' => $html,
    ]);
});

test('public visitors can read the privacy policy page with rendered content', function () {
    Page::query()->where('slug', 'kebijakan-privasi')->update([
        'content' => '<h2>Kebijakan Kami</h2><p>Kami melindungi data Anda.</p>',
    ]);

    $this->get(route('public.privacy-policy'))
        ->assertOk()
        ->assertSee('Kebijakan Privasi')
        ->assertSee('Kami melindungi data Anda.')
        ->assertSee('<p>Kami melindungi data Anda.</p>', escape: false);
});

test('public pages show a friendly placeholder while content is empty', function () {
    $this->get(route('public.accessibility'))
        ->assertOk()
        ->assertSee('Konten sedang disiapkan');
});

test('non-admin users cannot edit static pages', function () {
    $this->actingAs(User::factory()->create(['role' => 'editor']));

    $this->get(route('admin.halaman.edit', ['slug' => 'kebijakan-privasi']))
        ->assertForbidden();

    $this->put(route('admin.halaman.update', ['slug' => 'kebijakan-privasi']), [
        'content' => 'tidak boleh',
    ])->assertForbidden();

    expect(Page::query()->where('slug', 'kebijakan-privasi')->value('content'))->toBeNull();
});

test('sanitized content accessor strips dangerous html', function () {
    $page = Page::query()->create([
        'title' => 'Uji',
        'slug' => 'uji-sanitasi',
        'content' => '<script>alert(1)</script><p onclick="x()">Teks aman</p><a href="javascript:alert(1)">Link</a>',
    ]);

    expect($page->sanitized_content)
        ->not->toContain('<script')
        ->not->toContain('onclick')
        ->not->toContain('javascript:')
        ->toContain('Teks aman');
});

test('page model exposes the documented fillable attributes', function () {
    expect((new Page)->getFillable())->toBe(['title', 'slug', 'content']);
});
