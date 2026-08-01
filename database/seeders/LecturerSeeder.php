<?php

namespace Database\Seeders;

use App\Models\Lecturer;
use Illuminate\Database\Seeder;

class LecturerSeeder extends Seeder
{
    public function run(): void
    {
        $lecturers = [
            [
                'sort_order' => 1, 'name' => 'Dr. Rina Kartika, M.Kom.', 'nidn' => '0712048501',
                'position' => 'Ketua Program Studi', 'expertise' => 'Sistem Cerdas, Data Mining',
                'education' => 'S3 Ilmu Komputer', 'email' => 'rina.kartika@uniwara.ac.id',
                'bio' => 'Berfokus pada pengembangan pembelajaran sistem cerdas, data mining, dan penguatan riset terapan untuk kebutuhan akademik prodi.',
            ],
            [
                'sort_order' => 2, 'name' => 'Arif Wibisono, S.Kom., M.Kom.', 'nidn' => '0709088802',
                'position' => 'Dosen Tetap', 'expertise' => 'Rekayasa Perangkat Lunak, Web Engineering',
                'education' => 'S2 Teknik Informatika', 'email' => 'arif.wibisono@uniwara.ac.id',
                'bio' => 'Membimbing pengembangan aplikasi web, perancangan perangkat lunak, dan praktik proyek berbasis kebutuhan industri.',
            ],
            [
                'sort_order' => 3, 'name' => 'Siti Nur Azizah, S.Kom., M.Cs.', 'nidn' => '0715029003',
                'position' => 'Dosen Tetap', 'expertise' => 'Basis Data, Analitik Data',
                'education' => 'S2 Ilmu Komputer', 'email' => 'siti.azizah@uniwara.ac.id',
                'bio' => 'Mengembangkan materi basis data, pemodelan data, dan analitik untuk mendukung kompetensi pengolahan data mahasiswa.',
            ],
            [
                'sort_order' => 4, 'name' => 'Dimas Pradana, S.T., M.T.', 'nidn' => '0723118704',
                'position' => 'Dosen Tetap', 'expertise' => 'Jaringan Komputer, Keamanan Sistem',
                'education' => 'S2 Teknik Elektro', 'email' => 'dimas.pradana@uniwara.ac.id',
                'bio' => 'Mendampingi praktikum jaringan, administrasi sistem, dan penerapan keamanan dasar dalam lingkungan infrastruktur komputer.',
            ],
            [
                'sort_order' => 5, 'name' => 'Nandya Putri, S.Kom., M.Kom.', 'nidn' => '0704059105',
                'position' => 'Dosen Tetap', 'expertise' => 'Interaksi Manusia Komputer, UI/UX',
                'education' => 'S2 Sistem Informasi', 'email' => 'nandya.putri@uniwara.ac.id',
                'bio' => 'Berfokus pada pengalaman pengguna, perancangan antarmuka, dan evaluasi produk digital berbasis kebutuhan pengguna.',
            ],
            [
                'sort_order' => 6, 'name' => 'Fajar Hidayat, S.Kom., M.Kom.', 'nidn' => '0710108906',
                'position' => 'Dosen Tetap', 'expertise' => 'Multimedia, Game Technology',
                'education' => 'S2 Informatika', 'email' => 'fajar.hidayat@uniwara.ac.id',
                'bio' => 'Mengampu materi multimedia interaktif, pemrograman game, dan pengembangan konten digital untuk portofolio mahasiswa.',
            ],
        ];

        foreach ($lecturers as $lecturer) {
            Lecturer::query()->firstOrCreate(
                ['nidn' => $lecturer['nidn']],
                [...$lecturer, 'status' => Lecturer::STATUS_ACTIVE],
            );
        }
    }
}
