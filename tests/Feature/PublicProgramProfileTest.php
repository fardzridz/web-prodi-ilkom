<?php

use App\Models\ProgramProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);
} else {
    beforeEach(function () {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    });
}

/**
 * Extract the inner HTML of a public profile field component by data-profile-field.
 */
function profileFieldHtml(string $pageHtml, string $field): string
{
    $pattern = '/data-profile-field="'.preg_quote($field, '/').'"[^>]*>(.*?)<\/div>/s';

    expect($pageHtml)->toMatch($pattern);

    preg_match($pattern, $pageHtml, $matches);

    return $matches[1] ?? '';
}

test('public profile renders sanitized rich html and keeps quill delta path', function () {
    $delta = json_encode([
        'ops' => [
            ['insert' => "Visi aman dari delta\n"],
        ],
    ], JSON_THROW_ON_ERROR);

    ProgramProfile::query()->create([
        'history' => '<p>Sejarah <strong>aman</strong></p><script>alert(1)</script>',
        'description' => '<p onclick="alert(1)">Deskripsi</p><a href="javascript:alert(1)">link</a><a href="https://example.com">ok</a>',
        'vision' => $delta,
        'mission' => '<p>Misi</p><img src=x onerror=alert(1)>',
        'goals' => '<ul><li>Tujuan satu</li></ul><iframe src="https://evil.test"></iframe>',
        'accreditation' => 'Baik Sekali',
        'advantages' => '<p>Keunggulan <em>prodi</em></p>',
    ]);

    $response = $this->get(route('profile'))->assertOk();
    $html = $response->getContent();

    $history = profileFieldHtml($html, 'history');
    $description = profileFieldHtml($html, 'description');
    $mission = profileFieldHtml($html, 'mission');
    $goals = profileFieldHtml($html, 'goals');
    $advantages = profileFieldHtml($html, 'advantages');

    expect($history)
        ->toContain('<strong>aman</strong>')
        ->toContain('Sejarah')
        ->not->toContain('<script');

    expect($description)
        ->toContain('Deskripsi')
        ->toContain('href="https://example.com"')
        ->not->toContain('onclick=')
        ->not->toContain('javascript:')
        ->toContain('<a>link</a>');

    expect($mission)
        ->toContain('<p>Misi</p>')
        ->not->toContain('<img')
        ->not->toContain('onerror=');

    expect($goals)
        ->toContain('Tujuan satu')
        ->not->toContain('<iframe');

    expect($advantages)
        ->toContain('Keunggulan')
        ->toContain('<em>prodi</em>');

    expect($html)->toContain('data-quill-delta="'.base64_encode($delta).'"');
    // Quill delta path must not echo raw JSON/HTML body; only the data attribute.
    expect(trim(profileFieldHtml($html, 'vision')))->toBe('');
});

test('public profile falls back when program profile is missing', function () {
    $this->get(route('profile'))
        ->assertOk()
        ->assertSee('Profil Program Studi', escape: false);
});
