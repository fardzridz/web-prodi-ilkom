<?php

namespace App\Console\Commands;

use App\Models\Activity;
use App\Models\ProgramProfile;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('rich:convert')]
#[Description('Convert existing Froala rich text to plain HTML')]
class ConvertRichTextCommand extends Command
{
    public function handle()
    {
        $converter = function ($html) {
            if (! $html) {
                return '';
            }
            $html = str_replace(['<br>', '<br/>', '<br />'], "\n", $html);
            $html = strip_tags($html, '<p><b><strong><i><em><ul><ol><li><a><blockquote><h1><h2><h3><h4>');

            return trim($html);
        };

        $this->info('Starting rich text conversion...');

        // Program Profile
        $profiles = ProgramProfile::all();
        foreach ($profiles as $profile) {
            $profile->update([
                'description' => $converter($profile->description),
                'history' => $converter($profile->history),
                'vision' => $converter($profile->vision),
                'mission' => $converter($profile->mission),
                'goals' => $converter($profile->goals),
                'advantages' => $converter($profile->advantages),
            ]);
        }

        // Activities
        $activities = Activity::all();
        foreach ($activities as $activity) {
            $activity->update(['content' => $converter($activity->content)]);
        }

        $this->info('✅ All rich text successfully converted to HTML!');
    }
}
