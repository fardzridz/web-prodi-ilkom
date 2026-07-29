<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::query()->firstOrCreate(
            ['id' => 1],
            [
                'address' => 'Jl. Ki Hajar Dewantara No. 27-29, Pasuruan, Jawa Timur',
                'email' => 'univ.pgriwiranegara@gmail.com',
                'phone' => '0821-4155-4377',
                'instagram' => 'https://instagram.com/uniwara',
                'youtube' => 'https://youtube.com/@uniwara',
                'facebook' => 'https://facebook.com/uniwara',
                'map_embed' => null,
            ],
        );
    }
}
