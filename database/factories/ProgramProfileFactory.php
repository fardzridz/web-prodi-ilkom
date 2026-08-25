<?php

namespace Database\Factories;

use App\Models\ProgramProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProgramProfile>
 */
class ProgramProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'history' => '<p>'.fake()->paragraph().'</p>',
            'history_image' => null,
            'description' => '<p>'.fake()->paragraph().'</p>',
            'description_image' => null,
            'vision' => '<p>'.fake()->sentence(12).'</p>',
            'mission' => '<ul><li>'.fake()->sentence(8).'</li><li>'.fake()->sentence(8).'</li></ul>',
            'goals' => '<ul><li>'.fake()->sentence(8).'</li></ul>',
            'goals_image' => null,
            'accreditation' => 'Baik Sekali',
            'advantages' => '<ul><li>'.fake()->sentence(8).'</li></ul>',
            'advantages_image' => null,
        ];
    }
}
