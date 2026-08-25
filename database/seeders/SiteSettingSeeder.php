<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SiteSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Program Studi Ilmu Komputer',
                'university_name' => 'Universitas PGRI Wiranegara',
                'faculty_name' => 'Fakultas Teknologi dan Sains',
                'logo' => null,
                'favicon' => null,
                'journal_url' => 'https://ejurnal.uniwara.ac.id',
                'registration_url' => 'https://admisi.uniwara.ac.id',
                'footer_text' => '© 2026 Program Studi Ilmu Komputer.',
                'footer_academic_links' => [
                    [
                        'label' => 'Wiraakademik',
                        'url' => 'https://wiraakademik.uniwara.ac.id',
                    ],
                    [
                        'label' => 'Wiraakademik Mahasiswa',
                        'url' => 'https://student.wiraakademik.uniwara.ac.id',
                    ],
                    [
                        'label' => 'Wiramerdeka MBKM',
                        'url' => 'https://wiramerdeka.uniwara.ac.id',
                    ],
                    [
                        'label' => 'Kalender Akademik',
                        'url' => 'https://wiraakademik.uniwara.ac.id/kalender-akademik',
                    ],
                    [
                        'label' => 'Wiralearning',
                        'url' => 'https://wiralearning.uniwara.ac.id',
                    ],
                ],
            ],
        );
    }
}
