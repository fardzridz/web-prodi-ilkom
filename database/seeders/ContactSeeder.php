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
                'instagram' => 'https://www.instagram.com/compscienceuniwara',
                'youtube' => 'https://youtube.com/@uniwara',
                'facebook' => 'https://facebook.com/uniwara',
                'map_embed' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3954.1338436862375!2d112.90022727602569!3d-7.668756675901799!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd7cf53a67427b3%3A0x1458c6500c5d88d0!2sUniversitas%20PGRI%20Wiranegara!5e0!3m2!1sid!2sid!4v1787710543223!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>',
            ],
        );
    }
}
