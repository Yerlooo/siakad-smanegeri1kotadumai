<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $stats = [
            'total_guru' => User::whereHas('role', function($q) {
                $q->whereIn('name', ['guru', 'kepala_tatausaha', 'tata_usaha']);
            })->count(),
            'total_siswa' => Siswa::where('status', 'aktif')->count(),
            'total_kelas' => Kelas::count(),
            'total_mata_pelajaran' => MataPelajaran::count(),
        ];

        // Get recent activities based on user role
        $recentActivities = $this->getRecentActivities($user);

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentActivities' => $recentActivities,
            'user' => $user->load('role'),
        ]);
    }

    private function getRecentActivities($user)
    {
        $activities = [];
        if ($user->isGuru() || $user->isKepalaSekolah()) {
            // Get today's schedule for teacher
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
                    'time' => $schedule->jam_mulai,
                ];
            }
        }
        return collect($activities)->sortBy('time')->values();
    }
}
