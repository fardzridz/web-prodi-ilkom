<?php

namespace Database\Factories;

use App\Models\Alumni;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumni>
 */
class AlumniFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $batchYear = fake()->numberBetween(2010, 2022);

        return [
            'name' => fake()->name(),
            'batch_year' => $batchYear,
            'graduation_year' => $batchYear + 4,
            'job_position' => fake()->randomElement(['Software Engineer', 'Data Analyst', 'IT Consultant', 'Guru Informatika']),
            'company' => fake()->company(),
            'testimonial' => fake()->paragraph(),
            'photo' => null,
            'status' => Alumni::STATUS_ACTIVE,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Alumni::STATUS_INACTIVE,
        ]);
    }
}
