<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Siswa;
use App\Models\User;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\NilaiSiswa;
use App\Models\Absensi;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100'
        ]);

        $query = trim($request->query('query')); // Menggunakan query() untuk query parameter GET request
        $user = Auth::user();
        $results = [];

        // Check if user has role
        if (!$user || !$user->role) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak memiliki akses.',
                'results' => []
            ], 403);
        }

        try {
            // Search Siswa
            if (in_array($user->role->name, ['kepala_tatausaha', 'tata_usaha', 'guru'])) {
                $siswaResults = Siswa::with(['kelas', 'user'])
                    ->where(function($q) use ($query) {
                        $q->where('nama_lengkap', 'LIKE', "%{$query}%")
                          ->orWhere('nis', 'LIKE', "%{$query}%")
                          ->orWhere('nisn', 'LIKE', "%{$query}%");
                    })
                    ->limit(5)
                    ->get()
                    ->map(function($siswa) {
                        return [
                            'type' => 'siswa',
                            'title' => $siswa->nama_lengkap,
                            'subtitle' => "NIS: {$siswa->nis}" . ($siswa->kelas ? " • Kelas: {$siswa->kelas->nama_kelas}" : ""),
                            'url' => route('siswa.show', $siswa->id)
                        ];
                    });
                
                $results = array_merge($results, $siswaResults->toArray());
            }

            // Search Guru
            if (in_array($user->role->name, ['kepala_tatausaha', 'tata_usaha', 'guru'])) {
                $guruResults = User::whereHas('role', function($q) {
                        $q->where('name', 'guru');
                    })
                    ->where(function($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%");
                    })
                    ->limit(5)
                    ->get()
                    ->map(function($guru) {
                        return [
                            'type' => 'guru',
                            'title' => $guru->name,
                            'subtitle' => "Email: {$guru->email}",
                            'url' => route('guru.show', $guru->id)
                        ];
                    });
                
                $results = array_merge($results, $guruResults->toArray());
            }

            // Search Kelas
            $kelasResults = Kelas::with(['waliKelas'])
                ->where('nama_kelas', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($kelas) {
                    return [
                        'type' => 'kelas',
                        'title' => $kelas->nama_kelas,
                        'subtitle' => $kelas->waliKelas ? "Wali Kelas: {$kelas->waliKelas->name}" : "Belum ada wali kelas",
                        'url' => route('kelas.show', $kelas->id)
                    ];
                });
            
            $results = array_merge($results, $kelasResults->toArray());

            // Search Mata Pelajaran
            if (in_array($user->role->name, ['kepala_tatausaha', 'tata_usaha', 'guru'])) {
                $mataPelajaranResults = MataPelajaran::where('nama_mapel', 'LIKE', "%{$query}%")
                    ->limit(5)
                    ->get()
                    ->map(function($mapel) {
                        return [
                            'type' => 'mata_pelajaran',
                            'title' => $mapel->nama_mapel,
                            'subtitle' => "Kode: {$mapel->kode_mapel}",
                            'url' => route('mata-pelajaran.show', $mapel->id)
                        ];
                    });
                
                $results = array_merge($results, $mataPelajaranResults->toArray());
            }

            // Search Jadwal Pelajaran
            $jadwalResults = JadwalPelajaran::with(['mataPelajaran', 'kelas', 'guru'])
                ->whereHas('mataPelajaran', function($q) use ($query) {
                    $q->where('nama_mapel', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('kelas', function($q) use ($query) {
                    $q->where('nama_kelas', 'LIKE', "%{$query}%");
                })
                ->orWhereHas('guru', function($q) use ($query) {
                    $q->where('name', 'LIKE', "%{$query}%");
                })
                ->orWhere('hari', 'LIKE', "%{$query}%")
                ->limit(5)
                ->get()
                ->map(function($jadwal) {
                    return [
                        'type' => 'jadwal',
                        'title' => "{$jadwal->mataPelajaran->nama_mapel} - {$jadwal->kelas->nama_kelas}",
                        'subtitle' => "{$jadwal->hari}, {$jadwal->jam_mulai} - {$jadwal->jam_selesai} • {$jadwal->guru->name}",
                        'url' => route('jadwal-pelajaran.show', $jadwal->id)
                    ];
                });
            
            $results = array_merge($results, $jadwalResults->toArray());

            return response()->json([
                'success' => true,
                'results' => array_slice($results, 0, 20), // Limit total results to 20
                'total' => count($results)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mencari data.',
                'results' => []
            ], 500);
        }
    }
}
