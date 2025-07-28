<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MataPelajaran;

class MataPelajaranSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataPelajaran = [
            // Mata Pelajaran Wajib
            [
                'kode_mapel' => 'PAI',
                'nama_mapel' => 'Pendidikan Agama dan Budi Pekerti',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kemampuan peserta didik dalam memahami, menghayati, dan mengamalkan nilai-nilai agama',
                'jam_pelajaran' => 3
            ],
            [
                'kode_mapel' => 'PPG',
                'nama_mapel' => 'Pendidikan Pancasila',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kemampuan warga negara yang demokratis dan bertanggung jawab',
                'jam_pelajaran' => 2
            ],
            [
                'kode_mapel' => 'BIN',
                'nama_mapel' => 'Bahasa Indonesia',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kemampuan berkomunikasi dalam bahasa Indonesia',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'MTK',
                'nama_mapel' => 'Matematika',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kemampuan berpikir logis, analitis, sistematis, kritis, dan kreatif',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'SEJ',
                'nama_mapel' => 'Sejarah Indonesia',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kesadaran peserta didik tentang pentingnya waktu dan tempat',
                'jam_pelajaran' => 2
            ],
            [
                'kode_mapel' => 'ENG',
                'nama_mapel' => 'Bahasa Inggris',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kemampuan berkomunikasi dalam bahasa Inggris',
                'jam_pelajaran' => 2
            ],
            
            // Mata Pelajaran Peminatan IPA
            [
                'kode_mapel' => 'MTKP',
                'nama_mapel' => 'Matematika Peminatan',
                'deskripsi' => 'Mata pelajaran matematika lanjutan untuk peminatan IPA',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'FIS',
                'nama_mapel' => 'Fisika',
                'deskripsi' => 'Mata pelajaran yang mempelajari fenomena alam dan interaksi dalam lingkup alam semesta',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'KIM',
                'nama_mapel' => 'Kimia',
                'deskripsi' => 'Mata pelajaran yang mempelajari struktur, sifat, dan reaksi materi',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'BIO',
                'nama_mapel' => 'Biologi',
                'deskripsi' => 'Mata pelajaran yang mempelajari makhluk hidup dan kehidupan',
                'jam_pelajaran' => 4
            ],
            
            // Mata Pelajaran Peminatan IPS
            [
                'kode_mapel' => 'GEO',
                'nama_mapel' => 'Geografi',
                'deskripsi' => 'Mata pelajaran yang mempelajari permukaan bumi, fenomena alam, dan aktivitas manusia',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'SEJP',
                'nama_mapel' => 'Sejarah Peminatan',
                'deskripsi' => 'Mata pelajaran sejarah lanjutan untuk peminatan IPS',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'SOS',
                'nama_mapel' => 'Sosiologi',
                'deskripsi' => 'Mata pelajaran yang mempelajari masyarakat dan interaksi sosial',
                'jam_pelajaran' => 4
            ],
            [
                'kode_mapel' => 'EKO',
                'nama_mapel' => 'Ekonomi',
                'deskripsi' => 'Mata pelajaran yang mempelajari kegiatan manusia dalam memenuhi kebutuhan hidup',
                'jam_pelajaran' => 4
            ],
            
            // Muatan Lokal dan Wajib Lainnya
            [
                'kode_mapel' => 'BDAR',
                'nama_mapel' => 'Bahasa Daerah',
                'deskripsi' => 'Mata pelajaran bahasa daerah setempat',
                'jam_pelajaran' => 2
            ],
            [
                'kode_mapel' => 'PJOK',
                'nama_mapel' => 'Pendidikan Jasmani dan Kesehatan',
                'deskripsi' => 'Mata pelajaran yang mengembangkan aspek kebugaran jasmani, keterampilan gerak, dan gaya hidup sehat',
                'jam_pelajaran' => 3
            ],
            [
                'kode_mapel' => 'SBD',
                'nama_mapel' => 'Seni Budaya',
                'deskripsi' => 'Mata pelajaran yang mengembangkan kreativitas dan apresiasi seni',
                'jam_pelajaran' => 2
            ],
            [
                'kode_mapel' => 'INF',
                'nama_mapel' => 'Informatika',
                'deskripsi' => 'Mata pelajaran yang mengembangkan pemikiran komputasional dan literasi digital',
                'jam_pelajaran' => 2
            ]
        ];

        foreach ($mataPelajaran as $data) {
            MataPelajaran::create($data);
        }
    }
}
