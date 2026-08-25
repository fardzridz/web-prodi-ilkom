<?php

namespace Database\Factories;

use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => '<p>'.fake()->paragraph().'</p>',
        ];
    }

    public function privacyPolicy(): static
    {
        return $this->state(fn (array $attributes): array => [
            'title' => 'Kebijakan Privasi',
            'slug' => 'kebijakan-privasi',
        ]);
    }

    public function accessibility(): static
    {
        return $this->state(fn (array $attributes): array => [
            'title' => 'Aksesibilitas',
            'slug' => 'aksesibilitas',
        ]);
    }
}
