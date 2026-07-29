<?php

use App\Models\Activity;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Date;

$configuredDatabase = getenv('DB_CONNECTION');
$databaseDriverIsAvailable = $configuredDatabase !== 'sqlite'
    || in_array('sqlite', PDO::getAvailableDrivers(), true);

if ($databaseDriverIsAvailable) {
    uses(RefreshDatabase::class);
}

afterEach(function () {
    Date::setTestNow();
});

test('only due scheduled activities are automatically published', function () use ($databaseDriverIsAvailable) {
    if (! $databaseDriverIsAvailable) {
        $this->markTestSkipped('The configured SQLite PDO driver is not installed.');
    }

    Date::setTestNow('2026-07-14 10:00:00');

    $this->admin = User::factory()->create([
        'role' => User::ROLE_ADMIN,
    ]);

    $createActivity = fn (string $slug, string $status, string $publishedAt): Activity => Activity::query()->create([
        'user_id' => $this->admin->id,
        'title' => str($slug)->headline(),
        'slug' => $slug,
        'content' => 'Isi kegiatan untuk pengujian jadwal terbit.',
        'activity_date' => '2026-08-20',
        'location' => 'Aula Kampus',
        'status' => $status,
        'published_at' => $publishedAt,
    ]);

    $pastSchedule = $createActivity('jadwal-sudah-lewat', Activity::STATUS_SCHEDULED, '2026-07-14 09:59:00');
    $exactSchedule = $createActivity('jadwal-tepat-waktu', Activity::STATUS_SCHEDULED, '2026-07-14 10:00:00');
    $futureSchedule = $createActivity('jadwal-masa-depan', Activity::STATUS_SCHEDULED, '2026-07-14 10:01:00');
    $draft = $createActivity('draf-dengan-waktu-lampau', Activity::STATUS_DRAFT, '2026-07-14 09:00:00');
    $published = $createActivity('sudah-terbit', Activity::STATUS_PUBLISHED, '2026-07-14 08:00:00');

    expect(Artisan::call('activities:publish-scheduled'))->toBe(0)
        ->and(Artisan::output())->toContain('2 kegiatan terjadwal berhasil diterbitkan.');

    expect($pastSchedule->refresh()->status)->toBe(Activity::STATUS_PUBLISHED)
        ->and($pastSchedule->published_at?->format('Y-m-d H:i:s'))->toBe('2026-07-14 09:59:00')
        ->and($exactSchedule->refresh()->status)->toBe(Activity::STATUS_PUBLISHED)
        ->and($exactSchedule->published_at?->format('Y-m-d H:i:s'))->toBe('2026-07-14 10:00:00')
        ->and($futureSchedule->refresh()->status)->toBe(Activity::STATUS_SCHEDULED)
        ->and($draft->refresh()->status)->toBe(Activity::STATUS_DRAFT)
        ->and($published->refresh()->status)->toBe(Activity::STATUS_PUBLISHED);

    expect(Artisan::call('activities:publish-scheduled'))->toBe(0)
        ->and(Artisan::output())->toContain('0 kegiatan terjadwal berhasil diterbitkan.');
});

test('automatic publication command is scheduled every minute without overlap', function () {
    $event = collect(app(Schedule::class)->events())
        ->first(fn ($event): bool => str_contains($event->command ?? '', 'activities:publish-scheduled'));

    expect($event)->not->toBeNull()
        ->and($event->expression)->toBe('* * * * *')
        ->and($event->withoutOverlapping)->toBeTrue();
});
