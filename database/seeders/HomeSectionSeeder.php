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
                'advantages' => [
                    'heading' => 'Mengapa Memilih Ilmu Komputer?',
                    'items' => [
                        ['order' => 1, 'title' => 'Kurikulum Terkini', 'description' => 'Mata kuliah disusun bersama praktisi industri dan terus diperbarui mengikuti perkembangan teknologi.', 'image' => 'uploads/home/logone-5xsaek.webp'],
                        ['order' => 2, 'title' => 'Dosen Berpengalaman', 'description' => 'Belajar langsung dari akademisi dan praktisi yang aktif di dunia industri serta riset.', 'image' => null],
                        ['order' => 3, 'title' => 'Fasilitas Lengkap', 'description' => 'Laboratorium komputer, jaringan, dan riset modern untuk mendukung praktikum mahasiswa.', 'image' => null],
                        ['order' => 4, 'title' => 'Peluang Karier Luas', 'description' => 'Lulusan siap berkiprah sebagai software engineer, data scientist, hingga technopreneur.', 'image' => null],
                        ['order' => 5, 'title' => 'Komunitas Aktif', 'description' => 'Himpunan mahasiswa dan unit kegiatan membuka ruang mengasah soft skill dan berprestasi.', 'image' => null],
                        ['order' => 6, 'title' => 'Dukungan Beasiswa', 'description' => 'Beragam program beasiswa dan keringanan biaya untuk mahasiswa berprestasi.', 'image' => null],
                    ],
                ],
                'cta_text' => 'Lihat Profil',
                'cta_link' => '/profil',
                'welcome_title' => 'Assalamualaikum warahmatullahi wabarakatuh.',
                'welcome_description' => 'Puji syukur kita panjatkan ke hadirat Allah SWT atas rahmat-Nya sehingga Program Studi Ilmu Komputer terus bertumbuh sebagai rumah bagi generasi yang ingin memecahkan masalah nyata lewat teknologi.'."\n".'Kami membekali mahasiswa tidak hanya dengan keterampilan rekayasa perangkat lunak, data, dan kecerdasan buatan, tetapi juga integritas dan kemampuan berpikir kritis. Selamat bergabung, mari kita ciptakan masa depan bersama.',
                'welcome_image' => 'uploads/home/hero-3-cYdqNI.webp',
            ],
        );
    }
}
