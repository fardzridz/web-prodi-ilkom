<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class ProbeSeeder extends Seeder
{
    public function run(): void
    {
        $row = HomeSection::query()->firstOrCreate(
            ['id' => 1],
            [
                'hero_title' => 'Probe',
                'welcome_title' => 'Probe',
                'advantages' => ['heading' => 'PROBE-HEADING', 'items' => [['order' => 1, 'title' => 'PROBE-SEED', 'description' => 'PROBE', 'image' => null]]],
            ]
        );

        $items = HomeSection::advantageItems($row->advantages ?? []);
        if ($items === [] || ($items[0]['title'] ?? null) !== 'PROBE-SEED') {
            $row->advantages = ['heading' => 'PROBE-HEADING', 'items' => [['order' => 1, 'title' => 'PROBE-SEED', 'description' => 'PROBE', 'image' => null]]];
            $row->save();
            fwrite(STDOUT, 'ADV-WRITTEN'.PHP_EOL);
        } else {
            fwrite(STDOUT, 'ADV-SKIP'.PHP_EOL);
        }

        fwrite(STDOUT, 'DB='.config('database.default').PHP_EOL);
        fwrite(STDOUT, 'DATABASE='.config('database.connections.'.config('database.default').'.database').PHP_EOL);
        fwrite(STDOUT, 'HOST='.config('database.connections.'.config('database.default').'.host').PHP_EOL);
    }
}
