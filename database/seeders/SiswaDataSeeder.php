<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class SiswaDataSeeder extends Seeder
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
        
        // Ambil role murid
        $muridRole = Role::where('name', 'murid')->first();
        
        if (!$muridRole) {
            $this->command->error('❌ Role "murid" tidak ditemukan. Pastikan role sudah ada di database.');
            return;
        }

        // Daftar nama depan dan belakang yang umum di Indonesia
        $namaDepan = [
            'Ahmad', 'Budi', 'Candra', 'Dedi', 'Eko', 'Fajar', 'Galih', 'Hadi', 'Indra', 'Joko',
            'Krisna', 'Lukman', 'Made', 'Nanda', 'Oscar', 'Putra', 'Qori', 'Rama', 'Sandi', 'Taufik',
            'Umar', 'Vino', 'Wahyu', 'Yogi', 'Zaki', 'Andi', 'Bayu', 'Citra', 'Dimas', 'Eka',
            'Siti', 'Ani', 'Bela', 'Cinta', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana', 'Indah',
            'Jihan', 'Kartika', 'Lina', 'Maya', 'Nisa', 'Ovi', 'Putri', 'Qonita', 'Rina', 'Sarah',
            'Tari', 'Umi', 'Vina', 'Wulan', 'Yuli', 'Zahra', 'Ayu', 'Bella', 'Cici', 'Dina'
        ];

        $namaBelakang = [
            'Pratama', 'Wijaya', 'Sari', 'Putra', 'Putri', 'Lestari', 'Utama', 'Kusuma', 'Permana', 'Mahendra',
            'Santoso', 'Rahayu', 'Setiawan', 'Handayani', 'Kurniawan', 'Wulandari', 'Susanto', 'Maharani', 'Gunawan', 'Anggraini',
            'Situmorang', 'Hasibuan', 'Manurung', 'Siregar', 'Siahaan', 'Panjaitan', 'Simanjuntak', 'Simbolon', 'Tampubolon', 'Nababan',
            'Saputra', 'Saputri', 'Ramadan', 'Hidayat', 'Maulana', 'Safitri', 'Nuraini', 'Oktavia', 'Novita', 'Puspita'
        ];

        $kotaAsal = [
            'Jakarta', 'Surabaya', 'Bandung', 'Medan', 'Semarang', 'Makassar', 'Palembang', 'Tangerang',
            'Depok', 'Bekasi', 'Bogor', 'Batam', 'Pekanbaru', 'Bandar Lampung', 'Malang', 'Yogyakarta',
            'Samarinda', 'Denpasar', 'Balikpapan', 'Pontianak', 'Jambi', 'Cirebon', 'Sukabumi', 'Tasikmalaya',
            'Serang', 'Manado', 'Padang', 'Banjarmasin', 'Mataram', 'Kendari', 'Dumai', 'Pematangsiantar'
        ];

        $this->command->info('🔄 Memulai pembuatan 150 data siswa...');
        $progressBar = $this->command->getOutput()->createProgressBar(150);
        $progressBar->start();

        // Bagi siswa ke dalam kelas secara merata
        $siswaPerKelas = intval(150 / $kelasList->count()); // 150 / 8 = 18-19 siswa per kelas
        $siswaExtra = 150 % $kelasList->count(); // sisa siswa
        
        $currentSiswaCount = 0;
        
        foreach ($kelasList as $index => $kelas) {
            // Tentukan jumlah siswa untuk kelas ini
            $jumlahSiswaKelas = $siswaPerKelas + ($index < $siswaExtra ? 1 : 0);
            
            for ($i = 0; $i < $jumlahSiswaKelas; $i++) {
                $currentSiswaCount++;
                
                // Generate data siswa
                $namaLengkap = $faker->randomElement($namaDepan) . ' ' . $faker->randomElement($namaBelakang);
                $nis = str_pad($currentSiswaCount, 6, '0', STR_PAD_LEFT); // NIS 6 digit
                $email = strtolower(str_replace(' ', '.', $namaLengkap)) . $currentSiswaCount . '@student.smanse1dumai.sch.id';
                $jenisKelamin = $faker->randomElement(['laki-laki', 'perempuan']); // Ubah format
                $tanggalLahir = $faker->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d');
                $tempatLahir = $faker->randomElement($kotaAsal);
                
                // Buat user account untuk siswa
                $user = User::create([
                    'name' => $namaLengkap,
                    'email' => $email,
                    'password' => Hash::make(str_replace('-', '', $tanggalLahir)), // Password default = tanggal lahir
                    'role_id' => $muridRole->id,
                    'no_telepon' => '08' . $faker->numerify('##########'),
                    'alamat' => $faker->address,
                    'jenis_kelamin' => $jenisKelamin,
                    'tanggal_lahir' => $tanggalLahir,
                    'email_verified_at' => now(),
                ]);

                // Buat data siswa
                Siswa::create([
                    'user_id' => $user->id,
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
                        'PNS', 'Karyawan Swasta', 'Wiraswasta', 'Petani', 'Buruh', 'Guru', 'Dokter', 'Polisi', 'TNI', 'Pedagang'
                    ]),
                    'pekerjaan_ibu' => $faker->randomElement([
                        'Ibu Rumah Tangga', 'PNS', 'Karyawan Swasta', 'Guru', 'Dokter', 'Perawat', 'Pedagang', 'Wiraswasta'
                    ]),
                    'status' => 'aktif',
                    'tahun_masuk' => $this->getTahunMasuk($kelas->tingkat),
                    'created_at' => now(),
                    'updated_at' => now(),
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
        
        $this->command->info('');
        $this->command->info('📋 Info Login Siswa:');
        $this->command->info('   📧 Email: [nama.siswa][nomor]@student.smanse1dumai.sch.id');
        $this->command->info('   🔐 Password: password123 (untuk semua siswa)');
        $this->command->info('   👤 Role: murid');
    }
    
    /**
     * Tentukan tahun masuk berdasarkan tingkat kelas
     */
    private function getTahunMasuk($tingkat)
    {
        $currentYear = date('Y');
        return $currentYear - ($tingkat - 10); // Kelas 10 = tahun ini, 11 = tahun lalu, 12 = 2 tahun lalu
    }
}
