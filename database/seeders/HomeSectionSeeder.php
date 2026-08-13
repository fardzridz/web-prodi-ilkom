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
                        ['order' => 1, 'title' => 'Arah Karier Luas', 'description' => 'Lulusan disiapkan untuk berkiprah sebagai software developer, network specialist, big data specialist, game specialist, mobile developer, researcher, hingga startup entrepreneur.', 'image' => null],
                        ['order' => 2, 'title' => 'Peminatan yang Jelas', 'description' => 'Mulai semester 5 mahasiswa dapat memperdalam bidang KBJ, KCV, RPL, atau MGM sesuai minat dan rencana profesi.', 'image' => null],
                        ['order' => 3, 'title' => 'Pembelajaran Berbasis Proyek', 'description' => 'Mahasiswa diarahkan membangun portofolio nyata melalui praktikum, proyek aplikasi, dan kegiatan kolaboratif.', 'image' => null],
                        ['order' => 4, 'title' => 'Koneksi Akademik dan Industri', 'description' => 'Kegiatan prodi menghubungkan perkuliahan dengan sertifikasi, magang, PKL, dan pengabdian masyarakat.', 'image' => null],
                    ],
                ],
                'cta_text' => 'Lihat Profil',
                'cta_link' => '/profil',
                'welcome_title' => 'Tentang Ilmu Komputer',
                'welcome_description' => 'Program Studi Ilmu Komputer Universitas PGRI Wiranegara menyiapkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, dan siap berkontribusi.',
            ],
        );
    }
}
