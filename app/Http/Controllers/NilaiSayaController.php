<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\NilaiSiswa;
use App\Models\Siswa;
use App\Models\Setting;
use App\Models\MataPelajaran;
use App\Models\JenisNilai;
use App\Models\KkmMataPelajaran;
use Illuminate\Support\Facades\DB;

class NilaiSayaController extends Controller
{
    /**
     * Tampilkan nilai siswa yang sedang login
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Pastikan yang mengakses adalah murid
        if (!$user->isMurid()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya murid yang dapat mengakses halaman ini.');
        }

        // Ambil data siswa berdasarkan user yang login
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data murid tidak ditemukan.');
        }

        // Ambil tahun ajaran dan semester aktif dari settings
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));

        // Filter semester dan tahun ajaran jika ada request
        $selectedSemester = $request->get('semester', $semester);
        $selectedTahunAjaran = $request->get('tahun_ajaran', $tahunAjaran);

        // Ambil semua nilai siswa berdasarkan semester dan tahun ajaran
        $nilaiSiswa = NilaiSiswa::with([
                'mataPelajaran',
                'jenisNilai', 
                'guru'
            ])
            ->where('siswa_id', $siswa->id)
            ->where('semester', $selectedSemester)
            ->where('tahun_ajaran', $selectedTahunAjaran)
            ->where('status', 'final') // Hanya tampilkan nilai yang sudah final
            ->orderBy('mata_pelajaran_id')
            ->orderBy('jenis_nilai_id')
            ->get();

        // Grup nilai berdasarkan mata pelajaran
        $nilaiPerMapel = $nilaiSiswa->groupBy('mata_pelajaran_id');

        // Ambil semua jenis nilai untuk header tabel
        $jenisNilai = JenisNilai::aktif()->orderBy('nama')->get();

        // Ambil KKM untuk setiap mata pelajaran
        $kkmData = KkmMataPelajaran::where('kelas_id', $siswa->kelas_id)
            ->where('semester', $selectedSemester)
            ->where('tahun_ajaran', $selectedTahunAjaran)
            ->get()
            ->keyBy('mata_pelajaran_id');

        // Hitung nilai akhir per mata pelajaran
        $nilaiAkhir = [];
        foreach ($nilaiPerMapel as $mataPelajaranId => $nilaiList) {
            $totalBobot = 0;
            $nilaiTerbobot = 0;
            
            foreach ($nilaiList as $nilai) {
                $bobot = $nilai->jenisNilai->bobot ?? 0; // Handle null bobot
                $totalBobot += $bobot;
                $nilaiTerbobot += ($nilai->nilai * $bobot / 100);
            }
            
            // Hanya hitung nilai akhir jika ada bobot yang valid
            if ($totalBobot > 0) {
                $nilaiAkhir[$mataPelajaranId] = round($nilaiTerbobot, 2);
            }
        }

        // Hitung statistik menggunakan method helper
        $statistik = $this->hitungStatistik($nilaiAkhir, $nilaiPerMapel->count());

        // Debug logging untuk development
        if (config('app.debug')) {
            \Log::info('NilaiSaya Debug - Siswa: ' . $siswa->nama_lengkap, [
                'nilai_akhir_count' => count($nilaiAkhir),
                'nilai_akhir' => $nilaiAkhir,
                'statistik' => $statistik
            ]);
        }

        return Inertia::render('Nilai/NilaiSaya', [
            'siswa' => $siswa,
            'nilaiPerMapel' => $nilaiPerMapel,
            'jenisNilaiAktif' => $jenisNilai,
            'nilaiAkhir' => $nilaiAkhir,
            'statistik' => $statistik,
            'semester' => $selectedSemester,
            'tahunAjaran' => $selectedTahunAjaran
        ]);
    }

    /**
     * Get predikat berdasarkan nilai dan KKM
     */
    private function getPredikat($nilai, $kkm)
    {
        if ($nilai >= 90) return ['huruf' => 'A', 'predikat' => 'Sangat Baik'];
        if ($nilai >= 80) return ['huruf' => 'B+', 'predikat' => 'Baik'];
        if ($nilai >= $kkm) return ['huruf' => 'B', 'predikat' => 'Baik'];
        if ($nilai >= $kkm - 10) return ['huruf' => 'C+', 'predikat' => 'Cukup'];
        if ($nilai >= $kkm - 20) return ['huruf' => 'C', 'predikat' => 'Cukup'];
        return ['huruf' => 'D', 'predikat' => 'Kurang'];
    }

    /**
     * Export nilai siswa ke PDF
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isMurid()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data siswa tidak ditemukan.');
        }

        // Ambil data yang sama seperti di index
        $tahunAjaran = $request->get('tahun_ajaran', Setting::get('academic_year', '2024/2025'));
        $semester = $request->get('semester', Setting::get('academic_semester', 'ganjil'));

        // ... (implementasi export PDF, bisa ditambahkan nanti)
        
        return response()->json(['message' => 'Export PDF belum diimplementasikan']);
    }

    /**
     * Helper method untuk menghitung statistik nilai
     */
    private function hitungStatistik($nilaiAkhir, $totalMapel)
    {
        // Validasi input
        if (empty($nilaiAkhir) || !is_array($nilaiAkhir)) {
            return [
                'total_mapel' => $totalMapel,
                'rata_rata' => 0,
                'nilai_tertinggi' => 0,
                'nilai_terendah' => 0,
            ];
        }

        // Filter nilai yang valid (numeric)
        $nilaiValid = array_filter($nilaiAkhir, function($nilai) {
            return is_numeric($nilai) && $nilai >= 0;
        });

        // Jika tidak ada nilai valid
        if (empty($nilaiValid)) {
            return [
                'total_mapel' => $totalMapel,
                'rata_rata' => 0,
                'nilai_tertinggi' => 0,
                'nilai_terendah' => 0,
            ];
        }

        // Hitung statistik
        $rataRata = round(array_sum($nilaiValid) / count($nilaiValid), 2);
        $nilaiTertinggi = max($nilaiValid);
        $nilaiTerendah = min($nilaiValid);

        return [
            'total_mapel' => $totalMapel,
            'rata_rata' => $rataRata,
            'nilai_tertinggi' => $nilaiTertinggi,
            'nilai_terendah' => $nilaiTerendah,
        ];
    }
}
