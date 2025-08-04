<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\NilaiSiswa;
use App\Models\Absensi;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Dapatkan statistik berdasarkan role
        $stats = $this->getStatsForRole($user);
        
        // Dapatkan aktivitas terbaru berdasarkan role
        $recentActivities = $this->getRecentActivities($user);
        
        // Dapatkan chart data berdasarkan role
        $chartData = $this->getChartDataForRole($user);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'chartData' => $chartData,
            'user' => $user->load('role'),
            'roleSpecific' => $this->getRoleSpecificData($user),
        ]);
    }
    
    private function getStatsForRole($user)
    {
        $stats = [];
        
        switch($user->role->name) {
            case 'kepala_tatausaha':
            case 'tata_usaha':
                $stats = [
                    'total_guru' => User::whereHas('role', function($q) {
                        $q->where('name', 'guru');
                    })->count(),
                    'total_siswa' => Siswa::where('status', 'aktif')->count(),
                    'total_kelas' => Kelas::count(),
                    'total_mata_pelajaran' => MataPelajaran::count(),
                    'siswa_laki' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'laki-laki')->count(),
                    'siswa_perempuan' => Siswa::where('status', 'aktif')->where('jenis_kelamin', 'perempuan')->count(),
                    'kelas_aktif' => Kelas::count(), // Menghapus filter status karena kolom tidak ada
                    'rata_rata_nilai' => round(NilaiSiswa::avg('nilai') ?? 0, 2),
                ];
                break;
                
            case 'guru':
                // Ambil kelas berdasarkan jadwal mengajar guru
                $jadwalGuru = JadwalPelajaran::where('guru_id', $user->id)->with('kelas')->get();
                $kelasIdsFromJadwal = $jadwalGuru->pluck('kelas_id')->unique();
                
                // Ambil kelas yang diampu sebagai wali kelas
                $kelasAsWali = Kelas::where('wali_kelas_id', $user->id)->get();
                
                // Gabungkan semua kelas (jadwal + wali kelas)
                $allKelasIds = $kelasIdsFromJadwal->merge($kelasAsWali->pluck('id'))->unique();
                
                // Hitung total siswa dari semua kelas yang diampu
                $totalSiswaGuru = Siswa::whereIn('kelas_id', $allKelasIds)->count();
                
                $stats = [
                    'kelas_diampu' => $allKelasIds->count(),
                    'total_siswa_diampu' => $totalSiswaGuru,
                    'jadwal_minggu_ini' => $jadwalGuru->count(),
                    'tugas_belum_dinilai' => NilaiSiswa::whereIn('siswa_id', function($query) use ($allKelasIds) {
                        $query->select('id')
                              ->from('siswa')
                              ->whereIn('kelas_id', $allKelasIds);
                    })->whereNull('nilai')->count(),
                    'kehadiran_hari_ini' => $this->getKehadiranHariIni($user, $allKelasIds),
                    'rata_rata_kelas' => $this->getRataRataKelas($user, $allKelasIds),
                ];
                break;
                
            case 'siswa':
            case 'murid':
                $siswa = $user->siswa;
                if ($siswa) {
                    $stats = [
                        'kelas' => $siswa->kelas->nama_kelas ?? 'Belum ada kelas',
                        'wali_kelas' => $siswa->kelas->waliKelas->name ?? 'Belum ditentukan',
                        'mata_pelajaran' => MataPelajaran::count(),
                        'nilai_tertinggi' => NilaiSiswa::where('siswa_id', $siswa->id)->max('nilai') ?? 0,
                        'nilai_terendah' => NilaiSiswa::where('siswa_id', $siswa->id)->min('nilai') ?? 0,
                        'rata_rata_nilai' => round(NilaiSiswa::where('siswa_id', $siswa->id)->avg('nilai') ?? 0, 2),
                        'total_absensi' => Absensi::where('siswa_id', $siswa->id)->count(),
                        'ranking_kelas' => $this->getRankingKelas($siswa),
                    ];
                } else {
                    $stats = [
                        'kelas' => 'Belum ada kelas',
                        'wali_kelas' => 'Belum ditentukan',
                        'mata_pelajaran' => MataPelajaran::count(),
                        'nilai_tertinggi' => 0,
                        'nilai_terendah' => 0,
                        'rata_rata_nilai' => 0,
                        'total_absensi' => 0,
                        'ranking_kelas' => null,
                    ];
                }
                break;
                
            default:
                $stats = [
                    'total_pengguna' => User::count(),
                    'siswa_aktif' => Siswa::where('status', 'aktif')->count(),
                    'kelas_tersedia' => Kelas::count(),
                    'mata_pelajaran' => MataPelajaran::count(),
                ];
        }
        
        return $stats;
    }

    private function getRecentActivities($user)
    {
        $activities = [];
        
        switch($user->role->name) {
            case 'kepala_tatausaha':
            case 'tata_usaha':
                // Aktivitas untuk admin/kepala sekolah
                $recentUsers = User::latest()->take(3)->get();
                foreach ($recentUsers as $recentUser) {
                    $activities[] = [
                        'id' => 'user-' . $recentUser->id,
                        'type' => 'user',
                        'title' => "Pengguna baru: {$recentUser->name}",
                        'description' => "Role: {$recentUser->role->display_name}",
                        'time' => $recentUser->created_at,
                    ];
                }
                
                // Siswa baru
                $recentSiswa = Siswa::with('kelas')->latest()->take(2)->get();
                foreach ($recentSiswa as $siswa) {
                    $activities[] = [
                        'id' => 'siswa-' . $siswa->id,
                        'type' => 'siswa',
                        'title' => "Siswa baru: {$siswa->nama}",
                        'description' => "NIS: {$siswa->nis} - Kelas: " . ($siswa->kelas->nama_kelas ?? 'Belum ada kelas'),
                        'time' => $siswa->created_at,
                    ];
                }
                break;
                
            case 'guru':
                // Ambil semua kelas yang diampu guru
                $jadwalGuru = JadwalPelajaran::where('guru_id', $user->id)->with('kelas')->get();
                $kelasIdsFromJadwal = $jadwalGuru->pluck('kelas_id')->unique();
                $kelasAsWali = Kelas::where('wali_kelas_id', $user->id)->get();
                $allKelasIds = $kelasIdsFromJadwal->merge($kelasAsWali->pluck('id'))->unique();
                
                // Jadwal hari ini untuk guru
                $todaySchedule = JadwalPelajaran::with(['mataPelajaran', 'kelas'])
                    ->where('guru_id', $user->id)
                    ->where('hari', now()->locale('id')->dayName)
                    ->orderBy('jam_mulai')
                    ->take(5)
                    ->get();
                    
                foreach ($todaySchedule as $schedule) {
                    $activities[] = [
                        'id' => 'schedule-' . $schedule->id,
                        'type' => 'schedule',
                        'title' => "Jadwal Mengajar: {$schedule->mataPelajaran->nama_mapel}",
                        'description' => "Kelas {$schedule->kelas->nama_kelas} - {$schedule->jam_mulai} s/d {$schedule->jam_selesai}",
                        'time' => today()->setTimeFromTimeString($schedule->jam_mulai),
                    ];
                }
                
                // Nilai yang perlu diinput dari semua kelas yang diampu
                $nilaiPending = NilaiSiswa::whereIn('siswa_id', function($query) use ($allKelasIds) {
                    $query->select('id')
                          ->from('siswa')
                          ->whereIn('kelas_id', $allKelasIds);
                })->whereNull('nilai')->with(['siswa', 'jenisNilai'])->take(3)->get();
                
                foreach ($nilaiPending as $nilai) {
                    $activities[] = [
                        'id' => 'nilai-' . $nilai->id,
                        'type' => 'nilai',
                        'title' => "Nilai belum diinput",
                        'description' => "Siswa: " . ($nilai->siswa->nama ?? 'Unknown') . " - " . ($nilai->jenisNilai->nama ?? 'Nilai'),
                        'time' => $nilai->created_at,
                    ];
                }
                break;
                
            case 'siswa':
            case 'murid':
                $siswa = $user->siswa;
                if ($siswa) {
                    // Jadwal hari ini untuk siswa
                    $todaySchedule = JadwalPelajaran::with(['mataPelajaran', 'guru'])
                        ->whereHas('kelas', function($query) use ($siswa) {
                            $query->where('id', $siswa->kelas_id);
                        })
                        ->where('hari', now()->locale('id')->dayName)
                        ->orderBy('jam_mulai')
                        ->take(5)
                        ->get();
                        
                    foreach ($todaySchedule as $schedule) {
                        $activities[] = [
                            'id' => 'schedule-' . $schedule->id,
                            'type' => 'schedule',
                            'title' => "Pelajaran: {$schedule->mataPelajaran->nama_mapel}",
                            'description' => "Guru: {$schedule->guru->name} - {$schedule->jam_mulai} s/d {$schedule->jam_selesai}",
                            'time' => today()->setTimeFromTimeString($schedule->jam_mulai),
                        ];
                    }
                    
                                        // Nilai terbaru
                    $recentNilai = NilaiSiswa::where('siswa_id', $siswa->id)
                        ->whereNotNull('nilai')
                        ->with(['jenisNilai', 'mataPelajaran'])
                        ->latest()
                        ->take(3)
                        ->get();
                        
                    foreach ($recentNilai as $nilai) {
                        $activities[] = [
                            'id' => 'nilai-' . $nilai->id,
                            'type' => 'nilai',
                            'title' => "Nilai baru: " . ($nilai->jenisNilai->nama ?? 'Nilai'),
                            'description' => "Nilai: {$nilai->nilai} - " . ($nilai->mataPelajaran->nama_mapel ?? 'Mata Pelajaran'),
                            'time' => $nilai->updated_at,
                        ];
                    }
                }
                break;
        }
        
        return collect($activities)->sortByDesc('time')->values();
    }
    
    private function getChartDataForRole($user)
    {
        $chartData = [];
        
        switch($user->role->name) {
            case 'kepala_tatausaha':
            case 'tata_usaha':
                // Chart untuk admin
                $chartData['siswaPerKelas'] = Kelas::withCount('siswa')->get()->map(function($kelas) {
                    return [
                        'name' => $kelas->nama_kelas,
                        'value' => $kelas->siswa_count
                    ];
                });
                
                $chartData['nilaiRataRata'] = MataPelajaran::with('nilaiSiswa')->get()->map(function($mapel) {
                    $avgNilai = $mapel->nilaiSiswa->whereNotNull('nilai')->avg('nilai');
                    return [
                        'name' => $mapel->nama_mapel,
                        'value' => round($avgNilai ?? 0, 2)
                    ];
                })->filter(function($item) {
                    return $item['value'] > 0; // Filter out subjects with no grades
                });
                break;
                
            case 'guru':
                // Chart untuk guru - ambil semua kelas yang diampu
                $jadwalGuru = JadwalPelajaran::where('guru_id', $user->id)->with('kelas')->get();
                $kelasIdsFromJadwal = $jadwalGuru->pluck('kelas_id')->unique();
                
                $kelasAsWali = Kelas::where('wali_kelas_id', $user->id)->get();
                $allKelasIds = $kelasIdsFromJadwal->merge($kelasAsWali->pluck('id'))->unique();
                
                $chartData['siswaKelas'] = Kelas::whereIn('id', $allKelasIds)
                    ->withCount('siswa')
                    ->get()
                    ->map(function($kelas) {
                        return [
                            'name' => $kelas->nama_kelas,
                            'value' => $kelas->siswa_count
                        ];
                    });
                break;
                
            case 'siswa':
            case 'murid':
                $siswa = $user->siswa;
                if ($siswa) {
                    // Chart nilai siswa per mata pelajaran
                    $chartData['nilaiSiswa'] = NilaiSiswa::where('siswa_id', $siswa->id)
                        ->whereNotNull('nilai')
                        ->with('mataPelajaran')
                        ->get()
                        ->groupBy('mata_pelajaran_id')
                        ->map(function($nilaiGroup, $mapelId) {
                            $mapel = $nilaiGroup->first()->mataPelajaran;
                            return [
                                'name' => $mapel->nama_mapel ?? 'Unknown',
                                'value' => round($nilaiGroup->avg('nilai'), 2)
                            ];
                        })->values();
                }
                break;
        }
        
        return $chartData;
    }
    
    private function getRoleSpecificData($user)
    {
        $data = [];
        
        switch($user->role->name) {
            case 'kepala_tatausaha':
            case 'tata_usaha':
                $data['quickActions'] = [
                    ['title' => 'Kelola Siswa', 'icon' => '👨‍🎓', 'route' => 'siswa.index'],
                    ['title' => 'Kelola Guru', 'icon' => '👨‍🏫', 'route' => 'guru.index'],
                    ['title' => 'Kelola Kelas', 'icon' => '🏫', 'route' => 'kelas.index'],
                    ['title' => 'Jadwal Pelajaran', 'icon' => '📅', 'route' => 'jadwal-pelajaran.index'],
                    ['title' => 'Mata Pelajaran', 'icon' => '📚', 'route' => 'mata-pelajaran.index'],
                    ['title' => 'Wali Kelas', 'icon' => '�', 'route' => 'wali-kelas.index'],
                ];
                break;
                
            case 'guru':
                $data['quickActions'] = [
                    ['title' => 'Jadwal Mengajar', 'icon' => '📅', 'route' => 'jadwal-pelajaran.index'],
                    ['title' => 'Input Nilai', 'icon' => '📝', 'route' => 'nilai-siswa.index'],
                    ['title' => 'Absensi', 'icon' => '✅', 'route' => 'absensi.index'],
                    ['title' => 'Data Siswa', 'icon' => '👨‍🎓', 'route' => 'siswa.index'],
                ];
                break;
                
            case 'siswa':
            case 'murid':
                $data['quickActions'] = [
                    ['title' => 'Lihat Nilai', 'icon' => '📊', 'route' => 'nilai-saya.index'],
                    ['title' => 'Jadwal Pelajaran', 'icon' => '📅', 'route' => 'jadwal-pelajaran.index'],
                    ['title' => 'Absensi', 'icon' => '✅', 'route' => 'absensi.index'],
                    ['title' => 'Notifikasi', 'icon' => '�', 'route' => 'notifications.index'],
                ];
                break;
        }
        
        return $data;
    }
    
    private function getKehadiranHariIni($user, $kelasIds = null)
    {
        if ($kelasIds) {
            // Untuk guru dengan multiple kelas
            return Absensi::whereHas('siswa', function($query) use ($kelasIds) {
                $query->whereIn('kelas_id', $kelasIds);
            })
            ->whereDate('tanggal', today())
            ->where('status', 'hadir')
            ->count();
        } else {
            // Untuk wali kelas saja
            return Absensi::whereHas('siswa.kelas', function($query) use ($user) {
                $query->where('wali_kelas_id', $user->id);
            })
            ->whereDate('tanggal', today())
            ->where('status', 'hadir')
            ->count();
        }
    }
    
    private function getRataRataKelas($user, $kelasIds = null)
    {
        if ($kelasIds) {
            // Untuk guru dengan multiple kelas
            return round(NilaiSiswa::whereHas('siswa', function($query) use ($kelasIds) {
                $query->whereIn('kelas_id', $kelasIds);
            })->avg('nilai') ?? 0, 2);
        } else {
            // Untuk wali kelas saja
            return round(NilaiSiswa::whereHas('siswa.kelas', function($query) use ($user) {
                $query->where('wali_kelas_id', $user->id);
            })->avg('nilai') ?? 0, 2);
        }
    }
    
    private function getRankingKelas($siswa)
    {
        if (!$siswa->kelas_id) return null;
        
        $siswaKelas = Siswa::where('kelas_id', $siswa->kelas_id)
            ->with('nilaiSiswa')
            ->get()
            ->map(function($s) {
                return [
                    'id' => $s->id,
                    'nama' => $s->nama,
                    'rata_rata' => $s->nilaiSiswa->avg('nilai') ?? 0
                ];
            })
            ->sortByDesc('rata_rata')
            ->values();
            
        $ranking = $siswaKelas->search(function($s) use ($siswa) {
            return $s['id'] == $siswa->id;
        });
        
        return $ranking !== false ? $ranking + 1 : null;
    }
}
