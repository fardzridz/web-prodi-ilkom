<?php

namespace Database\Factories;

use App\Models\SiteSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SiteSetting>
 */
class SiteSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'site_name' => 'Program Studi Ilmu Komputer',
            'university_name' => 'Universitas PGRI Wiranegara',
            'faculty_name' => 'Fakultas Teknologi dan Sains',
            'logo' => null,
            'favicon' => null,
            'journal_url' => 'https://ejurnal.uniwara.ac.id',
            'registration_url' => 'https://admisi.uniwara.ac.id',
            'footer_text' => '© '.date('Y').' Program Studi Ilmu Komputer.',
            'footer_academic_links' => [],
        ];
    }
}
