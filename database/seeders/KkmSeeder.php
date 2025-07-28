<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KkmMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;

class KkmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mataPelajaran = MataPelajaran::all();
        $kelas = Kelas::all();
        
        // KKM default berdasarkan mata pelajaran
        $kkmDefault = [
            'Matematika' => 75,
            'Bahasa Indonesia' => 78,
            'Bahasa Inggris' => 75,
            'Fisika' => 75,
            'Kimia' => 75,
            'Biologi' => 78,
            'Sejarah' => 80,
            'Geografi' => 78,
            'Ekonomi' => 78,
            'Sosiologi' => 80,
            'PKn' => 80,
            'Agama' => 80,
            'Penjaskes' => 75,
            'Seni Budaya' => 75,
            'Prakarya' => 75
        ];

        foreach ($mataPelajaran as $mapel) {
            $kkm = $kkmDefault[$mapel->nama_mapel] ?? 75;
            
            foreach ($kelas as $kelasItem) {
                // KKM untuk semester ganjil
                KkmMataPelajaran::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'kelas_id' => $kelasItem->id,
                    'kkm' => $kkm,
                    'semester' => 'ganjil',
                    'tahun_ajaran' => '2024/2025'
                ]);

                // KKM untuk semester genap
                KkmMataPelajaran::create([
                    'mata_pelajaran_id' => $mapel->id,
                    'kelas_id' => $kelasItem->id,
                    'kkm' => $kkm,
                    'semester' => 'genap',
                    'tahun_ajaran' => '2024/2025'
                ]);
            }
        }
    }
}
