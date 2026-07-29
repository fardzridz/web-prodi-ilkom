<?php

namespace Database\Seeders;

use App\Models\HomeSection;
use Illuminate\Database\Seeder;

class HomeSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        HomeSection::query()->firstOrCreate(
            ['id' => 1],
            [
                'hero_title' => "Logika diasah.\nKreativitas dikembangkan.\nMasa depan diciptakan.",
                'hero_subtitle' => 'Dunia digital terus berubah, membawa tantangan dan peluang baru di setiap langkahnya. Di Program Studi Ilmu Komputer, mahasiswa belajar membangun solusi teknologi yang berguna bagi masyarakat.',
                'hero_slides' => [],
                'cta_text' => 'Lihat Profil',
                'cta_link' => '/profil',
                'welcome_title' => 'Tentang Ilmu Komputer',
                'welcome_description' => 'Program Studi Ilmu Komputer Universitas PGRI Wiranegara menyiapkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, dan siap berkontribusi.',
            ],
        );
    }
}
