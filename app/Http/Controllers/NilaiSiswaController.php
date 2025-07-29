<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\NilaiSiswa;
use App\Models\JenisNilai;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\KkmMataPelajaran;
use App\Models\JadwalPelajaran;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Exports\NilaiSiswaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class NilaiSiswaController extends Controller
{
    /**
     * Display a listing of the resource - Dashboard Input Nilai Guru
     */
    public function index()
    {
        $user = auth()->user();
        
        // Jika bukan guru, redirect ke dashboard
        if (!$user->isGuru() && !$user->isKepalaSekolah()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya guru yang dapat mengakses halaman ini.');
        }

        // Ambil tahun ajaran dan semester aktif dari settings
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));

        // Ambil mata pelajaran yang diajar guru ini
        $mataPelajaranGuru = JadwalPelajaran::with(['mataPelajaran', 'kelas'])
            ->where('guru_id', $user->id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', true)
            ->get()
            ->groupBy('mata_pelajaran_id');

        // Statistik progress input nilai
        $progressNilai = [];
        $jenisNilaiAktif = JenisNilai::aktif()->get();
        $totalJenisNilai = $jenisNilaiAktif->count();
        
        foreach ($mataPelajaranGuru as $mataPelajaranId => $jadwalList) {
            $mataPelajaran = $jadwalList->first()->mataPelajaran;
            $totalKelas = $jadwalList->count();
            
            $progressKelas = [];
            foreach ($jadwalList as $jadwal) {
                $totalSiswa = $jadwal->kelas->siswa()->count();
                
                if ($totalSiswa > 0 && $totalJenisNilai > 0) {
                    // Hitung progress berdasarkan kelengkapan jenis nilai
                    $siswaIds = $jadwal->kelas->siswa->pluck('id');
                    
                    // Hitung berapa banyak siswa yang sudah memiliki nilai untuk setiap jenis
                    $progressCount = 0;
                    
                    foreach ($siswaIds as $siswaId) {
                        $jenisNilaiTerisi = NilaiSiswa::where('mata_pelajaran_id', $mataPelajaranId)
                            ->where('siswa_id', $siswaId)
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahunAjaran)
                            ->whereIn('jenis_nilai_id', $jenisNilaiAktif->pluck('id'))
                            ->distinct('jenis_nilai_id')
                            ->count();
                        
                        // Progress per siswa = jenis nilai terisi / total jenis nilai
                        $progressCount += ($jenisNilaiTerisi / $totalJenisNilai);
                    }
                    
                    $progressPersen = round(($progressCount / $totalSiswa) * 100);
                    
                    // Hitung siswa yang sudah lengkap semua jenis nilainya
                    $siswaLengkap = 0;
                    foreach ($siswaIds as $siswaId) {
                        $jenisNilaiTerisi = NilaiSiswa::where('mata_pelajaran_id', $mataPelajaranId)
                            ->where('siswa_id', $siswaId)
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahunAjaran)
                            ->whereIn('jenis_nilai_id', $jenisNilaiAktif->pluck('id'))
                            ->distinct('jenis_nilai_id')
                            ->count();
                        
                        if ($jenisNilaiTerisi == $totalJenisNilai) {
                            $siswaLengkap++;
                        }
                    }
                    
                    // Tambahkan informasi status untuk setiap jenis nilai
                    $statusJenisNilai = [];
                    foreach ($jenisNilaiAktif as $jenisNilai) {
                        $nilaiStatus = NilaiSiswa::where('mata_pelajaran_id', $mataPelajaranId)
                            ->where('jenis_nilai_id', $jenisNilai->id)
                            ->where('semester', $semester)
                            ->where('tahun_ajaran', $tahunAjaran)
                            ->whereIn('siswa_id', $siswaIds)
                            ->get();
                        
                        $totalNilai = $nilaiStatus->count();
                        $finalCount = $nilaiStatus->where('status', 'final')->count();
                        $draftCount = $nilaiStatus->where('status', 'draft')->count();
                        
                        $statusJenisNilai[] = [
                            'jenis_nilai_id' => $jenisNilai->id,
                            'jenis_nilai_nama' => $jenisNilai->nama,
                            'total_siswa' => $totalSiswa,
                            'total_dinilai' => $totalNilai,
                            'final_count' => $finalCount,
                            'draft_count' => $draftCount,
                            'belum_dinilai' => $totalSiswa - $totalNilai,
                            'is_complete' => $totalNilai == $totalSiswa,
                            'is_all_final' => $finalCount == $totalSiswa,
                            'completion_percentage' => $totalSiswa > 0 ? round(($totalNilai / $totalSiswa) * 100) : 0,
                            'final_percentage' => $totalSiswa > 0 ? round(($finalCount / $totalSiswa) * 100) : 0
                        ];
                    }
                } else {
                    $progressPersen = 0;
                    $siswaLengkap = 0;
                    $statusJenisNilai = [];
                }
                
                $progressKelas[] = [
                    'kelas' => $jadwal->kelas,
                    'total_siswa' => $totalSiswa,
                    'nilai_selesai' => $siswaLengkap,
                    'progress_persen' => $progressPersen,
                    'status_jenis_nilai' => $statusJenisNilai
                ];
            }
            
            $progressNilai[] = [
                'mata_pelajaran' => $mataPelajaran,
                'total_kelas' => $totalKelas,
                'kelas_detail' => $progressKelas
            ];
        }

        $jenisNilai = JenisNilai::aktif()->get()->map(function($jenis) {
            return [
                'id' => $jenis->id,
                'nama' => $jenis->nama,
                'kategori' => $jenis->kategori,
                'kategori_label' => $jenis->kategori_label,
                'bobot' => $jenis->bobot,
                'deskripsi' => $jenis->deskripsi,
                'status' => $jenis->status,
                'created_at' => $jenis->created_at,
                'updated_at' => $jenis->updated_at,
            ];
        });

        // Debug: log jenis nilai to check if data is loaded
        \Log::info('JenisNilai data:', ['count' => $jenisNilai->count(), 'data' => $jenisNilai->toArray()]);

        return Inertia::render('Nilai/Dashboard', [
            'progressNilai' => $progressNilai,
            'jenisNilai' => $jenisNilai
        ]);
    }

    /**
     * Show form input nilai untuk kelas tertentu
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        
        // Ambil tahun ajaran dan semester aktif dari settings
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));
        
        // Validasi parameter
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_nilai_id' => 'required|exists:jenis_nilai,id'
        ]);

        // Pastikan guru mengajar mata pelajaran ini di kelas ini
        $jadwal = JadwalPelajaran::where('guru_id', $user->id)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        if (!$jadwal && !$user->isKepalaSekolah()) {
            return redirect()->route('nilai-siswa.index')
                ->with('error', 'Anda tidak mengajar mata pelajaran ini di kelas tersebut.');
        }

        // Ambil data yang diperlukan
        $mataPelajaran = MataPelajaran::findOrFail($request->mata_pelajaran_id);
        $kelas = Kelas::with(['siswa' => function($query) {
            $query->where('status', 1)
                  ->orderBy('nama_lengkap', 'asc');
        }])->findOrFail($request->kelas_id);
        $jenisNilai = JenisNilai::findOrFail($request->jenis_nilai_id);
        
        // Ambil KKM untuk mata pelajaran ini
        $kkm = KkmMataPelajaran::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->first();

        // Ambil nilai yang sudah ada (jika edit)
        $nilaiExisting = NilaiSiswa::where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('jenis_nilai_id', $request->jenis_nilai_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
            ->get()
            ->keyBy('siswa_id');

        return Inertia::render('Nilai/InputBatch', [
            'mataPelajaran' => $mataPelajaran,
            'kelas' => $kelas,
            'jenisNilai' => $jenisNilai,
            'kkm' => $kkm,
            'nilaiExisting' => $nilaiExisting,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran
        ]);
    }

    /**
     * Store batch nilai siswa
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validasi dasar
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'jenis_nilai_id' => 'required|exists:jenis_nilai,id',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|string',
            'status' => 'required|in:draft,final',
            'nilai_siswa' => 'required|array|min:1',
        ]);

        // Validasi nilai_siswa berdasarkan status
        foreach ($request->nilai_siswa as $index => $nilaiData) {
            $rules = [
                "nilai_siswa.{$index}.siswa_id" => 'required|exists:siswa,id',
                "nilai_siswa.{$index}.nilai" => 'required|numeric|min:0|max:100',
                "nilai_siswa.{$index}.keterangan" => 'nullable|string|max:255'
            ];
            
            $messages = [
                "nilai_siswa.{$index}.nilai.required" => 'Nilai wajib diisi.',
                "nilai_siswa.{$index}.nilai.numeric" => 'Nilai harus berupa angka.',
                "nilai_siswa.{$index}.nilai.min" => 'Nilai tidak boleh kurang dari 0.',
                "nilai_siswa.{$index}.nilai.max" => 'Nilai tidak boleh lebih dari 100.',
                "nilai_siswa.{$index}.siswa_id.required" => 'Siswa wajib dipilih.',
                "nilai_siswa.{$index}.siswa_id.exists" => 'Siswa tidak ditemukan.',
                "nilai_siswa.{$index}.keterangan.max" => 'Keterangan maksimal 255 karakter.'
            ];
            
            $request->validate($rules, $messages);
        }

        // Pastikan guru mengajar mata pelajaran ini
        $jadwal = JadwalPelajaran::where('guru_id', $user->id)
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester', $request->semester)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->first();

        if (!$jadwal && !$user->isKepalaSekolah()) {
            return back()->with('error', 'Anda tidak mengajar mata pelajaran ini di kelas tersebut.');
        }

        try {
            DB::beginTransaction();

            $successCount = 0;
            $errorMessages = [];

            foreach ($request->nilai_siswa as $nilaiData) {
                try {
                    // Cek apakah nilai sudah ada
                    $existingNilai = NilaiSiswa::where([
                        'siswa_id' => $nilaiData['siswa_id'],
                        'mata_pelajaran_id' => $request->mata_pelajaran_id,
                        'jenis_nilai_id' => $request->jenis_nilai_id,
                        'semester' => $request->semester,
                        'tahun_ajaran' => $request->tahun_ajaran
                    ])->first();

                    // Cek apakah nilai sudah final dan tidak boleh diubah tanpa approval
                    if ($existingNilai && $existingNilai->status === 'final' && $request->status === 'final') {
                        // Jika mengubah nilai final, perlu approval (skip untuk sekarang)
                        if ($existingNilai->nilai != $nilaiData['nilai']) {
                            $siswa = Siswa::find($nilaiData['siswa_id']);
                            $errorMessages[] = "Nilai final {$siswa->nama_lengkap} tidak dapat diubah tanpa persetujuan.";
                            continue;
                        }
                    }

                    $dataToSave = [
                        'siswa_id' => $nilaiData['siswa_id'],
                        'mata_pelajaran_id' => $request->mata_pelajaran_id,
                        'jenis_nilai_id' => $request->jenis_nilai_id,
                        'nilai' => $nilaiData['nilai'],
                        'tanggal_input' => now()->format('Y-m-d'),
                        'semester' => $request->semester,
                        'tahun_ajaran' => $request->tahun_ajaran,
                        'keterangan' => $nilaiData['keterangan'] ?? null,
                        'guru_id' => $user->id,
                        'status' => $request->status
                    ];

                    if ($existingNilai) {
                        // Update jika sudah ada
                        $existingNilai->update($dataToSave);
                    } else {
                        // Create baru jika belum ada
                        NilaiSiswa::create($dataToSave);
                    }
                    
                    $successCount++;
                    
                } catch (\Exception $e) {
                    $siswa = Siswa::find($nilaiData['siswa_id']);
                    $errorMessages[] = "Error pada {$siswa->nama_lengkap}: " . $e->getMessage();
                }
            }

            DB::commit();
            
            $jenisNilai = JenisNilai::find($request->jenis_nilai_id);
            $mataPelajaran = MataPelajaran::find($request->mata_pelajaran_id);
            $kelas = Kelas::find($request->kelas_id);
            
            $statusText = $request->status === 'final' ? 'final' : 'draft';
            $message = "Berhasil menyimpan {$successCount} nilai {$jenisNilai->nama} {$mataPelajaran->nama_mapel} kelas {$kelas->nama_kelas} sebagai {$statusText}.";
            
            if (!empty($errorMessages)) {
                $message .= " Namun ada beberapa error: " . implode(', ', $errorMessages);
            }

            return redirect()->route('nilai-siswa.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan saat menyimpan nilai: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show nilai untuk monitoring
     */
    public function show(Request $request)
    {
        $user = auth()->user();
        
        // Ambil tahun ajaran dan semester aktif dari settings
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));
        
        // Validasi parameter
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $mataPelajaran = MataPelajaran::findOrFail($request->mata_pelajaran_id);
        $kelas = Kelas::with('siswa')->findOrFail($request->kelas_id);
        
        // Pastikan guru mengajar mata pelajaran ini di kelas ini (kecuali kepala tata usaha)
        if (!$user->hasRole('kepala_tatausaha')) {
            $jadwal = JadwalPelajaran::where('guru_id', $user->id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->where('kelas_id', $request->kelas_id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->exists();

            if (!$jadwal) {
                return redirect()->route('nilai-siswa.index')
                    ->with('error', 'Anda tidak mengajar mata pelajaran ini di kelas tersebut.');
            }
        }
        
        // Ambil semua nilai siswa untuk mata pelajaran dan kelas ini
        $nilaiSiswa = NilaiSiswa::with(['siswa', 'jenisNilai'])
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        // Ambil semua jenis nilai
        $jenisNilai = JenisNilai::aktif()->get();

        return Inertia::render('Nilai/Detail', [
            'mataPelajaran' => $mataPelajaran,
            'kelas' => $kelas,
            'siswaList' => $kelas->siswa,
            'nilaiSiswa' => $nilaiSiswa,
            'jenisNilai' => $jenisNilai
        ]);
    }

    /**
     * Helper method untuk menghitung nilai akhir
     */
    private function hitungNilaiAkhir($nilaiPerKategori)
    {
        $totalNilai = 0;
        $totalBobot = 0;

        foreach (['pengetahuan', 'keterampilan'] as $kategori) {
            $nilaiKategori = 0;
            $bobotKategori = 0;
            
            foreach ($nilaiPerKategori[$kategori] as $nilai) {
                if ($nilai['status'] === 'final') {
                    $nilaiKategori += $nilai['nilai'] * ($nilai['bobot'] / 100);
                    $bobotKategori += $nilai['bobot'];
                }
            }
            
            if ($bobotKategori > 0) {
                $totalNilai += $nilaiKategori;
                $totalBobot += 1; // Setiap kategori punya bobot 1
            }
        }

        return $totalBobot > 0 ? $totalNilai / $totalBobot : 0;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Export nilai siswa ke Excel
     */
    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        
        // Ambil tahun ajaran dan semester aktif dari settings
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));
        
        // Validasi parameter
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $mataPelajaran = MataPelajaran::findOrFail($request->mata_pelajaran_id);
        $kelas = Kelas::with('siswa')->findOrFail($request->kelas_id);
        
        // Pastikan guru mengajar mata pelajaran ini di kelas ini (kecuali kepala tata usaha)
        if (!$user->hasRole('kepala_tatausaha')) {
            $jadwal = JadwalPelajaran::where('guru_id', $user->id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->where('kelas_id', $request->kelas_id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->exists();

            if (!$jadwal) {
                return redirect()->back()
                    ->with('error', 'Anda tidak mengajar mata pelajaran ini di kelas tersebut.');
            }
        }
        
        // Ambil data nilai siswa
        $nilaiSiswa = NilaiSiswa::with(['siswa', 'jenisNilai'])
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $jenisNilai = JenisNilai::aktif()->get();

        $fileName = 'Nilai_' . str_replace(' ', '_', $mataPelajaran->nama_mapel) . '_' . 
                   str_replace('-', '_', $kelas->nama_kelas) . '_' . date('Y-m-d') . '.xlsx';

        return Excel::download(
            new NilaiSiswaExport($mataPelajaran, $kelas, $kelas->siswa, $nilaiSiswa, $jenisNilai), 
            $fileName
        );
    }

    /**
     * Export nilai siswa ke PDF
     */
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        
        // Ambil tahun ajaran dan semester aktif dari settings
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));
        
        // Validasi parameter
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $mataPelajaran = MataPelajaran::findOrFail($request->mata_pelajaran_id);
        $kelas = Kelas::with('siswa')->findOrFail($request->kelas_id);
        
        // Pastikan guru mengajar mata pelajaran ini di kelas ini (kecuali kepala tata usaha)
        if (!$user->hasRole('kepala_tatausaha')) {
            $jadwal = JadwalPelajaran::where('guru_id', $user->id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->where('kelas_id', $request->kelas_id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->exists();

            if (!$jadwal) {
                return redirect()->back()
                    ->with('error', 'Anda tidak mengajar mata pelajaran ini di kelas tersebut.');
            }
        }
        
        // Ambil data nilai siswa
        $nilaiSiswa = NilaiSiswa::with(['siswa', 'jenisNilai'])
            ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
            ->whereIn('siswa_id', $kelas->siswa->pluck('id'))
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->get();

        $jenisNilai = JenisNilai::aktif()->get();

        // Siapkan data untuk PDF
        $data = [
            'mataPelajaran' => $mataPelajaran,
            'kelas' => $kelas,
            'siswaList' => $kelas->siswa,
            'nilaiSiswa' => $nilaiSiswa,
            'jenisNilai' => $jenisNilai,
            'tanggal' => date('d F Y'),
            'guru' => $user->name,
            'tahunAjaran' => $tahunAjaran,
            'semester' => $semester
        ];

        $fileName = 'Nilai_' . str_replace(' ', '_', $mataPelajaran->nama_mapel) . '_' . 
                   str_replace('-', '_', $kelas->nama_kelas) . '_' . date('Y-m-d') . '.pdf';

        $pdf = Pdf::loadView('exports.nilai-siswa-pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        
        return $pdf->download($fileName);
    }
}
