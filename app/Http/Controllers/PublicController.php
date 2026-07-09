<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class PublicController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'activities' => array_slice($this->activitiesData(), 0, 3),
            'alumni' => array_slice($this->alumniData(), 0, 4),
        ]);
    }

    public function profile(): View
    {
        return view('public.profile');
    }

    public function visionMission(): RedirectResponse
    {
        return redirect('/profil#visi-misi-page');
    }

    public function lecturers(): View
    {
        return view('public.lecturers', [
            'lecturers' => $this->lecturersData(),
        ]);
    }

    public function activities(): View
    {
        return view('public.activities.index', [
            'activities' => $this->activitiesData(),
        ]);
    }

    public function activityDetail(string $slug): View
    {
        return view('public.activities.show', [
            'activity' => $this->findActivity($slug),
        ]);
    }

    public function journalRedirect(): RedirectResponse
    {
        return redirect()->away(config('app.journal_url', 'https://ejurnal.uniwara.ac.id'));
    }

    public function documents(): View
    {
        return view('public.documents', [
            'documents' => $this->documentsData(),
        ]);
    }

    public function alumni(): View
    {
        return view('public.alumni', [
            'alumni' => $this->alumniData(),
        ]);
    }

    public function contact(): RedirectResponse
    {
        return redirect('/#kontak-section');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function activitiesData(): array
    {
        return [
            [
                'title' => 'Seminar AI dan Data Sains',
                'slug' => 'seminar-ai-dan-data-sains',
                'excerpt' => 'Seminar akademik tentang pemanfaatan kecerdasan buatan dan data sains untuk riset, industri, dan pengembangan produk digital.',
                'date' => '2026-03-15',
                'date_label' => '15 Maret 2026',
                'location' => 'Aula Kampus',
                'category' => 'Seminar',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'image_class' => 'placeholder-visit',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Program Studi Ilmu Komputer menyelenggarakan seminar tentang pemanfaatan kecerdasan buatan dan data sains sebagai penguatan wawasan akademik mahasiswa. Kegiatan ini menghadirkan pemateri dari kalangan praktisi dan akademisi untuk membahas perkembangan teknologi terkini.'],
                    ['type' => 'heading', 'text' => 'Fokus Pembahasan'],
                    ['type' => 'list', 'items' => [
                        'Pengenalan penerapan AI dan data sains pada riset, industri, dan layanan publik.',
                        'Studi kasus pengolahan data untuk menghasilkan insight dan rekomendasi keputusan.',
                        'Etika penggunaan teknologi, keamanan data, dan kesiapan kompetensi mahasiswa.',
                    ]],
                    ['type' => 'heading', 'text' => 'Agenda Kegiatan'],
                    ['type' => 'list', 'style' => 'ol', 'items' => [
                        'Pembukaan dan pengantar dari Program Studi Ilmu Komputer.',
                        'Pemaparan materi utama serta sesi diskusi bersama narasumber.',
                        'Penutup, dokumentasi, dan tindak lanjut portofolio pembelajaran.',
                    ]],
                    ['type' => 'quote', 'text' => 'Kegiatan ini menjadi ruang belajar mahasiswa untuk memahami kebutuhan industri sekaligus memperkuat portofolio akademik berbasis teknologi.'],
                ],
            ],
            [
                'title' => 'Workshop UI/UX dan Prototyping',
                'slug' => 'workshop-ui-ux-prototyping',
                'excerpt' => 'Workshop praktis untuk merancang alur pengguna, wireframe, dan prototipe produk digital berbasis kebutuhan pengguna.',
                'date' => '2026-03-22',
                'date_label' => '22 Maret 2026',
                'location' => 'Laboratorium Komputer',
                'category' => 'Workshop',
                'image' => 'assets/images/hero/hero-2.jpeg',
                'image_class' => 'placeholder-open',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Workshop ini membahas proses desain produk digital dari riset kebutuhan pengguna hingga penyusunan prototipe interaktif. Mahasiswa diarahkan memahami hubungan antara masalah pengguna, struktur informasi, dan rancangan antarmuka.'],
                    ['type' => 'heading', 'text' => 'Materi Utama'],
                    ['type' => 'list', 'items' => [
                        'Riset pengguna, user flow, wireframe, dan penyusunan skenario penggunaan.',
                        'Pembuatan prototipe antarmuka untuk kebutuhan presentasi dan validasi awal.',
                        'Evaluasi desain berdasarkan kejelasan informasi, konsistensi, dan aksesibilitas.',
                    ]],
                    ['type' => 'quote', 'text' => 'Output kegiatan diarahkan menjadi portofolio desain yang dapat dikembangkan lagi pada mata kuliah proyek.'],
                ],
            ],
            [
                'title' => 'Kuliah Tamu Cybersecurity Industri',
                'slug' => 'kuliah-tamu-cybersecurity-industri',
                'excerpt' => 'Kuliah tamu bersama praktisi untuk membahas keamanan aplikasi, manajemen risiko, dan praktik perlindungan data di industri.',
                'date' => '2026-04-05',
                'date_label' => '05 April 2026',
                'location' => 'Ruang Seminar Prodi',
                'category' => 'Kuliah Tamu',
                'image' => 'assets/images/hero/hero-3.jpeg',
                'image_class' => 'placeholder-virtual',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Kuliah tamu ini memperkenalkan tantangan keamanan siber di lingkungan industri, mulai dari keamanan aplikasi, kesadaran pengguna, sampai tata kelola data. Mahasiswa mendapatkan gambaran tentang kompetensi yang dibutuhkan untuk masuk ke bidang cybersecurity.'],
                    ['type' => 'heading', 'text' => 'Topik Diskusi'],
                    ['type' => 'list', 'items' => [
                        'Prinsip dasar keamanan aplikasi dan perlindungan data.',
                        'Manajemen risiko teknologi informasi pada organisasi.',
                        'Studi kasus insiden keamanan dan langkah mitigasi.',
                    ]],
                ],
            ],
            [
                'title' => 'Pelatihan Sertifikasi Junior Web Developer',
                'slug' => 'pelatihan-sertifikasi-junior-web-developer',
                'excerpt' => 'Pelatihan intensif untuk menyiapkan mahasiswa mengikuti sertifikasi kompetensi pengembangan web tingkat junior.',
                'date' => '2026-04-19',
                'date_label' => '19 April 2026',
                'location' => 'Laboratorium Komputer',
                'category' => 'Kompetensi',
                'image' => 'assets/images/hero/hero-4.jpeg',
                'image_class' => 'placeholder-classroom',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Pelatihan ini menyiapkan mahasiswa memahami alur kerja pengembangan web, struktur halaman, pengelolaan data, dan praktik deployment sederhana. Materi disusun agar selaras dengan kebutuhan sertifikasi kompetensi.'],
                    ['type' => 'heading', 'text' => 'Target Kompetensi'],
                    ['type' => 'list', 'style' => 'ol', 'items' => [
                        'Membuat struktur halaman web yang semantik dan responsif.',
                        'Mengelola interaksi dasar serta validasi input pengguna.',
                        'Menyusun dokumentasi proyek sebagai bukti kompetensi.',
                    ]],
                ],
            ],
            [
                'title' => 'Pembekalan PKL dan Etika Kerja Industri',
                'slug' => 'pembekalan-pkl-dan-etika-kerja-industri',
                'excerpt' => 'Pembekalan mahasiswa sebelum praktik kerja lapangan, mencakup administrasi, etika kerja, komunikasi, dan pelaporan kegiatan.',
                'date' => '2026-04-22',
                'date_label' => '22 April 2026',
                'location' => 'Ruang Prodi',
                'category' => 'PKL',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'image_class' => 'placeholder-community',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Pembekalan PKL membantu mahasiswa memahami kesiapan administrasi, perilaku profesional, dan tanggung jawab selama berada di tempat praktik. Kegiatan ini juga menjelaskan format laporan dan mekanisme bimbingan.'],
                    ['type' => 'heading', 'text' => 'Ruang Lingkup'],
                    ['type' => 'list', 'items' => [
                        'Alur administrasi, surat pengantar, dan koordinasi dengan mitra.',
                        'Etika komunikasi, disiplin kerja, dan pelaporan aktivitas harian.',
                        'Format laporan akhir serta presentasi hasil PKL.',
                    ]],
                ],
            ],
            [
                'title' => 'Kunjungan Industri ke Startup Teknologi',
                'slug' => 'kunjungan-industri-ke-startup-teknologi',
                'excerpt' => 'Kunjungan mahasiswa ke mitra industri untuk mengenal proses kerja, pengembangan produk, dan budaya tim teknologi.',
                'date' => '2026-08-05',
                'date_label' => '05 Agustus 2026',
                'location' => 'Mitra Industri Pasuruan',
                'category' => 'Industri',
                'image' => 'assets/images/hero/hero-2.jpeg',
                'image_class' => 'placeholder-career',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Kunjungan industri memberi mahasiswa pengalaman langsung melihat proses kerja tim teknologi, mulai dari perencanaan produk, pengembangan fitur, pengujian, hingga rilis. Kegiatan ini memperkuat pemahaman mahasiswa terhadap dunia kerja.'],
                    ['type' => 'heading', 'text' => 'Aktivitas Kunjungan'],
                    ['type' => 'list', 'items' => [
                        'Company profile dan pengenalan unit kerja teknologi.',
                        'Diskusi alur pengembangan produk digital.',
                        'Sesi tanya jawab tentang magang, karier, dan kebutuhan kompetensi.',
                    ]],
                ],
            ],
            [
                'title' => 'Pengabdian Literasi Digital Sekolah',
                'slug' => 'pengabdian-literasi-digital-sekolah',
                'excerpt' => 'Kegiatan pengabdian dosen dan mahasiswa untuk mengenalkan keamanan digital, etika internet, dan pemanfaatan teknologi pembelajaran.',
                'date' => '2026-08-16',
                'date_label' => '16 Agustus 2026',
                'location' => 'SMA Mitra Pasuruan',
                'category' => 'Pengabdian',
                'image' => 'assets/images/hero/hero-3.jpeg',
                'image_class' => 'placeholder-media',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Kegiatan pengabdian ini mempertemukan dosen, mahasiswa, dan siswa sekolah mitra dalam pelatihan literasi digital. Materi disampaikan dengan pendekatan praktis agar mudah diterapkan dalam aktivitas belajar sehari-hari.'],
                    ['type' => 'heading', 'text' => 'Materi Pengabdian'],
                    ['type' => 'list', 'items' => [
                        'Keamanan akun, kata sandi, dan kebiasaan digital yang aman.',
                        'Etika penggunaan media sosial dan sumber informasi daring.',
                        'Pemanfaatan aplikasi pembelajaran untuk tugas dan kolaborasi.',
                    ]],
                ],
            ],
            [
                'title' => 'Bootcamp Data Analytics',
                'slug' => 'bootcamp-data-analytics',
                'excerpt' => 'Bootcamp pengolahan data, visualisasi, dan penyusunan insight berbasis dataset studi kasus untuk portofolio mahasiswa.',
                'date' => '2026-09-03',
                'date_label' => '03 September 2026',
                'location' => 'Studio Proyek',
                'category' => 'Bootcamp',
                'image' => 'assets/images/hero/hero-4.jpeg',
                'image_class' => 'placeholder-business',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Bootcamp Data Analytics dirancang untuk melatih mahasiswa membaca dataset, membersihkan data, membuat visualisasi, dan menyusun insight yang dapat dipresentasikan secara ringkas.'],
                    ['type' => 'heading', 'text' => 'Luaran Kegiatan'],
                    ['type' => 'list', 'style' => 'ol', 'items' => [
                        'Dataset studi kasus yang telah dibersihkan dan dianalisis.',
                        'Visualisasi data yang menjawab pertanyaan bisnis atau sosial.',
                        'Ringkasan insight sebagai bahan portofolio mahasiswa.',
                    ]],
                ],
            ],
            [
                'title' => 'Expo Karya Mahasiswa Ilmu Komputer',
                'slug' => 'expo-karya-mahasiswa-ilmu-komputer',
                'excerpt' => 'Pameran karya proyek mahasiswa berupa aplikasi, sistem informasi, prototipe produk digital, dan hasil kolaborasi dengan mitra.',
                'date' => '2026-10-18',
                'date_label' => '18 Oktober 2026',
                'location' => 'Aula Kampus',
                'category' => 'Expo',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'image_class' => 'placeholder-sport',
                'content_blocks' => [
                    ['type' => 'paragraph', 'text' => 'Expo karya mahasiswa menjadi ruang publikasi hasil pembelajaran berbasis proyek. Mahasiswa menampilkan aplikasi, sistem informasi, prototipe produk digital, serta karya kolaboratif yang dikembangkan selama perkuliahan.'],
                    ['type' => 'heading', 'text' => 'Format Kegiatan'],
                    ['type' => 'list', 'items' => [
                        'Pameran proyek mahasiswa dan demonstrasi produk.',
                        'Presentasi singkat konsep, teknologi, dan manfaat karya.',
                        'Umpan balik dari dosen, alumni, mitra, dan pengunjung.',
                    ]],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function findActivity(string $slug): array
    {
        foreach ($this->activitiesData() as $activity) {
            if ($activity['slug'] === $slug) {
                return $activity;
            }
        }

        abort(404);
    }

    /**
     * @return array<int, array<string, int|string>>
     */
    private function lecturersData(): array
    {
        return [
            [
                'sort_order' => 1,
                'name' => 'Dr. Rina Kartika, M.Kom.',
                'nidn' => '0712048501',
                'position' => 'Ketua Program Studi',
                'field' => 'Sistem Cerdas, Data Mining',
                'expertise' => 'Sistem Cerdas, Data Mining',
                'expertise_short' => 'Sistem Cerdas, Data Mining',
                'education' => 'S3 Ilmu Komputer',
                'email' => 'rina.kartika@uniwara.ac.id',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'description' => 'Berfokus pada pengembangan pembelajaran sistem cerdas, data mining, dan penguatan riset terapan untuk kebutuhan akademik prodi.',
                'icon' => 'fa-solid fa-microchip',
            ],
            [
                'sort_order' => 2,
                'name' => 'Arif Wibisono, S.Kom., M.Kom.',
                'nidn' => '0709088802',
                'position' => 'Dosen Tetap',
                'field' => 'Rekayasa Perangkat Lunak',
                'expertise' => 'Rekayasa Perangkat Lunak, Web Engineering',
                'expertise_short' => 'Rekayasa Perangkat Lunak',
                'education' => 'S2 Teknik Informatika',
                'email' => 'arif.wibisono@uniwara.ac.id',
                'image' => 'assets/images/hero/hero-2.jpeg',
                'description' => 'Membimbing pengembangan aplikasi web, perancangan perangkat lunak, dan praktik proyek berbasis kebutuhan industri.',
                'icon' => 'fa-solid fa-code',
            ],
            [
                'sort_order' => 3,
                'name' => 'Siti Nur Azizah, S.Kom., M.Cs.',
                'nidn' => '0715029003',
                'position' => 'Dosen Tetap',
                'field' => 'Basis Data, Analitik Data',
                'expertise' => 'Basis Data, Analitik Data',
                'expertise_short' => 'Basis Data, Analitik Data',
                'education' => 'S2 Ilmu Komputer',
                'email' => 'siti.azizah@uniwara.ac.id',
                'image' => 'assets/images/hero/hero-3.jpeg',
                'description' => 'Mengembangkan materi basis data, pemodelan data, dan analitik untuk mendukung kompetensi pengolahan data mahasiswa.',
                'icon' => 'fa-solid fa-database',
            ],
            [
                'sort_order' => 4,
                'name' => 'Dimas Pradana, S.T., M.T.',
                'nidn' => '0723118704',
                'position' => 'Dosen Tetap',
                'field' => 'Jaringan Komputer',
                'expertise' => 'Jaringan Komputer, Keamanan Sistem',
                'expertise_short' => 'Jaringan Komputer',
                'education' => 'S2 Teknik Elektro',
                'email' => 'dimas.pradana@uniwara.ac.id',
                'image' => 'assets/images/hero/hero-4.jpeg',
                'description' => 'Mendampingi praktikum jaringan, administrasi sistem, dan penerapan keamanan dasar dalam lingkungan infrastruktur komputer.',
                'icon' => 'fa-solid fa-network-wired',
            ],
            [
                'sort_order' => 5,
                'name' => 'Nandya Putri, S.Kom., M.Kom.',
                'nidn' => '0704059105',
                'position' => 'Dosen Tetap',
                'field' => 'UI/UX, Aplikasi Mobile',
                'expertise' => 'Interaksi Manusia Komputer, UI/UX',
                'expertise_short' => 'UI/UX, Aplikasi Mobile',
                'education' => 'S2 Sistem Informasi',
                'email' => 'nandya.putri@uniwara.ac.id',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'description' => 'Berfokus pada pengalaman pengguna, perancangan antarmuka, dan evaluasi produk digital berbasis kebutuhan pengguna.',
                'icon' => 'fa-solid fa-mobile-screen',
            ],
            [
                'sort_order' => 6,
                'name' => 'Fajar Hidayat, S.Kom., M.Kom.',
                'nidn' => '0710108906',
                'position' => 'Dosen Tetap',
                'field' => 'Multimedia, Game Technology',
                'expertise' => 'Multimedia, Game Technology',
                'expertise_short' => 'Multimedia, Game Technology',
                'education' => 'S2 Informatika',
                'email' => 'fajar.hidayat@uniwara.ac.id',
                'image' => 'assets/images/hero/hero-2.jpeg',
                'description' => 'Mengampu materi multimedia interaktif, pemrograman game, dan pengembangan konten digital untuk portofolio mahasiswa.',
                'icon' => 'fa-solid fa-gamepad',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function alumniData(): array
    {
        return [
            [
                'name' => 'Raka Maulana',
                'role' => 'Web Developer di Studio Teknologi Pasuruan',
                'year' => '2018',
                'batch_year' => '2018',
                'graduation_year' => '2022',
                'job_position' => 'Web Developer',
                'company' => 'Studio Teknologi Pasuruan',
                'quote' => 'Penguatan web mobile membantu saya cepat beradaptasi dengan kebutuhan pengembangan aplikasi.',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'icon' => 'fa-solid fa-briefcase',
            ],
            [
                'name' => 'Nadia Putri',
                'role' => 'Intelligent System Specialist di Digital Insight Indonesia',
                'year' => '2019',
                'batch_year' => '2019',
                'graduation_year' => '2023',
                'job_position' => 'Intelligent System Specialist',
                'company' => 'Digital Insight Indonesia',
                'quote' => 'Pengalaman riset dan proyek sistem cerdas membantu saya memahami penerapan teknologi berbasis komputasi.',
                'image' => 'assets/images/hero/hero-2.jpeg',
                'icon' => 'fa-solid fa-brain',
            ],
            [
                'name' => 'Fajar Ramadhan',
                'role' => 'Network Specialist di Solusi Jaringan Mandiri',
                'year' => '2017',
                'batch_year' => '2017',
                'graduation_year' => '2021',
                'job_position' => 'Network Specialist',
                'company' => 'Solusi Jaringan Mandiri',
                'quote' => 'Penguatan jaringan komputer membantu saya menangani infrastruktur, monitoring, dan layanan konektivitas.',
                'image' => 'assets/images/hero/hero-3.jpeg',
                'icon' => 'fa-solid fa-network-wired',
            ],
            [
                'name' => 'Aulia Safira',
                'role' => 'Big Data Specialist di Data Lab Nusantara',
                'year' => '2020',
                'batch_year' => '2020',
                'graduation_year' => '2024',
                'job_position' => 'Big Data Specialist',
                'company' => 'Data Lab Nusantara',
                'quote' => 'Pengolahan data dan proyek analitik menjadi bekal untuk membaca pola kebutuhan layanan digital.',
                'image' => 'assets/images/hero/hero-4.jpeg',
                'icon' => 'fa-solid fa-chart-column',
            ],
            [
                'name' => 'Bagus Prasetyo',
                'role' => 'Game Specialist di Studio Game Edukasi',
                'year' => '2016',
                'batch_year' => '2016',
                'graduation_year' => '2020',
                'job_position' => 'Game Specialist',
                'company' => 'Studio Game Edukasi',
                'quote' => 'Proyek multimedia dan game membuka ruang untuk membuat produk interaktif berbasis pembelajaran.',
                'image' => 'assets/images/hero/hero-1.jpeg',
                'icon' => 'fa-solid fa-gamepad',
            ],
            [
                'name' => 'Dimas Wirawan',
                'role' => 'Mobile Application Specialist di Mobile Lab Pasuruan',
                'year' => '2021',
                'batch_year' => '2021',
                'graduation_year' => '2025',
                'job_position' => 'Mobile Application Specialist',
                'company' => 'Mobile Lab Pasuruan',
                'quote' => 'Pengalaman membangun aplikasi membantu saya merancang layanan mobile yang ringan dan mudah digunakan.',
                'image' => 'assets/images/hero/hero-2.jpeg',
                'icon' => 'fa-solid fa-mobile-screen',
            ],
        ];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function documentsData(): array
    {
        return [
            [
                'title' => 'Kurikulum Program Studi',
                'category' => 'Kurikulum',
                'description' => 'Dokumen struktur kurikulum, capaian pembelajaran, dan distribusi mata kuliah Program Studi Ilmu Komputer.',
                'type' => 'PDF',
                'date' => '2026',
                'file_type' => 'PDF',
                'file_icon' => 'fa-file',
                'file_size' => '2.4 MB',
                'updated_at' => '2026-01-12',
                'updated_label' => '12 Januari 2026',
                'file' => 'assets/documents/kurikulum-program-studi.pdf',
                'icon' => 'fa-file-pdf',
            ],
            [
                'title' => 'Panduan Akademik Mahasiswa',
                'category' => 'Akademik',
                'description' => 'Panduan layanan akademik, perwalian, pengisian KRS, perkuliahan, dan prosedur administrasi mahasiswa.',
                'type' => 'PDF',
                'date' => '2026',
                'file_type' => 'PDF',
                'file_icon' => 'fa-file',
                'file_size' => '1.8 MB',
                'updated_at' => '2026-02-03',
                'updated_label' => '03 Februari 2026',
                'file' => 'assets/documents/panduan-akademik-mahasiswa.pdf',
                'icon' => 'fa-file-pdf',
            ],
            [
                'title' => 'Dokumen Akreditasi Prodi',
                'category' => 'Akreditasi',
                'description' => 'Arsip pendukung akreditasi program studi, evaluasi mutu, dan dokumen kelengkapan institusional.',
                'type' => 'PDF',
                'date' => '2025',
                'file_type' => 'PDF',
                'file_icon' => 'fa-file',
                'file_size' => '4.1 MB',
                'updated_at' => '2025-11-18',
                'updated_label' => '18 November 2025',
                'file' => 'assets/documents/dokumen-akreditasi-prodi.pdf',
                'icon' => 'fa-file-pdf',
            ],
            [
                'title' => 'SOP Praktikum Laboratorium',
                'category' => 'SOP',
                'description' => 'Ketentuan penggunaan laboratorium, tata tertib praktikum, peminjaman perangkat, dan pelaporan kendala.',
                'type' => 'PDF',
                'date' => '2026',
                'file_type' => 'PDF',
                'file_icon' => 'fa-file',
                'file_size' => '950 KB',
                'updated_at' => '2026-01-29',
                'updated_label' => '29 Januari 2026',
                'file' => 'assets/documents/sop-praktikum-laboratorium.pdf',
                'icon' => 'fa-file-lines',
            ],
            [
                'title' => 'Template Proposal Kegiatan',
                'category' => 'Kegiatan',
                'description' => 'Format proposal kegiatan mahasiswa, seminar, workshop, pelatihan, dan agenda komunitas prodi.',
                'type' => 'DOCX',
                'date' => '2026',
                'file_type' => 'DOCX',
                'file_icon' => 'fa-file-word',
                'file_size' => '420 KB',
                'updated_at' => '2026-02-14',
                'updated_label' => '14 Februari 2026',
                'file' => 'assets/documents/template-proposal-kegiatan.docx',
                'icon' => 'fa-file-lines',
            ],
            [
                'title' => 'Form Tracer Study Alumni',
                'category' => 'Alumni',
                'description' => 'Formulir pendataan alumni untuk kebutuhan evaluasi lulusan, jejaring kerja, dan pengembangan kurikulum.',
                'type' => 'PDF',
                'date' => '2026',
                'file_type' => 'PDF',
                'file_icon' => 'fa-file',
                'file_size' => '760 KB',
                'updated_at' => '2026-03-02',
                'updated_label' => '02 Maret 2026',
                'file' => 'assets/documents/form-tracer-study-alumni.pdf',
                'icon' => 'fa-file-lines',
            ],
        ];
    }
}
