<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\MataPelajaran;
use Illuminate\Validation\Rule;

class MataPelajaranController extends Controller
{
    /**
     * Check if user has permission for CUD operations
     */
    private function canModifyData()
    {
        $userRole = auth()->user()->role->name ?? null;
        // Guru hanya bisa melihat, tidak bisa tambah/edit/hapus
        return in_array($userRole, ['kepala_tatausaha', 'tata_usaha']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MataPelajaran::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_mapel', 'like', "%{$search}%")
                  ->orWhere('kode_mapel', 'like', "%{$search}%");
            });
        }

        // Filter berdasarkan jam pelajaran
        if ($request->filled('jam_pelajaran')) {
            $query->where('jam_pelajaran', $request->jam_pelajaran);
        }

                $mataPelajaran = $query->latest()->paginate(10);

        return Inertia::render('MataPelajaran/Index', [
            'mataPelajaran' => $mataPelajaran,
            'canModify' => $this->canModifyData()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menambah data mata pelajaran.');
        }

        return Inertia::render('MataPelajaran/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menambah data mata pelajaran.');
        }

        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajaran,kode_mapel',
            'jam_pelajaran' => 'required|integer|min:1|max:20',
            'deskripsi' => 'nullable|string'
        ]);

        MataPelajaran::create([
            'nama_mapel' => $request->nama_mapel,
            'kode_mapel' => $request->kode_mapel,
            'jam_pelajaran' => $request->jam_pelajaran,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MataPelajaran $mataPelajaran)
    {
        $mataPelajaran->load(['jadwalPelajaran.kelas', 'jadwalPelajaran.guru']);
        
        return Inertia::render('MataPelajaran/Show', [
            'mataPelajaran' => $mataPelajaran
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MataPelajaran $mataPelajaran)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data mata pelajaran.');
        }

        return Inertia::render('MataPelajaran/Edit', [
            'mataPelajaran' => $mataPelajaran
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MataPelajaran $mataPelajaran)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk mengubah data mata pelajaran.');
        }

        $request->validate([
            'nama_mapel' => 'required|string|max:255',
            'kode_mapel' => ['required', 'string', 'max:10', Rule::unique('mata_pelajaran', 'kode_mapel')->ignore($mataPelajaran->id)],
            'jam_pelajaran' => 'required|integer|min:1|max:20',
            'deskripsi' => 'nullable|string'
        ]);

        $mataPelajaran->update([
            'nama_mapel' => $request->nama_mapel,
            'kode_mapel' => $request->kode_mapel,
            'jam_pelajaran' => $request->jam_pelajaran,
            'deskripsi' => $request->deskripsi
        ]);

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MataPelajaran $mataPelajaran)
    {
        if (!$this->canModifyData()) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk menghapus data mata pelajaran.');
        }

        // Check if mata pelajaran has jadwal
        if ($mataPelajaran->jadwalPelajaran()->count() > 0) {
            return back()->with('error', 'Tidak dapat menghapus mata pelajaran yang masih memiliki jadwal.');
        }

        $mataPelajaran->delete();

        return redirect()->route('mata-pelajaran.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
