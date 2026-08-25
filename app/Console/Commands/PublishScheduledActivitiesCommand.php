<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Services\DashboardCache;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

#[Signature('activities:publish-scheduled')]
#[Description('Publish activities whose scheduled publication time has arrived')]
class PublishScheduledActivitiesCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $publishedCount = Activity::query()
            ->where('status', Activity::STATUS_SCHEDULED)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->update([
                'status' => Activity::STATUS_PUBLISHED,
            ]);

        if ($publishedCount > 0) {
            Cache::forget('public:activity_categories');
            DashboardCache::forgetActivity();
        }

        $this->info("{$publishedCount} kegiatan terjadwal berhasil diterbitkan.");

        return self::SUCCESS;
    }
}
