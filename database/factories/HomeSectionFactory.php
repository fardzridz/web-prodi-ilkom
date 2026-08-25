<?php

namespace Database\Factories;

use App\Models\HomeSection;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeSection>
 */
class HomeSectionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'hero_title' => "Logika diasah.\nKreativitas dikembangkan.",
            'hero_subtitle' => fake()->paragraph(),
            'hero_slides' => [],
            'advantages' => [
                'heading' => HomeSection::DEFAULT_ADVANTAGE_HEADING,
                'items' => [
                    ['order' => 1, 'title' => 'Kurikulum Terkini', 'description' => fake()->sentence(), 'image' => null],
                    ['order' => 2, 'title' => 'Dosen Berpengalaman', 'description' => fake()->sentence(), 'image' => null],
                ],
            ],
            'cta_text' => 'Lihat Profil',
            'cta_link' => '/profil',
            'welcome_title' => 'Tentang Ilmu Komputer',
            'welcome_description' => fake()->paragraph(),
            'welcome_image' => null,
        ];
    }
}
