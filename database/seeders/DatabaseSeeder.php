<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SiteSettingSeeder::class,
            HomeSectionSeeder::class,
            ProgramProfileSeeder::class,
            PageSeeder::class,
            ContactSeeder::class,
            DocumentCategorySeeder::class,
            LecturerSeeder::class,
            AlumniSeeder::class,
        ]);
    }
}
