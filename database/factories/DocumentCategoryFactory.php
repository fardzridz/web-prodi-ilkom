<?php

namespace Database\Factories;

use App\Models\DocumentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<DocumentCategory>
 */
class DocumentCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Kurikulum',
            'Panduan Akademik',
            'Akreditasi',
            'Pedoman Kemahasiswaan',
            'Surat Keputusan',
        ]).' '.fake()->unique()->numerify('###');

        return [
            'name' => $name,
            'slug' => Str::slug($name),
        ];
    }
}
