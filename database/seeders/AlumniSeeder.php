<?php

namespace Database\Seeders;

use App\Models\Alumni;
use Illuminate\Database\Seeder;

class AlumniSeeder extends Seeder
{
    public function run(): void
    {
        $alumni = [
            [
                'name' => 'Raka Maulana', 'batch_year' => 2018, 'graduation_year' => 2022,
                'job_position' => 'Web Developer', 'company' => 'Studio Teknologi Pasuruan',
                'testimonial' => 'Penguatan web mobile membantu saya cepat beradaptasi dengan kebutuhan pengembangan aplikasi.',
            ],
            [
                'name' => 'Nadia Putri', 'batch_year' => 2019, 'graduation_year' => 2023,
                'job_position' => 'Intelligent System Specialist', 'company' => 'Digital Insight Indonesia',
                'testimonial' => 'Pengalaman riset dan proyek sistem cerdas membantu saya memahami penerapan teknologi berbasis komputasi.',
            ],
            [
                'name' => 'Fajar Ramadhan', 'batch_year' => 2017, 'graduation_year' => 2021,
                'job_position' => 'Network Specialist', 'company' => 'Solusi Jaringan Mandiri',
                'testimonial' => 'Penguatan jaringan komputer membantu saya menangani infrastruktur, monitoring, dan layanan konektivitas.',
            ],
            [
                'name' => 'Aulia Safira', 'batch_year' => 2020, 'graduation_year' => 2024,
                'job_position' => 'Big Data Specialist', 'company' => 'Data Lab Nusantara',
                'testimonial' => 'Pengolahan data dan proyek analitik menjadi bekal untuk membaca pola kebutuhan layanan digital.',
            ],
            [
                'name' => 'Bagus Prasetyo', 'batch_year' => 2016, 'graduation_year' => 2020,
                'job_position' => 'Game Specialist', 'company' => 'Studio Game Edukasi',
                'testimonial' => 'Proyek multimedia dan game membuka ruang untuk membuat produk interaktif berbasis pembelajaran.',
            ],
            [
                'name' => 'Dimas Wirawan', 'batch_year' => 2021, 'graduation_year' => 2025,
                'job_position' => 'Mobile Application Specialist', 'company' => 'Mobile Lab Pasuruan',
                'testimonial' => 'Pengalaman membangun aplikasi membantu saya merancang layanan mobile yang ringan dan mudah digunakan.',
            ],
        ];

        foreach ($alumni as $data) {
            Alumni::query()->create([...$data, 'status' => Alumni::STATUS_ACTIVE]);
        }
    }
}
