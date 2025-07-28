<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JenisNilai;

class JenisNilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenisNilai = [
            // Kategori Pengetahuan (Kognitif)
            [
                'nama' => 'Tugas Harian',
                'kategori' => 'pengetahuan',
                'bobot' => 20.00,
                'deskripsi' => 'Nilai dari tugas-tugas harian yang diberikan guru',
                'status' => true
            ],
            [
                'nama' => 'Ulangan Harian',
                'kategori' => 'pengetahuan',
                'bobot' => 30.00,
                'deskripsi' => 'Nilai dari ulangan harian per bab/materi',
                'status' => true
            ],
            [
                'nama' => 'UTS (Ujian Tengah Semester)',
                'kategori' => 'pengetahuan',
                'bobot' => 20.00,
                'deskripsi' => 'Nilai ujian tengah semester',
                'status' => true
            ],
            [
                'nama' => 'UAS (Ujian Akhir Semester)',
                'kategori' => 'pengetahuan',
                'bobot' => 30.00,
                'deskripsi' => 'Nilai ujian akhir semester',
                'status' => true
            ],
            
            // Kategori Keterampilan (Psikomotorik)
            [
                'nama' => 'Praktik',
                'kategori' => 'keterampilan',
                'bobot' => 40.00,
                'deskripsi' => 'Nilai dari kegiatan praktik dan demonstrasi',
                'status' => true
            ],
            [
                'nama' => 'Projek',
                'kategori' => 'keterampilan',
                'bobot' => 35.00,
                'deskripsi' => 'Nilai dari projek yang dikerjakan siswa',
                'status' => true
            ],
            [
                'nama' => 'Portofolio',
                'kategori' => 'keterampilan',
                'bobot' => 25.00,
                'deskripsi' => 'Nilai dari kumpulan karya siswa',
                'status' => true
            ],
            
            // Kategori Sikap (Afektif)
            [
                'nama' => 'Spiritual',
                'kategori' => 'sikap',
                'bobot' => 50.00,
                'deskripsi' => 'Penilaian sikap spiritual siswa',
                'status' => true
            ],
            [
                'nama' => 'Sosial',
                'kategori' => 'sikap',
                'bobot' => 50.00,
                'deskripsi' => 'Penilaian sikap sosial siswa',
                'status' => true
            ]
        ];

        foreach ($jenisNilai as $jenis) {
            JenisNilai::create($jenis);
        }
    }
}
