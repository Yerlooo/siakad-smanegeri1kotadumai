<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Absensi;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AbsensiController extends Controller
{
    /**
     * Display a listing of absensi - Dashboard untuk Guru
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Hanya guru dan kepala tata usaha yang bisa akses
        if (!$user->isGuru() && !$user->isKepalaSekolah()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');

        // Ambil mata pelajaran yang diajar guru ini
        $mataPelajaranQuery = JadwalPelajaran::with(['mataPelajaran', 'kelas', 'guru'])
            ->where('guru_id', $user->id)
            ->where('status', true);

        if ($user->isKepalaSekolah()) {
            $mataPelajaranQuery = JadwalPelajaran::with(['mataPelajaran', 'kelas', 'guru'])
                ->where('status', true);
        }

        $jadwalPelajaran = $mataPelajaranQuery->get();

        // Filter berdasarkan kelas dan mata pelajaran jika dipilih
        if ($kelasId) {
            $jadwalPelajaran = $jadwalPelajaran->where('kelas_id', $kelasId);
        }
        
        if ($mataPelajaranId) {
            $jadwalPelajaran = $jadwalPelajaran->where('mata_pelajaran_id', $mataPelajaranId);
        }

        // Ambil data absensi untuk tanggal yang dipilih
        $absensiData = [];
        foreach ($jadwalPelajaran as $jadwal) {
            $siswaKelas = Siswa::where('kelas_id', $jadwal->kelas_id)->get();
            
            $absensiQuery = Absensi::with(['siswa'])
                ->where('tanggal', $tanggal)
                ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id);
            
            // Untuk guru, filter berdasarkan guru_id. Untuk kepala tata usaha, ambil dari guru yang mengajar
            if (!$user->isKepalaSekolah()) {
                $absensiQuery->where('guru_id', $user->id);
            } else {
                $absensiQuery->where('guru_id', $jadwal->guru_id);
            }
            
            $absensiSiswa = $absensiQuery->get()->keyBy('siswa_id');

            $dataKelas = [
                'jadwal' => $jadwal,
                'siswa' => $siswaKelas->map(function ($siswa) use ($absensiSiswa) {
                    return [
                        'siswa' => $siswa,
                        'absensi' => $absensiSiswa->get($siswa->id)
                    ];
                })
            ];

            $absensiData[] = $dataKelas;
        }

        // Untuk dropdown
        $kelasList = $jadwalPelajaran->pluck('kelas')->unique('id')->values();
        $mataPelajaranList = $jadwalPelajaran->pluck('mataPelajaran')->unique('id')->values();

        return Inertia::render('Absensi/Index', [
            'absensiData' => $absensiData,
            'tanggal' => $tanggal,
            'kelasList' => $kelasList,
            'mataPelajaranList' => $mataPelajaranList,
            'selectedKelas' => $kelasId,
            'selectedMataPelajaran' => $mataPelajaranId,
            'statusOptions' => Absensi::getStatusOptions(),
            'isKepalaSekolah' => $user->isKepalaSekolah()
        ]);
    }

    /**
     * Store absensi data
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Absensi store request received:', $request->all());
            
            $validated = $request->validate([
                'tanggal' => 'required|date',
                'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
                'guru_id' => 'nullable|exists:users,id',
                'absensi' => 'required|array|min:1',
                'absensi.*.siswa_id' => 'required|exists:siswa,id',
                'absensi.*.status' => 'required|in:hadir,sakit,izin,alpha',
                'absensi.*.keterangan' => 'nullable|string|max:255',
                'absensi.*.jam_masuk' => 'nullable|date_format:H:i',
                'absensi.*.jam_keluar' => 'nullable|date_format:H:i'
            ]);

            \Log::info('Absensi validation passed:', $validated);

            $user = auth()->user();
            $tanggal = $validated['tanggal'];
            $mataPelajaranId = $validated['mata_pelajaran_id'];
            $guruId = $validated['guru_id'] ?? $user->id;

            // Validate guru access
            if (!$user->isGuru() && !$user->isKepalaSekolah()) {
                return back()->withErrors(['error' => 'Anda tidak memiliki akses untuk input absensi.']);
            }

            DB::transaction(function () use ($validated, $tanggal, $mataPelajaranId, $guruId) {
                foreach ($validated['absensi'] as $data) {
                    $jamMasuk = null;
                    $jamKeluar = null;

                    // Hanya set jam jika status hadir dan jam diisi
                    if ($data['status'] === 'hadir') {
                        if (!empty($data['jam_masuk'])) {
                            try {
                                $jamMasuk = Carbon::createFromFormat('Y-m-d H:i', $tanggal . ' ' . $data['jam_masuk']);
                            } catch (\Exception $e) {
                                \Log::warning('Invalid jam_masuk format: ' . $data['jam_masuk']);
                            }
                        }

                        if (!empty($data['jam_keluar'])) {
                            try {
                                $jamKeluar = Carbon::createFromFormat('Y-m-d H:i', $tanggal . ' ' . $data['jam_keluar']);
                            } catch (\Exception $e) {
                                \Log::warning('Invalid jam_keluar format: ' . $data['jam_keluar']);
                            }
                        }
                    }

                    // Selalu buat record baru untuk setiap sesi absensi
                    // Tidak menggunakan updateOrCreate agar data tidak tertimpa
                    Absensi::create([
                        'siswa_id' => $data['siswa_id'],
                        'tanggal' => $tanggal,
                        'mata_pelajaran_id' => $mataPelajaranId,
                        'guru_id' => $guruId,
                        'status' => $data['status'],
                        'keterangan' => $data['keterangan'] ?? null,
                        'jam_masuk' => $jamMasuk,
                        'jam_keluar' => $jamKeluar,
                        'semester' => 'ganjil',
                        'tahun_ajaran' => '2024/2025'
                    ]);
                }
            });

            \Log::info('Absensi data saved successfully for ' . count($validated['absensi']) . ' students');
            return back()->with('success', 'Data absensi berhasil disimpan.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Error storing absensi: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Terjadi kesalahan saat menyimpan absensi. Silakan coba lagi.']);
        }
    }

    /**
     * Show rekap absensi per siswa
     */
    public function rekapSiswa(Request $request)
    {
        $user = auth()->user();
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);

        // Filter siswa berdasarkan kelas
        $siswaQuery = Siswa::with(['kelas', 'user']);
        
        if ($kelasId) {
            $siswaQuery->where('kelas_id', $kelasId);
        }

        if (!$user->isKepalaSekolah()) {
            // Guru hanya bisa lihat siswa dari kelas yang diajarnya
            $kelasGuru = JadwalPelajaran::where('guru_id', $user->id)
                ->pluck('kelas_id')
                ->toArray();
            $siswaQuery->whereIn('kelas_id', $kelasGuru);
        }

        $siswaList = $siswaQuery->get();

        // Ambil rekap absensi
        $rekapData = [];
        foreach ($siswaList as $siswa) {
            $query = Absensi::where('siswa_id', $siswa->id)
                ->byBulan($bulan, $tahun);

            if ($mataPelajaranId) {
                $query->where('mata_pelajaran_id', $mataPelajaranId);
            }

            $absensiList = $query->with(['mataPelajaran'])->get();
            
            // Hitung statistik berdasarkan data yang sudah difilter
            $statistik = [
                'total' => $absensiList->count(),
                'hadir' => $absensiList->where('status', 'hadir')->count(),
                'sakit' => $absensiList->where('status', 'sakit')->count(),
                'izin' => $absensiList->where('status', 'izin')->count(),
                'alpha' => $absensiList->where('status', 'alpha')->count(),
            ];
            
            // Hitung persentase kehadiran
            $statistik['persentase_hadir'] = $statistik['total'] > 0 
                ? round(($statistik['hadir'] / $statistik['total']) * 100, 2) 
                : 0;

            $rekapData[] = [
                'siswa' => $siswa,
                'absensi' => $absensiList,
                'statistik' => $statistik
            ];
        }

        // Data untuk dropdown
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();

        // Check if export is requested
        $format = $request->get('format');
        if ($format === 'excel') {
            return $this->exportExcel($rekapData, $bulan, $tahun, $kelasId, $mataPelajaranId);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($rekapData, $bulan, $tahun, $kelasId, $mataPelajaranId);
        }

        return Inertia::render('Absensi/RekapSiswa', [
            'rekapData' => $rekapData,
            'kelasList' => $kelasList,
            'mataPelajaranList' => $mataPelajaranList,
            'selectedKelas' => $kelasId,
            'selectedMataPelajaran' => $mataPelajaranId,
            'bulan' => $bulan,
            'tahun' => $tahun,
            'namaBulan' => Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F'),
            'isKepalaSekolah' => $user->isKepalaSekolah()
        ]);
    }

    /**
     * Show laporan absensi
     */
    public function laporan(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');
        $tanggalMulai = $request->get('tanggal_mulai', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $tanggalSelesai = $request->get('tanggal_selesai', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Query absensi
        $query = Absensi::with(['siswa.kelas', 'mataPelajaran', 'guru'])
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalSelesai]);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if ($mataPelajaranId) {
            $query->where('mata_pelajaran_id', $mataPelajaranId);
        }

        $absensiData = $query->orderBy('tanggal', 'desc')
            ->orderBy('siswa_id')
            ->get();

        // Statistik umum
        $statistikUmum = [
            'total_absensi' => $absensiData->count(),
            'total_hadir' => $absensiData->where('status', 'hadir')->count(),
            'total_sakit' => $absensiData->where('status', 'sakit')->count(),
            'total_izin' => $absensiData->where('status', 'izin')->count(),
            'total_alpha' => $absensiData->where('status', 'alpha')->count(),
        ];

        if ($statistikUmum['total_absensi'] > 0) {
            $statistikUmum['persentase_hadir'] = round(
                ($statistikUmum['total_hadir'] / $statistikUmum['total_absensi']) * 100, 
                2
            );
        } else {
            $statistikUmum['persentase_hadir'] = 0;
        }

        // Data untuk dropdown
        $kelasList = Kelas::all();
        $mataPelajaranList = MataPelajaran::all();

        return Inertia::render('Absensi/Laporan', [
            'absensiData' => $absensiData,
            'statistikUmum' => $statistikUmum,
            'kelasList' => $kelasList,
            'mataPelajaranList' => $mataPelajaranList,
            'selectedKelas' => $kelasId,
            'selectedMataPelajaran' => $mataPelajaranId,
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
        ]);
    }

    /**
     * Delete absensi record
     */
    public function destroy($id)
    {
        $user = auth()->user();
        
        $absensi = Absensi::findOrFail($id);
        
        // Hanya guru yang input atau kepala tata usaha yang bisa hapus
        if ($absensi->guru_id !== $user->id && !$user->isKepalaSekolah()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        $absensi->delete();

        return redirect()->back()->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Export rekap absensi ke Excel
     */
    private function exportExcel($rekapData, $bulan, $tahun, $kelasId = null, $mataPelajaranId = null)
    {
        $fileName = 'rekap-absensi-' . Carbon::create($tahun, $bulan, 1)->format('Y-m') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\""
        ];

        return response()->stream(function () use ($rekapData, $bulan, $tahun) {
            $handle = fopen('php://output', 'w');
            
            // Header info
            fputcsv($handle, ['REKAP ABSENSI SISWA']);
            fputcsv($handle, ['Periode: ' . Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y')]);
            fputcsv($handle, ['Dicetak: ' . Carbon::now()->locale('id')->translatedFormat('d F Y H:i:s')]);
            fputcsv($handle, []); // Empty row
            
            // Header kolom
            fputcsv($handle, [
                'No',
                'Nama Siswa',
                'Kelas', 
                'NISN',
                'Hadir',
                'Sakit',
                'Izin',
                'Alpha',
                'Total',
                'Persentase Hadir (%)'
            ]);
            
            // Data rows
            foreach ($rekapData as $index => $data) {
                fputcsv($handle, [
                    $index + 1,
                    $data['siswa']['nama_lengkap'],
                    $data['siswa']['kelas']['nama_kelas'] ?? 'N/A',
                    $data['siswa']['nisn'],
                    $data['statistik']['hadir'],
                    $data['statistik']['sakit'], 
                    $data['statistik']['izin'],
                    $data['statistik']['alpha'],
                    $data['statistik']['total'],
                    $data['statistik']['persentase_hadir']
                ]);
            }
            
            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export rekap absensi ke PDF
     */
    private function exportPdf($rekapData, $bulan, $tahun, $kelasId = null, $mataPelajaranId = null)
    {
        $namaBulan = Carbon::create($tahun, $bulan, 1)->locale('id')->translatedFormat('F Y');
        $tanggalCetak = Carbon::now()->locale('id')->translatedFormat('d F Y H:i:s');
        
        // Get guru yang sedang login
        $currentUser = auth()->user();
        
        // Get informasi mata pelajaran dan kelas dari data absensi
        $mataPelajaranInfo = collect();
        $kelasInfo = collect();
        $guruInfo = collect();
        
        // Analisis data absensi untuk mendapatkan informasi terkait
        foreach ($rekapData as $data) {
            foreach ($data['absensi'] as $absensi) {
                // Collect mata pelajaran
                if (isset($absensi['mata_pelajaran'])) {
                    $mataPelajaranInfo->push($absensi['mata_pelajaran']);
                }
                
                // Collect kelas dari siswa
                if (isset($data['siswa']['kelas'])) {
                    $kelasInfo->push($data['siswa']['kelas']);
                }
                
                // Get guru info from jadwal pelajaran if available
                if (isset($absensi['mata_pelajaran']['id']) && isset($data['siswa']['kelas']['id'])) {
                    $jadwal = JadwalPelajaran::where('mata_pelajaran_id', $absensi['mata_pelajaran']['id'])
                                           ->where('kelas_id', $data['siswa']['kelas']['id'])
                                           ->with('guru')
                                           ->first();
                    if ($jadwal && $jadwal->guru) {
                        $guruInfo->push($jadwal->guru);
                    }
                }
            }
        }
        
        // Get unique mata pelajaran and kelas
        $uniqueMataPelajaran = $mataPelajaranInfo->unique('id');
        $uniqueKelas = $kelasInfo->unique('id');
        $uniqueGuru = $guruInfo->unique('id');
        
        // For specific filter, get detailed info
        $selectedMataPelajaran = null;
        $selectedKelas = null;
        $selectedGuru = null;
        
        if ($mataPelajaranId) {
            $selectedMataPelajaran = MataPelajaran::find($mataPelajaranId);
        }
        
        if ($kelasId) {
            $selectedKelas = Kelas::find($kelasId);
        }
        
        // If both mata pelajaran and kelas selected, get specific guru
        if ($selectedMataPelajaran && $selectedKelas) {
            $jadwal = JadwalPelajaran::where('mata_pelajaran_id', $mataPelajaranId)
                                   ->where('kelas_id', $kelasId)
                                   ->with('guru')
                                   ->first();
            if ($jadwal && $jadwal->guru) {
                $selectedGuru = $jadwal->guru;
            }
        }
        
        // Buat HTML content untuk PDF
        $html = view('exports.rekap-absensi-pdf', [
            'rekapData' => $rekapData,
            'namaBulan' => $namaBulan,
            'tanggalCetak' => $tanggalCetak,
            'currentUser' => $currentUser,
            'selectedMataPelajaran' => $selectedMataPelajaran,
            'selectedKelas' => $selectedKelas,
            'selectedGuru' => $selectedGuru,
            'uniqueMataPelajaran' => $uniqueMataPelajaran,
            'uniqueKelas' => $uniqueKelas,
            'uniqueGuru' => $uniqueGuru
        ])->render();

        // Generate PDF menggunakan DomPDF atau library lain
        // Untuk sementara kita gunakan response HTML yang bisa di-print
        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'inline; filename="rekap-absensi-' . Carbon::create($tahun, $bulan, 1)->format('Y-m') . '.html"');
    }
}
