<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use Faker\Factory as Faker;

class SimpleSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        // Ambil semua kelas yang tersedia
        $kelasList = Kelas::all();
        
        if ($kelasList->isEmpty()) {
            $this->command->error('❌ Tidak ada kelas yang tersedia. Jalankan KelasSeeder terlebih dahulu.');
            return;
        }

        // Daftar nama yang umum di Indonesia
        $namaDepan = [
            'Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Galih', 'Hadi', 'Indra', 'Joko',
            'Krisna', 'Lukman', 'Made', 'Nanda', 'Oscar', 'Putra', 'Qori', 'Rama', 'Sandi', 'Taufik',
            'Siti', 'Ani', 'Bela', 'Cinta', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana', 'Indah',
            'Jihan', 'Kartika', 'Lina', 'Maya', 'Nisa', 'Ovi', 'Putri', 'Qonita', 'Rina', 'Sarah'
        ];

        $namaBelakang = [
            'Pratama', 'Wijaya', 'Sari', 'Putra', 'Putri', 'Lestari', 'Utama', 'Kusuma', 'Permana',
            'Santoso', 'Rahayu', 'Setiawan', 'Handayani', 'Kurniawan', 'Wulandari', 'Susanto',
            'Situmorang', 'Hasibuan', 'Manurung', 'Siregar', 'Saputra', 'Saputri', 'Hidayat'
        ];

        $kotaAsal = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Dumai', 'Pekanbaru', 'Padang'
        ];

        $this->command->info('🔄 Memulai pembuatan 150 data siswa...');
        $progressBar = $this->command->getOutput()->createProgressBar(150);
        $progressBar->start();

        // Bagi siswa ke dalam kelas secara merata
        $siswaPerKelas = intval(150 / $kelasList->count());
        $siswaExtra = 150 % $kelasList->count();
        
        $currentSiswaCount = 0;
        
        foreach ($kelasList as $index => $kelas) {
            $jumlahSiswaKelas = $siswaPerKelas + ($index < $siswaExtra ? 1 : 0);
            
            for ($i = 0; $i < $jumlahSiswaKelas; $i++) {
                $currentSiswaCount++;
                
                // Generate data siswa
                $namaLengkap = $faker->randomElement($namaDepan) . ' ' . $faker->randomElement($namaBelakang);
                $nis = str_pad($currentSiswaCount, 6, '0', STR_PAD_LEFT);
                $email = strtolower(str_replace(' ', '.', $namaLengkap)) . $currentSiswaCount . '@student.smanse1dumai.sch.id';
                $jenisKelamin = $faker->randomElement(['L', 'P']);
                $tanggalLahir = $faker->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d');
                $tempatLahir = $faker->randomElement($kotaAsal);
                
                // Buat data siswa (tanpa user_id dulu)
                Siswa::create([
                    'nis' => $nis,
                    'nama_lengkap' => $namaLengkap,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tanggalLahir,
                    'jenis_kelamin' => $jenisKelamin,
                    'agama' => $faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
                    'alamat' => $faker->address,
                    'kelas_id' => $kelas->id,
                    'no_telepon' => '08' . $faker->numerify('##########'),
                    'email' => $email,
                    'nama_ayah' => $faker->name('male'),
                    'nama_ibu' => $faker->name('female'),
                    'pekerjaan_ayah' => $faker->randomElement([
                        'PNS', 'Karyawan Swasta', 'Wiraswasta', 'Petani', 'Buruh', 'Guru'
                    ]),
                    'pekerjaan_ibu' => $faker->randomElement([
                        'Ibu Rumah Tangga', 'PNS', 'Karyawan Swasta', 'Guru', 'Perawat'
                    ]),
                    'status' => 'aktif',
                    'tahun_masuk' => $this->getTahunMasuk($kelas->tingkat),
                ]);
                
                $progressBar->advance();
            }
        }
        
        $progressBar->finish();
        $this->command->newLine();
        
        // Tampilkan ringkasan
        $this->command->info('✅ Berhasil membuat 150 siswa dengan distribusi:');
        foreach ($kelasList as $kelas) {
            $jumlahSiswa = Siswa::where('kelas_id', $kelas->id)->count();
            $this->command->info("   📚 {$kelas->nama_kelas}: {$jumlahSiswa} siswa");
        }
    }
    
    private function getTahunMasuk($tingkat)
    {
        $currentYear = date('Y');
        return $currentYear - ($tingkat - 10);
    }
}
