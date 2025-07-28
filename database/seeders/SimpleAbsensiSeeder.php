<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\User;
use Carbon\Carbon;

class SimpleAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cek data yang ada
        $siswaCount = Siswa::count();
        $guruCount = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->count();
        $mataPelajaranCount = MataPelajaran::count();

        $this->command->info("Data tersedia:");
        $this->command->info("- Siswa: {$siswaCount}");
        $this->command->info("- Guru: {$guruCount}");
        $this->command->info("- Mata Pelajaran: {$mataPelajaranCount}");

        if ($siswaCount === 0 || $guruCount === 0 || $mataPelajaranCount === 0) {
            $this->command->warn('Data tidak lengkap untuk membuat absensi sample.');
            return;
        }

        // Ambil beberapa data untuk test
        $siswaList = Siswa::take(5)->get();
        $guru = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->first();
        $mataPelajaran = MataPelajaran::first();

        if (!$guru || !$mataPelajaran) {
            $this->command->warn('Guru atau mata pelajaran tidak ditemukan.');
            return;
        }

        // Buat absensi sample untuk hari ini
        $tanggal = Carbon::today();
        $statusList = ['hadir', 'sakit', 'izin', 'alpha'];

        foreach ($siswaList as $siswa) {
            $status = $statusList[array_rand($statusList)];
            
            Absensi::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'tanggal' => $tanggal,
                    'mata_pelajaran_id' => $mataPelajaran->id,
                    'guru_id' => $guru->id
                ],
                [
                    'status' => $status,
                    'keterangan' => $status === 'hadir' ? 'Test data' : 'Sample ' . $status,
                    'jam_masuk' => $status === 'hadir' ? $tanggal->copy()->setHour(8)->setMinute(0) : null,
                    'jam_keluar' => $status === 'hadir' ? $tanggal->copy()->setHour(10)->setMinute(0) : null,
                    'semester' => 'ganjil',
                    'tahun_ajaran' => '2024/2025'
                ]
            );
        }

        $this->command->info('Absensi sample berhasil dibuat!');
    }
}
