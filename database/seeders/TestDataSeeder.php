<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\JadwalPelajaran;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ambil roles yang sudah ada (harus dijalankan setelah RoleSeeder)
        $roleGuru = Role::where('name', 'guru')->first();
        $roleKepalaSekolah = Role::where('name', 'kepala_tatausaha')->first();

        if (!$roleGuru || !$roleKepalaSekolah) {
            $this->command->error('❌ Role belum ada! Jalankan dulu: php artisan db:seed --class=RoleSeeder');
            return;
        }        // 2. Buat guru test
        $guruTest = User::firstOrCreate([
            'email' => 'guru@test.com'
        ], [
            'name' => 'Ahmad Fauzi',
            'password' => bcrypt('password'),
            'role_id' => $roleGuru->id,
            'nip' => '123456789',
            'jenis_kelamin' => 'Laki-laki'
        ]);

        // 3. Buat kepala tata usaha test
        $kepalaSekolah = User::firstOrCreate([
            'email' => 'kepsek@test.com'
        ], [
            'name' => 'Dr. Siti Nurhaliza',
            'password' => bcrypt('password'),
            'role_id' => $roleKepalaSekolah->id,
            'nip' => '987654321',
            'jenis_kelamin' => 'Perempuan'
        ]);

        // 4. Buat mata pelajaran test jika belum ada
        $mataPelajaran = [
            ['kode_mapel' => 'MAT', 'nama_mapel' => 'Matematika', 'jam_pelajaran' => 4],
            ['kode_mapel' => 'BID', 'nama_mapel' => 'Bahasa Indonesia', 'jam_pelajaran' => 4],
            ['kode_mapel' => 'BIG', 'nama_mapel' => 'Bahasa Inggris', 'jam_pelajaran' => 3],
            ['kode_mapel' => 'FIS', 'nama_mapel' => 'Fisika', 'jam_pelajaran' => 3],
            ['kode_mapel' => 'KIM', 'nama_mapel' => 'Kimia', 'jam_pelajaran' => 3]
        ];

        foreach ($mataPelajaran as $mapel) {
            MataPelajaran::firstOrCreate([
                'kode_mapel' => $mapel['kode_mapel']
            ], $mapel);
        }

        // 5. Buat kelas test jika belum ada
        $kelasData = [
            ['nama_kelas' => 'X-IPA-1', 'tingkat' => '10', 'jurusan' => 'IPA', 'kapasitas' => 30],
            ['nama_kelas' => 'X-IPA-2', 'tingkat' => '10', 'jurusan' => 'IPA', 'kapasitas' => 30],
            ['nama_kelas' => 'XI-IPA-1', 'tingkat' => '11', 'jurusan' => 'IPA', 'kapasitas' => 28],
        ];

        foreach ($kelasData as $kelas) {
            Kelas::firstOrCreate([
                'nama_kelas' => $kelas['nama_kelas']
            ], $kelas);
        }

        // 6. Buat siswa test untuk kelas X-IPA-1
        $kelasXIPA1 = Kelas::where('nama_kelas', 'X-IPA-1')->first();
        
        if ($kelasXIPA1) {
            $siswaData = [
                ['nis' => '2024001', 'nama_lengkap' => 'Ahmad Rizki', 'jenis_kelamin' => 'Laki-laki'],
                ['nis' => '2024002', 'nama_lengkap' => 'Siti Fatimah', 'jenis_kelamin' => 'Perempuan'],
                ['nis' => '2024003', 'nama_lengkap' => 'Budi Santoso', 'jenis_kelamin' => 'Laki-laki'],
                ['nis' => '2024004', 'nama_lengkap' => 'Rina Sari', 'jenis_kelamin' => 'Perempuan'],
                ['nis' => '2024005', 'nama_lengkap' => 'Doni Pratama', 'jenis_kelamin' => 'Laki-laki'],
                ['nis' => '2024006', 'nama_lengkap' => 'Maya Indah', 'jenis_kelamin' => 'Perempuan'],
                ['nis' => '2024007', 'nama_lengkap' => 'Andi Wijaya', 'jenis_kelamin' => 'Laki-laki'],
                ['nis' => '2024008', 'nama_lengkap' => 'Dewi Lestari', 'jenis_kelamin' => 'Perempuan'],
            ];

            foreach ($siswaData as $siswa) {
                Siswa::firstOrCreate([
                    'nis' => $siswa['nis']
                ], array_merge($siswa, [
                    'tanggal_lahir' => '2007-01-01',
                    'tempat_lahir' => 'Jakarta',
                    'alamat' => 'Jl. Test No. 1',
                    'kelas_id' => $kelasXIPA1->id,
                    'tahun_masuk' => 2024,
                    'status' => 'aktif'
                ]));
            }
        }

        // 7. Buat jadwal pelajaran test
        $matematika = MataPelajaran::where('nama_mapel', 'Matematika')->first();
        $bahasaIndonesia = MataPelajaran::where('nama_mapel', 'Bahasa Indonesia')->first();
        
        if ($matematika && $kelasXIPA1) {
            JadwalPelajaran::firstOrCreate([
                'mata_pelajaran_id' => $matematika->id,
                'kelas_id' => $kelasXIPA1->id,
                'guru_id' => $guruTest->id,
                'hari' => 'Senin'
            ], [
                'jam_mulai' => '07:30',
                'jam_selesai' => '09:00',
                'ruangan' => 'R.101',
                'semester' => 'ganjil',
                'tahun_ajaran' => '2024/2025',
                'status' => true
            ]);
        }

        if ($bahasaIndonesia && $kelasXIPA1) {
            JadwalPelajaran::firstOrCreate([
                'mata_pelajaran_id' => $bahasaIndonesia->id,
                'kelas_id' => $kelasXIPA1->id,
                'guru_id' => $guruTest->id,
                'hari' => 'Selasa'
            ], [
                'jam_mulai' => '07:30',
                'jam_selesai' => '09:00',
                'ruangan' => 'R.102',
                'semester' => 'ganjil',
                'tahun_ajaran' => '2024/2025',
                'status' => true
            ]);
        }

        $this->command->info('✅ Test data berhasil dibuat:');
        $this->command->info('📧 Guru: guru@test.com / password');
        $this->command->info('📧 Kepala Tata Usaha: kepsek@test.com / password');
        $this->command->info('👥 8 siswa di kelas X-IPA-1');
        $this->command->info('📚 Jadwal: Matematika & Bahasa Indonesia');
    }
}
