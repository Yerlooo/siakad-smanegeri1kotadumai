<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\NilaiSiswa;
use App\Models\Absensi;
use App\Models\JadwalPelajaran;
use App\Models\MataPelajaran;
use App\Models\JenisNilai;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WaliKelasPrivilegeController extends Controller
{
    /**
     * Dashboard khusus untuk wali kelas
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        if (!$user->isWaliKelas()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya wali kelas yang dapat mengakses halaman ini.');
        }

        $kelasAsWali = $user->getWaliKelasClasses();
        $stats = [];
        $recentActivities = [];

        foreach ($kelasAsWali as $kelas) {
            $siswaIds = $kelas->siswa->pluck('id');
            
            // Statistik per kelas
            $kelasStats = [
                'kelas_id' => $kelas->id,
                'nama_kelas' => $kelas->nama_kelas,
                'total_siswa' => $kelas->siswa->count(),
                'hadir_hari_ini' => Absensi::whereIn('siswa_id', $siswaIds)
                    ->whereDate('tanggal', today())
                    ->where('status', 'hadir')
                    ->count(),
                'rata_rata_nilai' => round(NilaiSiswa::whereIn('siswa_id', $siswaIds)->avg('nilai') ?? 0, 2),
                'siswa_berprestasi' => NilaiSiswa::whereIn('siswa_id', $siswaIds)
                    ->where('nilai', '>=', 85)
                    ->distinct('siswa_id')
                    ->count(),
                'siswa_perlu_perhatian' => NilaiSiswa::whereIn('siswa_id', $siswaIds)
                    ->where('nilai', '<', 70)
                    ->distinct('siswa_id')
                    ->count(),
            ];
            
            $stats[] = $kelasStats;
            
            // Recent activities untuk kelas ini
            $recentSiswa = $kelas->siswa()->latest()->take(3)->get();
            foreach ($recentSiswa as $siswa) {
                $recentActivities[] = [
                    'id' => 'siswa-' . $siswa->id,
                    'type' => 'siswa',
                    'title' => "Siswa baru di kelas {$kelas->nama_kelas}",
                    'description' => $siswa->nama_lengkap,
                    'time' => $siswa->created_at,
                    'kelas' => $kelas->nama_kelas
                ];
            }
        }

        // Sort activities by time
        $recentActivities = collect($recentActivities)->sortByDesc('time')->take(10)->values();

        return Inertia::render('WaliKelas/Dashboard', [
            'kelasStats' => $stats,
            'recentActivities' => $recentActivities,
            'totalKelas' => $kelasAsWali->count(),
            'kelas' => $kelasAsWali
        ]);
    }

    /**
     * Monitoring siswa untuk wali kelas - akses ke semua siswa di kelas yang diampu
     */
    public function monitoringSiswa(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isWaliKelas()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $kelasId = $request->get('kelas_id');
        $kelasAsWali = $user->getWaliKelasClasses();
        
        // Filter kelas berdasarkan yang dipilih
        if ($kelasId) {
            $kelas = $kelasAsWali->where('id', $kelasId)->first();
            if (!$kelas) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
            }
            $siswaList = $kelas->siswa()->with(['kelas', 'user'])->get();
        } else {
            // Ambil semua siswa dari semua kelas yang diampu
            $siswaList = collect();
            foreach ($kelasAsWali as $kelas) {
                $siswaList = $siswaList->merge($kelas->siswa()->with(['kelas', 'user'])->get());
            }
        }

        // Enhanced data untuk setiap siswa
        $siswaData = $siswaList->map(function ($siswa) {
            $nilaiTerbaru = NilaiSiswa::where('siswa_id', $siswa->id)
                ->with(['mataPelajaran', 'jenisNilai'])
                ->latest()
                ->take(5)
                ->get();
            
            $absensiStats = [
                'hadir' => Absensi::where('siswa_id', $siswa->id)->where('status', 'hadir')->count(),
                'sakit' => Absensi::where('siswa_id', $siswa->id)->where('status', 'sakit')->count(),
                'izin' => Absensi::where('siswa_id', $siswa->id)->where('status', 'izin')->count(),
                'alpha' => Absensi::where('siswa_id', $siswa->id)->where('status', 'alpha')->count(),
            ];
            
            $totalAbsensi = array_sum($absensiStats);
            $persentaseKehadiran = $totalAbsensi > 0 ? round(($absensiStats['hadir'] / $totalAbsensi) * 100, 2) : 0;

            return [
                'siswa' => $siswa,
                'nilai_terbaru' => $nilaiTerbaru,
                'rata_rata_nilai' => round(NilaiSiswa::where('siswa_id', $siswa->id)->avg('nilai') ?? 0, 2),
                'absensi_stats' => $absensiStats,
                'persentase_kehadiran' => $persentaseKehadiran,
                'status_akademik' => $this->getStatusAkademik($siswa->id),
            ];
        });

        return Inertia::render('WaliKelas/MonitoringSiswa', [
            'siswaData' => $siswaData,
            'kelasAsWali' => $kelasAsWali,
            'selectedKelas' => $kelasId,
        ]);
    }

    /**
     * Laporan komprehensif kelas untuk wali kelas
     */
    public function laporanKelas($kelasId)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAsWaliKelas($kelasId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $kelas = Kelas::with(['siswa', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
            ->findOrFail($kelasId);

        // Statistik lengkap kelas
        $siswaIds = $kelas->siswa->pluck('id');
        
        $statistik = [
            'total_siswa' => $kelas->siswa->count(),
            'siswa_laki' => $kelas->siswa->where('jenis_kelamin', 'laki-laki')->count(),
            'siswa_perempuan' => $kelas->siswa->where('jenis_kelamin', 'perempuan')->count(),
            'rata_rata_kelas' => round(NilaiSiswa::whereIn('siswa_id', $siswaIds)->avg('nilai') ?? 0, 2),
            'nilai_tertinggi' => NilaiSiswa::whereIn('siswa_id', $siswaIds)->max('nilai') ?? 0,
            'nilai_terendah' => NilaiSiswa::whereIn('siswa_id', $siswaIds)->min('nilai') ?? 0,
            'kehadiran_rata' => $this->getAverageAttendance($siswaIds),
            'prestasi_count' => NilaiSiswa::whereIn('siswa_id', $siswaIds)->where('nilai', '>=', 85)->distinct('siswa_id')->count(),
            'kehadiran_tertinggi' => $this->getHighestAttendance($siswaIds),
            'kehadiran_terendah' => $this->getLowestAttendance($siswaIds),
        ];

        // Ranking siswa berdasarkan rata-rata nilai
        $rankingSiswa = $kelas->siswa->map(function ($siswa) {
            $rataRata = round(NilaiSiswa::where('siswa_id', $siswa->id)->avg('nilai') ?? 0, 2);
            $kehadiran = $this->getStudentAttendance($siswa->id);
            return [
                'siswa' => $siswa,
                'rata_rata' => $rataRata,
                'kehadiran' => $kehadiran,
                'status' => $this->getStatusAkademik($siswa->id)['status']
            ];
        })->sortByDesc('rata_rata')->values();

        // Analisis per mata pelajaran
        $analisisMapel = MataPelajaran::whereHas('jadwalPelajaran', function ($query) use ($kelasId) {
            $query->where('kelas_id', $kelasId);
        })->with(['nilaiSiswa' => function ($query) use ($siswaIds) {
            $query->whereIn('siswa_id', $siswaIds);
        }])->get()->map(function ($mapel) {
            $nilaiMapel = $mapel->nilaiSiswa;
            return [
                'mata_pelajaran' => $mapel->nama_mapel,
                'rata_rata' => round($nilaiMapel->avg('nilai') ?? 0, 2),
                'nilai_tertinggi' => $nilaiMapel->max('nilai') ?? 0,
                'nilai_terendah' => $nilaiMapel->min('nilai') ?? 0,
                'jumlah_nilai' => $nilaiMapel->count(),
            ];
        });

        // Statistik kehadiran
        $absensiStats = [
            'hadir' => Absensi::whereIn('siswa_id', $siswaIds)->where('status', 'hadir')->count(),
            'sakit' => Absensi::whereIn('siswa_id', $siswaIds)->where('status', 'sakit')->count(),
            'izin' => Absensi::whereIn('siswa_id', $siswaIds)->where('status', 'izin')->count(),
            'alpha' => Absensi::whereIn('siswa_id', $siswaIds)->where('status', 'alpha')->count(),
        ];

        return Inertia::render('WaliKelas/LaporanKelas', [
            'kelas' => $kelas,
            'statistik' => $statistik,
            'rankingSiswa' => $rankingSiswa,
            'analisisMapel' => $analisisMapel,
            'absensiStats' => $absensiStats,
        ]);
    }

    /**
     * Akses khusus wali kelas untuk mengedit data siswa di kelasnya
     */
    public function editSiswa($siswaId)
    {
        $user = auth()->user();
        $siswa = Siswa::with(['kelas', 'user'])->findOrFail($siswaId);
        
        if (!$user->hasWaliKelasPrivilegeForSiswa($siswaId)) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengedit siswa di kelas yang Anda ampu.');
        }

        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return Inertia::render('WaliKelas/EditSiswa', [
            'siswa' => $siswa,
            'kelasList' => $kelasList,
            'isWaliKelasAccess' => true,
        ]);
    }

    /**
     * Update data siswa oleh wali kelas
     */
    public function updateSiswa(Request $request, $siswaId)
    {
        $user = auth()->user();
        $siswa = Siswa::findOrFail($siswaId);
        
        if (!$user->hasWaliKelasPrivilegeForSiswa($siswaId)) {
            return redirect()->back()->with('error', 'Anda hanya dapat mengedit siswa di kelas yang Anda ampu.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $siswa->update($request->only([
            'nama_lengkap', 'no_telepon', 'alamat', 'nama_ayah', 'nama_ibu',
            'pekerjaan_ayah', 'pekerjaan_ibu', 'keterangan'
        ]));

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Konsultasi siswa - fitur khusus wali kelas untuk mencatat konsultasi dengan siswa
     */
    public function konsultasiSiswa()
    {
        $user = auth()->user();
        
        if (!$user->isWaliKelas()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $kelasAsWali = $user->getWaliKelasClasses();
        $siswaList = collect();
        
        foreach ($kelasAsWali as $kelas) {
            $siswaList = $siswaList->merge($kelas->siswa()->with('kelas')->get());
        }

        return Inertia::render('WaliKelas/KonsultasiSiswa', [
            'siswaList' => $siswaList,
            'kelasAsWali' => $kelasAsWali,
        ]);
    }

    /**
     * Get status akademik siswa
     */
    private function getStatusAkademik($siswaId)
    {
        $rataRata = round(NilaiSiswa::where('siswa_id', $siswaId)->avg('nilai') ?? 0, 2);
        
        if ($rataRata >= 85) {
            return ['status' => 'Berprestasi', 'color' => 'green'];
        } elseif ($rataRata >= 75) {
            return ['status' => 'Baik', 'color' => 'blue'];
        } elseif ($rataRata >= 65) {
            return ['status' => 'Cukup', 'color' => 'yellow'];
        } else {
            return ['status' => 'Perlu Perhatian', 'color' => 'red'];
        }
    }

    /**
     * Export laporan kelas ke PDF
     */
    public function exportLaporanPDF($kelasId)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAsWaliKelas($kelasId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $kelas = Kelas::with(['siswa', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru'])
            ->findOrFail($kelasId);

        $siswaIds = $kelas->siswa->pluck('id');
        
        $statistik = [
            'total_siswa' => $kelas->siswa->count(),
            'siswa_laki' => $kelas->siswa->where('jenis_kelamin', 'laki-laki')->count(),
            'siswa_perempuan' => $kelas->siswa->where('jenis_kelamin', 'perempuan')->count(),
            'rata_rata_kelas' => round(NilaiSiswa::whereIn('siswa_id', $siswaIds)->avg('nilai') ?? 0, 2),
            'kehadiran_rata' => $this->getAverageAttendance($siswaIds),
            'prestasi_count' => NilaiSiswa::whereIn('siswa_id', $siswaIds)->where('nilai', '>=', 85)->distinct('siswa_id')->count(),
        ];

        $rankingSiswa = $kelas->siswa->map(function ($siswa) {
            $rataRata = round(NilaiSiswa::where('siswa_id', $siswa->id)->avg('nilai') ?? 0, 2);
            $kehadiran = $this->getStudentAttendance($siswa->id);
            return [
                'siswa' => $siswa,
                'rata_rata' => $rataRata,
                'kehadiran' => $kehadiran,
                'status' => $this->getStatusAkademik($siswa->id)['status']
            ];
        })->sortByDesc('rata_rata')->values();

        $pdf = \PDF::loadView('reports.laporan-kelas', [
            'kelas' => $kelas,
            'statistik' => $statistik,
            'rankingSiswa' => $rankingSiswa,
            'tanggal' => now()->format('d F Y')
        ]);

        return $pdf->download("Laporan-Kelas-{$kelas->nama_kelas}.pdf");
    }

    /**
     * Export laporan kelas ke Excel
     */
    public function exportLaporanExcel($kelasId)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAsWaliKelas($kelasId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        return \Excel::download(new \App\Exports\LaporanKelasExport($kelasId), "Laporan-Kelas-{$kelasId}.xlsx");
    }

    /**
     * Export laporan individual siswa ke PDF
     */
    public function exportStudentReportPDF($kelasId, $siswaId)
    {
        $user = auth()->user();
        
        if (!$user->canAccessAsWaliKelas($kelasId)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke kelas ini.');
        }

        $siswa = Siswa::with(['kelas', 'nilaiSiswa.mataPelajaran', 'absensi'])
            ->where('kelas_id', $kelasId)
            ->findOrFail($siswaId);

        // Data lengkap siswa
        $nilaiSiswa = $siswa->nilaiSiswa->groupBy('mata_pelajaran.nama_mapel');
        $rataRata = round($siswa->nilaiSiswa->avg('nilai') ?? 0, 2);
        
        // Statistik kehadiran siswa
        $totalAbsensi = $siswa->absensi->count();
        $absensiStats = [
            'hadir' => $siswa->absensi->where('status', 'hadir')->count(),
            'sakit' => $siswa->absensi->where('status', 'sakit')->count(),
            'izin' => $siswa->absensi->where('status', 'izin')->count(),
            'alpha' => $siswa->absensi->where('status', 'alpha')->count(),
        ];
        
        $kehadiranPersentase = $totalAbsensi > 0 ? round(($absensiStats['hadir'] / $totalAbsensi) * 100, 2) : 0;

        // Ranking siswa di kelas
        $allSiswa = Siswa::where('kelas_id', $kelasId)->get();
        $rankingData = $allSiswa->map(function($s) {
            return [
                'siswa_id' => $s->id,
                'rata_rata' => round(NilaiSiswa::where('siswa_id', $s->id)->avg('nilai') ?? 0, 2)
            ];
        })->sortByDesc('rata_rata')->values();
        
        $ranking = $rankingData->search(function($item) use ($siswaId) {
            return $item['siswa_id'] == $siswaId;
        }) + 1;

        $pdf = \PDF::loadView('reports.individual-student-report', [
            'siswa' => $siswa,
            'nilaiSiswa' => $nilaiSiswa,
            'rataRata' => $rataRata,
            'absensiStats' => $absensiStats,
            'kehadiranPersentase' => $kehadiranPersentase,
            'ranking' => $ranking,
            'totalSiswa' => $allSiswa->count(),
            'tanggal' => now()->format('d F Y')
        ]);

        return $pdf->download("Laporan-Siswa-{$siswa->nama_lengkap}.pdf");
    }

    /**
     * Helper method untuk menghitung rata-rata kehadiran
     */
    private function getAverageAttendance($siswaIds)
    {
        $totalAbsensi = Absensi::whereIn('siswa_id', $siswaIds)->count();
        if ($totalAbsensi == 0) return 0;
        
        $hadir = Absensi::whereIn('siswa_id', $siswaIds)->where('status', 'hadir')->count();
        return round(($hadir / $totalAbsensi) * 100, 2);
    }

    /**
     * Helper method untuk menghitung kehadiran siswa individu
     */
    private function getStudentAttendance($siswaId)
    {
        $totalAbsensi = Absensi::where('siswa_id', $siswaId)->count();
        if ($totalAbsensi == 0) return 0;
        
        $hadir = Absensi::where('siswa_id', $siswaId)->where('status', 'hadir')->count();
        return round(($hadir / $totalAbsensi) * 100, 2);
    }

    /**
     * Helper method untuk menghitung kehadiran tertinggi di kelas
     */
    private function getHighestAttendance($siswaIds)
    {
        $maxAttendance = 0;
        foreach ($siswaIds as $siswaId) {
            $attendance = $this->getStudentAttendance($siswaId);
            if ($attendance > $maxAttendance) {
                $maxAttendance = $attendance;
            }
        }
        return $maxAttendance;
    }

    /**
     * Helper method untuk menghitung kehadiran terendah di kelas
     */
    private function getLowestAttendance($siswaIds)
    {
        $minAttendance = 100;
        foreach ($siswaIds as $siswaId) {
            $attendance = $this->getStudentAttendance($siswaId);
            if ($attendance < $minAttendance) {
                $minAttendance = $attendance;
            }
        }
        return $minAttendance == 100 ? 0 : $minAttendance;
    }
}
