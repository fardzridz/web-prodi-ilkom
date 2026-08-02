<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Page::query()->updateOrCreate(
            ['slug' => 'kebijakan-privasi'],
            [
                'title' => 'Kebijakan Privasi',
                'content' => <<<'HTML'
                    <h2>Pendahuluan</h2>
                    <p>Program Studi Ilmu Komputer Universitas PGRI Wiranegara berkomitmen untuk melindungi data pribadi pengunjung situs web kami. Kebijakan privasi ini menjelaskan jenis informasi yang kami kumpulkan, cara informasi tersebut digunakan, serta langkah-langkah yang kami ambil untuk menjaga keamanannya.</p>
                    <p>Dengan mengakses dan menggunakan situs web ini, Anda menyetujui praktik pengelolaan data yang dijelaskan dalam kebijakan ini. Kebijakan ini dapat diperbarui sewaktu-waktu dan perubahan yang kami lakukan akan diumumkan melalui halaman ini.</p>

                    <h2>Informasi yang Kami Kumpulkan</h2>
                    <p>Kami mengumpulkan informasi dalam jumlah terbatas yang diperlukan untuk memberikan pelayanan terbaik kepada pengunjung, antara lain:</p>
                    <ul>
                        <li><strong>Data dari Form Kontak:</strong> nama, alamat email, nomor telepon, dan isi pesan yang Anda kirimkan melalui formulir kontak pada situs ini.</li>
                        <li><strong>Log Akses Anonim:</strong> alamat IP, jenis peramban (browser), perangkat, dan halaman yang dikunjungi, yang diproses secara anonim untuk keperluan statistik dan peningkatan layanan.</li>
                    </ul>
                    <p>Kami tidak mengumpulkan data pribadi yang bersifat sensitif dan tidak menggunakan pelacakan lintas situs untuk tujuan periklanan.</p>

                    <h2>Penggunaan Informasi</h2>
                    <p>Informasi yang kami kumpulkan digunakan semata-mata untuk kepentingan akademis dan pelayanan, antara lain:</p>
                    <ul>
                        <li>Menjawab pertanyaan dan menindaklanjuti pesan yang Anda sampaikan melalui formulir kontak.</li>
                        <li>Mengirimkan informasi akademik, kegiatan kemahasiswaan, dan pengumuman resmi program studi.</li>
                        <li>Menganalisis pola kunjungan secara agregat untuk meningkatkan kualitas dan kemudahan akses situs web.</li>
                    </ul>
                    <p>Kami tidak menjual, menyewakan, atau membagikan data pribadi Anda kepada pihak ketiga tanpa persetujuan, kecuali diwajibkan oleh peraturan perundang-undangan yang berlaku.</p>

                    <h2>Keamanan Data</h2>
                    <p>Kami menerapkan langkah-langkah teknis dan organisasional untuk melindungi data dari akses, perubahan, atau penyebaran yang tidak sah, antara lain:</p>
                    <ul>
                        <li>Penyimpanan data hanya pada server resmi yang dikelola dengan akses terbatas.</li>
                        <li>Penggunaan koneksi terenkripsi (HTTPS) pada seluruh halaman situs.</li>
                        <li>Pembatasan akses data hanya kepada pengelola yang berwenang.</li>
                        <li>Peninjauan berkala terhadap praktik keamanan untuk mencegah insiden.</li>
                    </ul>

                    <h2>Hak Pengguna &amp; Kontak Pengaduan</h2>
                    <p>Anda berhak mengajukan pertanyaan, meminta perbaikan, atau meminta penghapusan data pribadi yang pernah Anda kirimkan melalui situs ini. Untuk keperluan tersebut, atau jika Anda memiliki keluhan terkait perlindungan data, silakan hubungi kami melalui:</p>
                    <ul>
                        <li>Email: <a href="mailto:univ.pgriwiranegara@gmail.com">univ.pgriwiranegara@gmail.com</a></li>
                        <li>Telepon/WhatsApp: <a href="https://wa.me/6282141554377">0821-4155-4377</a></li>
                    </ul>
                    <p>Kami akan merespons setiap permintaan atau pengaduan secepat mungkin dan berupaya menyelesaikannya dengan baik.</p>
                    HTML,
            ]
        );

        Page::query()->updateOrCreate(
            ['slug' => 'aksesibilitas'],
            [
                'title' => 'Aksesibilitas',
                'content' => <<<'HTML'
                    <h2>Pernyataan Komitmen Aksesibilitas Web</h2>
                    <p>Program Studi Ilmu Komputer Universitas PGRI Wiranegara berkomitmen untuk menyediakan situs web yang dapat diakses oleh seluruh pengguna, termasuk penyandang disabilitas. Kami berupaya memenuhi standar <em>Web Content Accessibility Guidelines (WCAG) 2.1</em> level AA agar setiap orang dapat mengakses informasi akademik secara setara dan mandiri.</p>
                    <p>Kami meyakini bahwa aksesibilitas digital merupakan bagian dari kualitas layanan pendidikan. Oleh karena itu, seluruh konten dan fitur situs terus kami tinjau dan perbaiki secara berkala.</p>

                    <h2>Fitur Aksesibilitas yang Diterapkan</h2>
                    <p>Beberapa fitur yang telah kami terapkan untuk mendukung pengalaman penggunaan yang inklusif antara lain:</p>
                    <ul>
                        <li><strong>Navigasi dengan Keyboard:</strong> seluruh menu, tautan, dan formulir dapat dioperasikan menggunakan keyboard tanpa bantuan mouse.</li>
                        <li><strong>Kontras Warna:</strong> kombinasi warna teks dan latar dirancang dengan kontras yang cukup agar mudah dibaca.</li>
                        <li><strong>Teks Alternatif Gambar:</strong> setiap gambar informatif dilengkapi teks alternatif (alt text) yang menjelaskan isinya.</li>
                        <li><strong>Struktur Heading yang Jelas:</strong> halaman disusun dengan hierarki judul yang teratur sehingga mudah dinavigasi menggunakan pembaca layar.</li>
                        <li><strong>Tampilan Responsif:</strong> tata letak menyesuaikan dengan ukuran layar, termasuk perangkat seluler.</li>
                    </ul>

                    <h2>Masukan &amp; Bantuan Aksesibilitas</h2>
                    <p>Jika Anda mengalami kendala dalam mengakses situs ini atau memiliki saran untuk meningkatkan aksesibilitas, kami sangat menghargai masukan Anda. Silakan hubungi kami melalui:</p>
                    <ul>
                        <li>Email: <a href="mailto:univ.pgriwiranegara@gmail.com">univ.pgriwiranegara@gmail.com</a></li>
                        <li>Telepon/WhatsApp: <a href="https://wa.me/6282141554377">0821-4155-4377</a></li>
                    </ul>
                    <p>Kami akan menindaklanjuti setiap laporan dan berupaya memperbaiki kendala aksesibilitas secepat mungkin.</p>
                    HTML,
            ]
        );
    }
}
