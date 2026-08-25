<?php

namespace Database\Factories;

use App\Models\Lecturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lecturer>
 */
class LecturerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'nidn' => (string) fake()->unique()->numerify('##########'),
            'position' => fake()->randomElement(['Dosen Tetap', 'Ketua Program Studi', 'Sekretaris Program Studi']),
            'expertise' => fake()->randomElement(['Kecerdasan Artifisial', 'Rekayasa Perangkat Lunak', 'Jaringan Komputer', 'Sains Data']),
            'education' => fake()->randomElement(['S2 Teknik Informatika', 'S3 Ilmu Komputer']),
            'email' => fake()->unique()->safeEmail(),
            'photo' => null,
            'bio' => fake()->paragraph(),
            'status' => Lecturer::STATUS_ACTIVE,
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes): array => [
            'status' => Lecturer::STATUS_INACTIVE,
        ]);
    }
}
