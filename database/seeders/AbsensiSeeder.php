<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Absensi;
use App\Models\User;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use Carbon\Carbon;

class AbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data siswa, guru, dan mata pelajaran
        $siswaList = Siswa::with('user')->get();
        $guruList = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->get();
        $mataPelajaranList = MataPelajaran::all();

        if ($siswaList->isEmpty() || $guruList->isEmpty() || $mataPelajaranList->isEmpty()) {
            $this->command->info('Tidak ada data siswa, guru, atau mata pelajaran. Seeder dibatalkan.');
            return;
        }

        // Data absensi untuk 2 minggu terakhir
        $tanggalMulai = Carbon::now()->subWeeks(2);
        $tanggalSelesai = Carbon::now();

        $statusList = ['hadir', 'sakit', 'izin', 'alpha'];
        $keteranganList = [
            'hadir' => ['Tepat waktu', 'Hadir penuh', ''],
            'sakit' => ['Demam', 'Flu', 'Sakit kepala', 'Izin dokter'],
            'izin' => ['Acara keluarga', 'Keperluan mendadak', 'Izin orang tua'],
            'alpha' => ['Tanpa keterangan', '']
        ];

        $this->command->info('Membuat data absensi...');

        // Loop tanggal
        for ($tanggal = $tanggalMulai; $tanggal <= $tanggalSelesai; $tanggal->addDay()) {
            // Skip weekend
            if ($tanggal->isWeekend()) {
                continue;
            }

            // Random beberapa mata pelajaran per hari
            $mataPelajaranHari = $mataPelajaranList->random(rand(3, 5));

            foreach ($mataPelajaranHari as $mataPelajaran) {
                $guru = $guruList->random();

                // Random beberapa siswa untuk mata pelajaran ini
                $siswaHari = $siswaList->random(rand(15, 25));

                foreach ($siswaHari as $siswa) {
                    // Buat status dengan probabilitas: 80% hadir, 10% sakit, 5% izin, 5% alpha
                    $rand = rand(1, 100);
                    if ($rand <= 80) {
                        $status = 'hadir';
                    } elseif ($rand <= 90) {
                        $status = 'sakit';
                    } elseif ($rand <= 95) {
                        $status = 'izin';
                    } else {
                        $status = 'alpha';
                    }

                    $keterangan = collect($keteranganList[$status])->random();

                    // Jam masuk dan keluar (hanya untuk yang hadir)
                    $jamMasuk = null;
                    $jamKeluar = null;

                    if ($status === 'hadir') {
                        $jamMasuk = $tanggal->copy()->setHour(rand(7, 8))->setMinute(rand(0, 59));
                        $jamKeluar = $jamMasuk->copy()->addHours(rand(2, 4))->addMinutes(rand(0, 59));
                    }

                    Absensi::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'tanggal' => $tanggal->format('Y-m-d'),
                            'mata_pelajaran_id' => $mataPelajaran->id,
                            'guru_id' => $guru->id
                        ],
                        [
                            'status' => $status,
                            'keterangan' => $keterangan,
                            'jam_masuk' => $jamMasuk,
                            'jam_keluar' => $jamKeluar,
                            'semester' => 'ganjil',
                            'tahun_ajaran' => '2024/2025'
                        ]
                    );
                }
            }
        }

        $this->command->info('Data absensi berhasil dibuat!');
    }
}
