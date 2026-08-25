<?php

namespace Database\Factories;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Activity>
 */
class ActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('####'),
            'excerpt' => fake()->sentence(12),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'image' => null,
            'activity_date' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'location' => fake()->city(),
            'category' => fake()->randomElement(['Seminar', 'Workshop', 'Kompetisi', 'Pengabdian']),
            'status' => Activity::STATUS_PUBLISHED,
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Activity::STATUS_DRAFT,
            'published_at' => null,
        ]);
    }

    public function scheduled(?string $publishedAt = null): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Activity::STATUS_SCHEDULED,
            'published_at' => $publishedAt ?? now()->addDay(),
        ]);
    }
}
