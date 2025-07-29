<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\KkmMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Setting;
use App\Models\JadwalPelajaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class KkmController extends Controller
{
    /**
               return back()->with('error', 'Gagal memproses data KKM. Silakan coba lagi.');
        }
    }

    }
}

/**
 * Display a listing of the resource.
 */
public function index()
    {
        $user = auth()->user();
        
        // Jika bukan guru atau admin, redirect
        if (!$user->isGuru() && !$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil tahun ajaran dan semester aktif
        $tahunAjaran = Setting::get('academic_year', '2024/2025');
        $semester = strtolower(Setting::get('academic_semester', 'ganjil'));

        // Query dasar - KKM per kelas
        $query = KkmMataPelajaran::with(['mataPelajaran', 'kelas'])
            ->bySemester($semester, $tahunAjaran);

        // Filter berdasarkan request
        if (request('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', request('mata_pelajaran_id'));
        }

        if (request('kelas_id')) {
            $query->where('kelas_id', request('kelas_id'));
        }

        if (request('search')) {
            $query->whereHas('mataPelajaran', function($q) {
                $q->where('nama_mapel', 'like', '%' . request('search') . '%');
            });
        }

        // Jika guru, hanya tampilkan mata pelajaran yang diajar
        if ($user->isGuru()) {
            $mataPelajaranIds = JadwalPelajaran::where('guru_id', $user->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', true)
                ->pluck('mata_pelajaran_id')
                ->unique();
            
            $query->whereIn('mata_pelajaran_id', $mataPelajaranIds);
        }

        $kkmList = $query->orderBy('mata_pelajaran_id')
                        ->paginate(15);

        // Ambil data untuk form
        if ($user->isGuru()) {
            // Jika guru, hanya ambil mata pelajaran yang diajar
            $mataPelajaranIds = JadwalPelajaran::where('guru_id', $user->id)
                ->where('semester', $semester)
                ->where('tahun_ajaran', $tahunAjaran)
                ->where('status', true)
                ->pluck('mata_pelajaran_id')
                ->unique()
                ->toArray();
            
            $mataPelajaranList = MataPelajaran::whereIn('id', $mataPelajaranIds)
                ->orderBy('nama_mapel')
                ->get();
        } else {
            // Jika admin/tata usaha, ambil semua mata pelajaran
            $mataPelajaranList = MataPelajaran::orderBy('nama_mapel')->get();
        }

        return Inertia::render('Kkm/Index', [
            'kkmList' => $kkmList,
            'mataPelajaranList' => $mataPelajaranList->values()->toArray(), // Force to indexed array
            'kelasList' => Kelas::orderBy('nama_kelas')->get()->toArray(),
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'userRole' => $user->role->name
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        // Validasi akses
        if (!$user->isGuru() && !$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'kkm' => 'required|numeric|min:0|max:100',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|string'
        ], [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'kkm.required' => 'Nilai KKM wajib diisi.',
            'kkm.numeric' => 'Nilai KKM harus berupa angka.',
            'kkm.min' => 'Nilai KKM minimal 0.',
            'kkm.max' => 'Nilai KKM maksimal 100.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester tidak valid.',
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.'
        ]);

        // Cek apakah guru mengajar mata pelajaran ini (jika yang input adalah guru)
        if ($user->isGuru()) {
            $jadwal = JadwalPelajaran::where('guru_id', $user->id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->where('semester', $request->semester)
                ->where('tahun_ajaran', $request->tahun_ajaran)
                ->where('status', true)
                ->first();

            if (!$jadwal) {
                return back()->with('error', 'Anda tidak mengajar mata pelajaran ini.');
            }
        }

        // Cek duplikasi - berdasarkan mata pelajaran dan kelas
        $existing = KkmMataPelajaran::where([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id' => $request->kelas_id,
            'semester' => $request->semester,
            'tahun_ajaran' => $request->tahun_ajaran
        ])->first();

        if ($existing) {
            return back()->with('error', 'KKM untuk mata pelajaran dan kelas ini sudah ada.');
        }

        try {
            KkmMataPelajaran::create([
                'mata_pelajaran_id' => $request->mata_pelajaran_id,
                'kelas_id' => $request->kelas_id,
                'kkm' => $request->kkm,
                'semester' => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran
            ]);
            
            return back()->with('success', 'KKM berhasil ditambahkan untuk kelas yang dipilih.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan KKM. Silakan coba lagi.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        
        // Validasi akses
        if (!$user->isGuru() && !$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $kkm = KkmMataPelajaran::findOrFail($id);

        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'kkm' => 'required|numeric|min:0|max:100',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|string'
        ], [
            'mata_pelajaran_id.required' => 'Mata pelajaran wajib dipilih.',
            'mata_pelajaran_id.exists' => 'Mata pelajaran tidak valid.',
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists' => 'Kelas tidak valid.',
            'kkm.required' => 'Nilai KKM wajib diisi.',
            'kkm.numeric' => 'Nilai KKM harus berupa angka.',
            'kkm.min' => 'Nilai KKM minimal 0.',
            'kkm.max' => 'Nilai KKM maksimal 100.',
            'semester.required' => 'Semester wajib dipilih.',
            'semester.in' => 'Semester tidak valid.',
            'tahun_ajaran.required' => 'Tahun ajaran wajib diisi.'
        ]);

        // Cek apakah guru mengajar mata pelajaran ini (jika yang update adalah guru)
        if ($user->isGuru()) {
            $jadwal = JadwalPelajaran::where('guru_id', $user->id)
                ->where('mata_pelajaran_id', $request->mata_pelajaran_id)
                ->where('semester', $request->semester)
                ->where('tahun_ajaran', $request->tahun_ajaran)
                ->where('status', true)
                ->first();

            if (!$jadwal) {
                return back()->with('error', 'Anda tidak mengajar mata pelajaran ini.');
            }
        }

        // Cek duplikasi (kecuali record saat ini) - berdasarkan mata pelajaran dan kelas
        $existing = KkmMataPelajaran::where([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id' => $request->kelas_id,
            'semester' => $request->semester,
            'tahun_ajaran' => $request->tahun_ajaran
        ])->where('id', '!=', $id)->first();

        if ($existing) {
            return back()->with('error', 'KKM untuk mata pelajaran dan kelas ini sudah ada.');
        }

        try {
            $kkm->update([
                'mata_pelajaran_id' => $request->mata_pelajaran_id,
                'kelas_id' => $request->kelas_id,
                'kkm' => $request->kkm,
                'semester' => $request->semester,
                'tahun_ajaran' => $request->tahun_ajaran
            ]);
            
            return back()->with('success', 'KKM berhasil diperbarui untuk kelas yang dipilih.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui KKM. Silakan coba lagi.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();
        
        // Validasi akses
        if (!$user->isGuru() && !$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return back()->with('error', 'Akses ditolak.');
        }

        $kkm = KkmMataPelajaran::findOrFail($id);

        // Cek apakah guru mengajar mata pelajaran ini (jika yang hapus adalah guru)
        if ($user->isGuru()) {
            $jadwal = JadwalPelajaran::where('guru_id', $user->id)
                ->where('mata_pelajaran_id', $kkm->mata_pelajaran_id)
                ->where('semester', $kkm->semester)
                ->where('tahun_ajaran', $kkm->tahun_ajaran)
                ->where('status', true)
                ->first();

            if (!$jadwal) {
                return back()->with('error', 'Anda tidak mengajar mata pelajaran ini.');
            }
        }

        try {
            $kkm->delete();
            
            return back()->with('success', 'KKM berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus KKM. Silakan coba lagi.');
        }
    }

    /**
     * Bulk import KKM
     */
    public function bulkStore(Request $request)
    {
        $user = auth()->user();
        
        // Validasi akses
        if (!$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return back()->with('error', 'Akses ditolak. Hanya Kepala Sekolah dan Tata Usaha yang dapat melakukan bulk import.');
        }

        // Ambil tahun ajaran dan semester aktif jika tidak disediakan
        $tahunAjaran = $request->tahun_ajaran ?? Setting::get('academic_year', '2024/2025');
        $semester = $request->semester ?? strtolower(Setting::get('academic_semester', 'ganjil'));

        $request->validate([
            'kkm_data' => 'required|array|min:1',
            'kkm_data.*.mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kkm_data.*.kelas_id' => 'required|exists:kelas,id',
            'kkm_data.*.kkm' => 'required|numeric|min:0|max:100'
        ]);

        try {
            DB::beginTransaction();

            $successCount = 0;
            $errorMessages = [];

            foreach ($request->kkm_data as $index => $kkmData) {
                // Cek duplikasi - berdasarkan mata pelajaran dan kelas
                $existing = KkmMataPelajaran::where([
                    'mata_pelajaran_id' => $kkmData['mata_pelajaran_id'],
                    'kelas_id' => $kkmData['kelas_id'],
                    'semester' => $semester,
                    'tahun_ajaran' => $tahunAjaran
                ])->first();

                if ($existing) {
                    // Update jika sudah ada
                    $existing->update([
                        'kkm' => $kkmData['kkm']
                    ]);
                    $successCount++;
                } else {
                    // Buat baru jika belum ada
                    KkmMataPelajaran::create([
                        'mata_pelajaran_id' => $kkmData['mata_pelajaran_id'],
                        'kelas_id' => $kkmData['kelas_id'],
                        'kkm' => $kkmData['kkm'],
                        'semester' => $semester,
                        'tahun_ajaran' => $tahunAjaran
                    ]);
                    $successCount++;
                }
            }

            DB::commit();

            return back()->with('success', "Berhasil memproses {$successCount} data KKM per kelas.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses data KKM. Silakan coba lagi.');
        }
    }

    /**
     * Bulk delete KKM
     */
    public function bulkDelete(Request $request)
    {
        $user = auth()->user();
        
        // Validasi akses
        if (!$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return back()->with('error', 'Akses ditolak. Hanya Kepala Sekolah dan Tata Usaha yang dapat melakukan bulk delete.');
        }

        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|exists:kkm_mata_pelajaran,id'
        ]);

        try {
            DB::beginTransaction();

            $deletedCount = KkmMataPelajaran::whereIn('id', $request->ids)->delete();

            DB::commit();

            return back()->with('success', "Berhasil menghapus {$deletedCount} data KKM.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data KKM. Silakan coba lagi.');
        }
    }
}
