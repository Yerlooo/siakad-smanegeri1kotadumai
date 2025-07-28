<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\User;
use App\Notifications\WaliKelasAssigned;
use App\Notifications\WaliKelasRemoved;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class WaliKelasController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kelas = Kelas::with(['waliKelas'])
            ->withCount('siswa')
            ->get();
            
        $guru = User::whereHas('role', function($query) {
            $query->where('name', 'guru');
        })->get();

        return Inertia::render('WaliKelas/Index', [
            'kelas' => $kelas,
            'guru' => $guru
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Kelas $kelas)
    {
        $kelas->load(['waliKelas', 'siswa', 'jadwalPelajaran.mataPelajaran', 'jadwalPelajaran.guru']);
        
        return Inertia::render('WaliKelas/Show', [
            'kelas' => $kelas,
            'siswa' => $kelas->siswa ?? [],
            'guru' => User::whereHas('role', function($query) {
                $query->where('name', 'guru');
            })->get()
        ]);
    }

    /**
     * Assign wali kelas to a class
     */
    public function assign(Request $request, Kelas $kelas)
    {
        $request->validate([
            'wali_kelas_id' => 'nullable|exists:users,id'
        ]);

        $oldWaliKelas = $kelas->wali_kelas_id;
        $newWaliKelasId = $request->wali_kelas_id;
        $assigner = auth()->user();

        // Update wali kelas
        $kelas->update([
            'wali_kelas_id' => $newWaliKelasId
        ]);

        // Handle notifications
        if ($newWaliKelasId) {
            // Assigning new wali kelas
            $newWaliKelas = User::find($newWaliKelasId);
            if ($newWaliKelas) {
                $newWaliKelas->notify(new WaliKelasAssigned($kelas, $assigner));
            }
        }

        // Notify old wali kelas if being replaced or removed
        if ($oldWaliKelas && $oldWaliKelas !== $newWaliKelasId) {
            $oldWali = User::find($oldWaliKelas);
            if ($oldWali) {
                $oldWali->notify(new WaliKelasRemoved($kelas, $assigner));
            }
        }

        $message = $newWaliKelasId ? 'Wali kelas berhasil ditugaskan' : 'Wali kelas berhasil dihapus';
        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove wali kelas from a class
     */
    public function remove(Kelas $kelas)
    {
        $oldWaliKelas = $kelas->waliKelas;
        $assigner = auth()->user();

        $kelas->update([
            'wali_kelas_id' => null
        ]);

        // Send notification to removed wali kelas
        if ($oldWaliKelas) {
            $oldWaliKelas->notify(new WaliKelasRemoved($kelas, $assigner));
        }

        return redirect()->back()->with('success', 'Wali kelas berhasil dihapus');
    }
}
