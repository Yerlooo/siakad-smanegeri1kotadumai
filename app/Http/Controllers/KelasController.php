<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Kelas;
use App\Models\User;
use Illuminate\Validation\Rule;

class KelasController extends Controller
{
    /**
     * Check if user has permission for CUD operations
     */
    private function canModifyData()
    {
        $userRole = auth()->user()->role->name ?? null;
        return in_array($userRole, ['kepala_tatausaha', 'tata_usaha', 'guru']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::with(['waliKelas', 'siswa'])
            ->withCount('siswa')
            ->latest()
            ->paginate(10);

        return Inertia::render('Kelas/Index', [
            'kelas' => $kelas,
            'canModify' => $this->canModifyData()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menambah data kelas.');
        }

        // Coba query utama dengan whereHas
        $guru = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->get();
        
        // Jika tidak ada hasil, coba query alternatif dengan join
        if ($guru->isEmpty()) {
            $guru = User::join('roles', 'users.role_id', '=', 'roles.id')
                       ->where('roles.name', 'guru')
                       ->select('users.*')
                       ->get();
        }
        
        // Jika masih kosong, ambil semua user sebagai fallback untuk testing
        if ($guru->isEmpty()) {
            $guru = User::all();
        }
        
        // Debug: log jumlah guru yang ditemukan
        \Log::info('Jumlah guru ditemukan: ' . $guru->count());
        
        return Inertia::render('Kelas/Create', [
            'guru' => $guru
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menambah data kelas.');
        }

        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'tingkat' => 'required|in:10,11,12',
            'jurusan' => 'nullable|string|max:255',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'kapasitas' => 'required|integer|min:1|max:50',
        ]);

        Kelas::create($request->all());

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load(['waliKelas', 'siswa', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);
        
        return Inertia::render('Kelas/Show', [
            'kelas' => $kelas,
            'siswas' => $kelas->siswa
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kelas $kelas)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data kelas.');
        }

        $guru = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->get();
        
        return Inertia::render('Kelas/Edit', [
            'kelas' => $kelas->load('waliKelas'),
            'guru' => $guru,
            'siswas' => $kelas->siswa()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Kelas $kelas)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data kelas.');
        }

        $request->validate([
            'nama_kelas' => ['required', 'string', 'max:255', Rule::unique('kelas')->ignore($kelas->id)],
            'tingkat' => 'required|in:10,11,12',
            'jurusan' => 'nullable|string|max:255',
            'wali_kelas_id' => 'nullable|exists:users,id',
            'kapasitas' => 'required|integer|min:1|max:50',
        ]);

        $kelas->update($request->all());

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kelas $kelas)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menghapus data kelas.');
        }

        // Check if class has students
        if ($kelas->siswa()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus kelas yang masih memiliki siswa.');
        }

        $kelas->delete();

        return redirect()->route('kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    /**
     * Display wali kelas management page
     */
    public function waliKelas()
    {
        $kelas = Kelas::with(['waliKelas', 'siswa'])->withCount('siswa')->get();
        
        // Transformasi data untuk konsistensi frontend
        $kelas->transform(function($kelasItem) {
            if ($kelasItem->waliKelas) {
                $kelasItem->waliKelas->nama_lengkap = $kelasItem->waliKelas->name;
            }
            return $kelasItem;
        });
        
        $guru = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->get()->map(function($user) {
            $user->nama_lengkap = $user->name;
            return $user;
        });

        return Inertia::render('WaliKelas/Index', [
            'kelas' => $kelas,
            'guru' => $guru
        ]);
    }

    /**
     * Assign wali kelas to a class
     */
    public function assignWaliKelas(Request $request, Kelas $kelas)
    {
        $request->validate([
            'wali_kelas_id' => 'nullable|exists:users,id'
        ]);

        $kelas->update(['wali_kelas_id' => $request->wali_kelas_id]);

        return back()->with('success', 'Wali kelas berhasil diperbarui.');
    }

    /**
     * Show wali kelas detail
     */
    public function showWaliKelas(Kelas $kelas)
    {
        $kelas->load(['waliKelas', 'siswa']);
        
        // Transformasi data untuk konsistensi frontend
        if ($kelas->waliKelas) {
            $kelas->waliKelas->nama_lengkap = $kelas->waliKelas->name;
        }
        
        $guru = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->get()->map(function($user) {
            $user->nama_lengkap = $user->name;
            return $user;
        });
        
        return Inertia::render('WaliKelas/Show', [
            'kelas' => $kelas,
            'siswa' => $kelas->siswa,
            'guru' => $guru
        ]);
    }

    /**
     * Show the form for creating wali kelas assignments
     */
    public function createWaliKelas()
    {
        $kelas = Kelas::with(['waliKelas'])->get();
        $guru = User::whereHas('role', function($q) {
            $q->where('name', 'guru');
        })->get();
        
        return Inertia::render('WaliKelas/Create', [
            'kelas' => $kelas,
            'guru' => $guru
        ]);
    }
}
