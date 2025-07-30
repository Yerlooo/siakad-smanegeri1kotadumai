<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kelas;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kelasData = [
            [
                'nama_kelas' => 'X.2',
                'tingkat' => 10,
                'jurusan' => 'IPA',
                'kapasitas' => 32,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'X.3',
                'tingkat' => 10,
                'jurusan' => 'IPS',
                'kapasitas' => 32,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XI.1',
                'tingkat' => 11,
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XI.2',
                'tingkat' => 11,
                'jurusan' => 'IPA',
                'kapasitas' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XI.3',
                'tingkat' => 11,
                'jurusan' => 'IPS',
                'kapasitas' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XII.1',
                'tingkat' => 12,
                'jurusan' => 'IPA',
                'kapasitas' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XII.2',
                'tingkat' => 12,
                'jurusan' => 'IPA',
                'kapasitas' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_kelas' => 'XII.3',
                'tingkat' => 12,
                'jurusan' => 'IPS',
                'kapasitas' => 28,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($kelasData as $kelas) {
            Kelas::updateOrCreate(
                ['nama_kelas' => $kelas['nama_kelas']],
                $kelas
            );
        }

        $this->command->info('✅ 8 Kelas berhasil dibuat: X.2, X.3, XI.1, XI.2, XI.3, XII.1, XII.2, XII.3');
    }
}
