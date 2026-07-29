<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Kurikulum', 'slug' => 'kurikulum'],
            ['name' => 'Panduan', 'slug' => 'panduan'],
            ['name' => 'Akreditasi', 'slug' => 'akreditasi'],
            ['name' => 'SOP', 'slug' => 'sop'],
        ];

        foreach ($categories as $category) {
            DocumentCategory::query()->firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            );
        }
    }
}
