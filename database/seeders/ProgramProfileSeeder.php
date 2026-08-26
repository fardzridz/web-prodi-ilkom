<?php

namespace Database\Seeders;

use App\Models\ProgramProfile;
use Illuminate\Database\Seeder;

class ProgramProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProgramProfile::query()->firstOrCreate(
            ['id' => 1],
            [
                'history' => <<<'HTML'
                    <h1>Perjalanan Program Studi</h1><p>Program Studi Ilmu Komputer berkembang sebagai ruang akademik yang menjawab kebutuhan tenaga teknologi informasi di Pasuruan dan sekitarnya. Kurikulum, aktivitas mahasiswa, dan kerja sama terus disesuaikan dengan kebutuhan dunia digital.</p><blockquote><strong>Perjalanan prodi dibangun melalui pendidikan, riset, pengabdian, dan kolaborasi yang dekat dengan kebutuhan masyarakat.</strong></blockquote><p>Penguatan laboratorium, kegiatan praktikum, dan pembelajaran berbasis proyek menjadi bagian dari perkembangan prodi untuk menyiapkan mahasiswa menghadapi perubahan teknologi.</p>
                    HTML,
                'history_image' => 'uploads/profile/hero-2-x5P5IT.webp',
                'description' => <<<'HTML'
                    <h1>Pelajari, Ciptakan, Wujudkan</h1><p>Program Studi Ilmu Komputer menyiapkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, dapat dipercaya, mampu bekerja sama, dan siap berkontribusi secara nasional maupun internasional.</p><p>Pembelajaran diarahkan pada pengembangan ilmu komputer melalui pendidikan, penelitian, pengabdian, kerja sama, serta penerapan teknologi informasi yang bermanfaat bagi masyarakat.</p>
                    HTML,
                'description_image' => 'uploads/profile/hero-2-qtyOjV.webp',
                'vision' => <<<'HTML'
                    <h2>Visi</h2><p><strong><em>&ldquo;Menjadi Program Studi Ilmu Komputer yang unggul dalam pengembangan teknologi informasi, berjiwa entrepreneur, dan berkontribusi bagi masyarakat.&rdquo;</em></strong></p><p>Visi ini menjadi arah pengembangan akademik, riset, pengabdian, dan kerja sama prodi dalam membentuk lulusan yang adaptif terhadap perkembangan teknologi.</p>
                    HTML,
                'mission' => <<<'HTML'
                    <h2>Misi</h2><ul><li><strong>Lulusan Kompeten &amp; Berjiwa Entrepreneur - </strong>Menghasilkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa entrepreneur, dapat dipercaya, dan mampu bekerja sama.</li><li><strong>Pengembangan Ilmu &amp; Tridarma - </strong>Mengembangkan ilmu pengetahuan teknologi informasi dan komputer melalui tridarma perguruan tinggi.</li><li><strong>Pelayanan untuk Civitas &amp; Masyarakat - </strong>Memberikan pelayanan kepada civitas akademika dan masyarakat melalui pendidikan, penelitian, dan pengabdian.</li><li><strong>Kerja Sama &amp; Inovasi - </strong>Menjalin kerja sama serta menghasilkan produk inovasi dan kreasi di bidang teknologi informasi dan komputer.</li></ul>
                    HTML,
                'goals' => <<<'HTML'
                    <h1>Tujuan Pembelajaran</h1><ol><li>Menghasilkan lulusan berjiwa Pancasila, berintegritas, dan memiliki jiwa entrepreneurship.</li><li>Membekali mahasiswa dengan pengetahuan dan keterampilan teknologi sesuai bidang keahliannya.</li><li>Mendorong penelitian, pengabdian kepada masyarakat, dan kerja sama yang memberi manfaat nyata.</li><li>Tujuan prodi diterjemahkan dalam pembelajaran terapan, kegiatan akademik, dan penguatan portofolio mahasiswa.</li></ol>
                    HTML,
                'goals_image' => 'uploads/profile/hero-2-OsVR4O.webp',
                'accreditation' => 'Baik Sekali',
                'advantages' => <<<'HTML'
                    <h1>Keunggulan Program Studi</h1><h2>Kompetensi Sesuai Kebutuhan Industri</h2><p>Pembelajaran diarahkan pada penguatan kompetensi teknologi informasi dan komputer yang relevan dengan kebutuhan industri.</p><h2>Portofolio &amp; Kemampuan Memecahkan Masalah</h2><p>Mahasiswa didorong memiliki portofolio, pengalaman kerja sama, dan kemampuan menyelesaikan masalah berbasis teknologi.</p>
                    HTML,
                'advantages_image' => 'uploads/profile/hero-2-sbaONU.webp',
            ],
        );
    }
}
