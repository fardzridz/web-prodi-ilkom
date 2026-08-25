<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contact>
 */
class ContactFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'address' => 'Jl. Ki Hajar Dewantara No. 27-29, Pasuruan, Jawa Timur',
            'email' => fake()->safeEmail(),
            'phone' => '0821-4155-4377',
            'instagram' => 'https://instagram.com/uniwara',
            'youtube' => 'https://youtube.com/@uniwara',
            'facebook' => 'https://facebook.com/uniwara',
            'map_embed' => null,
        ];
    }
}
