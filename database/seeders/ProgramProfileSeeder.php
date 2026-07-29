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
                'history' => 'Sejarah prodi ditulis sebagai narasi akademik yang menjelaskan perkembangan Ilmu Komputer di lingkungan Universitas PGRI Wiranegara.',
                'description' => 'Program Studi Ilmu Komputer Universitas PGRI Wiranegara menyiapkan lulusan yang memiliki kompetensi teknologi informasi dan komputer, berjiwa wirausaha, mampu bekerja sama, serta siap berkontribusi di tingkat nasional maupun internasional.',
                'vision' => 'Mengembangkan ilmu pengetahuan teknologi informasi dan komputer melalui pendidikan, penelitian, pengabdian, dan kerja sama, dengan tetap menjunjung moral, etika, dan kebermanfaatan bagi masyarakat.',
                'mission' => implode(PHP_EOL, [
                    '1. Menyelenggarakan pendidikan Ilmu Komputer yang adaptif dan relevan dengan kebutuhan masyarakat.',
                    '2. Mengembangkan penelitian terapan di bidang teknologi informasi dan komputer.',
                    '3. Melaksanakan pengabdian masyarakat berbasis solusi teknologi.',
                ]),
                'goals' => implode(PHP_EOL, [
                    '1. Menghasilkan lulusan yang kompeten dalam pengembangan perangkat lunak, jaringan komputer, data, multimedia, dan solusi teknologi.',
                    '2. Mendorong lulusan agar mampu bekerja sama, berwirausaha, dan berkontribusi bagi masyarakat.',
                ]),
                'accreditation' => 'Baik Sekali',
                'advantages' => implode(PHP_EOL, [
                    'Keunggulan prodi mencakup pembelajaran berbasis proyek, pilihan rumpun peminatan Komputer Berbasis Jaringan, Komputasi Cerdas dan Visualisasi, Rekayasa Perangkat Lunak, serta Multimedia dan Game.',
                    'Program unggulan mendukung portofolio kompetensi mahasiswa melalui sertifikasi, pelatihan, bimbingan, dan pengalaman lapangan.',
                ]),
            ],
        );
    }
}
