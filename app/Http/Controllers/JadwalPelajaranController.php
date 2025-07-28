<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\JadwalPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Validation\Rule;

class JadwalPelajaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $userRole = $user->role->name ?? null;
        
        $query = JadwalPelajaran::with(['mataPelajaran', 'kelas', 'guru']);

        // Jika role adalah murid, filter berdasarkan kelas siswa yang sedang login
        if ($userRole === 'murid') {
            $siswa = $user->siswa;
            if ($siswa && $siswa->kelas_id) {
                $query->where('kelas_id', $siswa->kelas_id);
            } else {
                // Jika siswa tidak memiliki kelas, tampilkan data kosong
                $query->where('id', 0);
            }
        }

        // Filter berdasarkan hari (untuk role selain murid)
        if ($request->filled('hari') && $userRole !== 'murid') {
            $query->where('hari', $request->hari);
        }

        // Filter berdasarkan kelas (untuk role selain murid)
        if ($request->filled('kelas_id') && $userRole !== 'murid') {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter berdasarkan ruangan (untuk role selain murid)
        if ($request->filled('ruangan') && $userRole !== 'murid') {
            $query->where('ruangan', 'like', '%' . $request->ruangan . '%');
        }

        // Untuk murid, urutkan berdasarkan hari dan jam
        if ($userRole === 'murid') {
            $jadwalPelajaran = $query->orderByRaw("
                CASE hari 
                    WHEN 'Senin' THEN 1
                    WHEN 'Selasa' THEN 2
                    WHEN 'Rabu' THEN 3
                    WHEN 'Kamis' THEN 4
                    WHEN 'Jumat' THEN 5
                    WHEN 'Sabtu' THEN 6
                    ELSE 7
                END, jam_mulai ASC
            ")->paginate(10);
        } else {
            $jadwalPelajaran = $query->latest()->paginate(10);
        }
        
        // Get available kelas for filter dropdown (hanya untuk role selain murid)
        $availableKelas = $userRole !== 'murid' ? Kelas::orderBy('nama_kelas')->get() : collect();

        return Inertia::render('JadwalPelajaran/Index', [
            'jadwalPelajaran' => $jadwalPelajaran,
            'availableKelas' => $availableKelas,
            'userRole' => $userRole,
            'permissions' => [
                'canCreate' => in_array($userRole, ['kepala_tatausaha', 'tata_usaha']),
                'canEdit' => in_array($userRole, ['kepala_tatausaha', 'tata_usaha']),
                'canDelete' => in_array($userRole, ['kepala_tatausaha', 'tata_usaha']),
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah jadwal pelajaran.');
        }
        
        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $guru = User::whereHas('role', function($query) {
            $query->where('name', 'guru');
        })->orderBy('name')->get();

        return Inertia::render('JadwalPelajaran/Create', [
            'mataPelajaran' => $mataPelajaran,
            'kelas' => $kelas,
            'guru' => $guru
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk menambah jadwal pelajaran.');
        }
        
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:100',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|string|max:20',
            'status' => 'boolean'
        ]);

        // Check for scheduling conflicts
        $conflict = JadwalPelajaran::where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where('semester', $request->semester)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('status', true)
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhere(function($q) use ($request) {
                          $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                      });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['error' => 'Terdapat konflik jadwal pada waktu yang dipilih.']);
        }

        JadwalPelajaran::create([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id' => $request->kelas_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'ruangan' => $request->ruangan,
            'semester' => $request->semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'status' => $request->boolean('status', true)
        ]);

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JadwalPelajaran $jadwalPelajaran)
    {
        $jadwalPelajaran->load(['mataPelajaran', 'kelas', 'guru']);
        
        return Inertia::render('JadwalPelajaran/Show', [
            'jadwal' => $jadwalPelajaran
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JadwalPelajaran $jadwalPelajaran)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit jadwal pelajaran.');
        }
        
        $jadwalPelajaran->load(['mataPelajaran', 'kelas', 'guru']);
        
        $mataPelajaran = MataPelajaran::orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $guru = User::whereHas('role', function($query) {
            $query->where('name', 'guru');
        })->orderBy('name')->get();

        return Inertia::render('JadwalPelajaran/Edit', [
            'jadwal' => $jadwalPelajaran,
            'mataPelajaranList' => $mataPelajaran,
            'kelasList' => $kelas,
            'guruList' => $guru
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JadwalPelajaran $jadwalPelajaran)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        // Cek akses untuk guru
        if ($userRole === 'guru') {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Anda tidak memiliki akses untuk mengedit jadwal pelajaran.');
        }
        
        $request->validate([
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:users,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'ruangan' => 'nullable|string|max:100',
            'semester' => 'required|in:ganjil,genap',
            'tahun_ajaran' => 'required|string|max:20',
            'status' => 'boolean'
        ]);

        // Check for scheduling conflicts (excluding current record)
        $conflict = JadwalPelajaran::where('id', '!=', $jadwalPelajaran->id)
            ->where('kelas_id', $request->kelas_id)
            ->where('hari', $request->hari)
            ->where('semester', $request->semester)
            ->where('tahun_ajaran', $request->tahun_ajaran)
            ->where('status', true)
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhere(function($q) use ($request) {
                          $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                      });
            })
            ->exists();

        if ($conflict) {
            return back()->withErrors(['error' => 'Terdapat konflik jadwal pada waktu yang dipilih.']);
        }

        $jadwalPelajaran->update([
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'kelas_id' => $request->kelas_id,
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'ruangan' => $request->ruangan,
            'semester' => $request->semester,
            'tahun_ajaran' => $request->tahun_ajaran,
            'status' => $request->boolean('status', true)
        ]);

        return redirect()->route('jadwal-pelajaran.index')
            ->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JadwalPelajaran $jadwalPelajaran)
    {
        try {
            $userRole = auth()->user()->role->name ?? null;
            
            // Cek akses untuk guru
            if ($userRole === 'guru') {
                return redirect()->route('jadwal-pelajaran.index')
                    ->with('error', 'Anda tidak memiliki akses untuk menghapus jadwal pelajaran.');
            }
            
            $jadwalPelajaran->delete();

            return redirect()->route('jadwal-pelajaran.index')
                ->with('success', 'Jadwal pelajaran berhasil dihapus.');
                
        } catch (\Exception $e) {
            return redirect()->route('jadwal-pelajaran.index')
                ->with('error', 'Gagal menghapus jadwal pelajaran: ' . $e->getMessage());
        }
    }
}
