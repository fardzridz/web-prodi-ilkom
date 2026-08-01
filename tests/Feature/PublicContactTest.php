<?php

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

test('contact page renders the public form', function () {
    $this->get(route('contact'))
        ->assertOk()
        ->assertSee('name="name"', escape: false)
        ->assertSee('name="email"', escape: false)
        ->assertSee('name="subject"', escape: false)
        ->assertSee('name="message"', escape: false)
        ->assertSee('action="'.route('contact.store').'"', escape: false);
});

test('visitor can submit a contact message', function () {
    $this->post(route('contact.store'), [
        'name' => '  Budi Santoso  ',
        'email' => ' BUDI@EXAMPLE.COM ',
        'subject' => '  Pertanyaan Pendaftaran  ',
        'message' => 'Apakah prodi membuka kelas sore?',
    ])
        ->assertRedirect(route('contact'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('messages', [
        'name' => 'Budi Santoso',
        'email' => 'budi@example.com',
        'subject' => 'Pertanyaan Pendaftaran',
        'message' => 'Apakah prodi membuka kelas sore?',
    ]);
});

test('contact submission is validated', function () {
    $this->post(route('contact.store'), [
        'name' => '',
        'email' => 'bukan-email',
        'subject' => '',
        'message' => '',
    ])->assertSessionHasErrors(['name', 'email', 'subject', 'message']);

    $this->assertDatabaseCount('messages', 0);
});
