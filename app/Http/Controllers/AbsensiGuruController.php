<?php

namespace App\Http\Controllers;

use App\Models\AbsensiGuru;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AbsensiGuruController extends Controller
{
    /**
     * Tampilkan halaman absensi guru
     */
    public function index()
    {
        $user = auth()->user();
        
        // Dapatkan data absensi guru untuk user yang sedang login
        $absensiGuru = AbsensiGuru::where('user_id', $user->id)
            ->orderBy('tanggal', 'desc')
            ->with('user')
            ->paginate(10);

        return Inertia::render('AbsensiGuru/Index', [
            'absensiGuru' => $absensiGuru,
            'user' => $user
        ]);
    }

    /**
     * Tampilkan form untuk membuat absensi baru
     */
    public function create()
    {
        return Inertia::render('AbsensiGuru/Create');
    }

    /**
     * Simpan data absensi guru baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jenis_ketidakhadiran' => 'required|in:sakit,izin,dinas,cuti',
            'alasan' => 'required|string|max:500',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        $user = auth()->user();

        // Cek apakah sudah ada absensi untuk tanggal tersebut
        $existingAbsensi = AbsensiGuru::where('user_id', $user->id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($existingAbsensi) {
            return back()->withErrors([
                'tanggal' => 'Anda sudah mengajukan absensi untuk tanggal tersebut.'
            ]);
        }

        AbsensiGuru::create([
            'user_id' => $user->id,
            'tanggal' => $request->tanggal,
            'jenis_ketidakhadiran' => $request->jenis_ketidakhadiran,
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'status_laporan' => 'dilaporkan',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return redirect()->route('absensi-guru.index')->with('success', 'Laporan ketidakhadiran berhasil disimpan.');
    }

    /**
     * Tampilkan detail absensi guru
     */
    public function show(AbsensiGuru $absensiGuru)
    {
        // Pastikan guru hanya bisa melihat absensi mereka sendiri
        if ($absensiGuru->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk melihat data ini.');
        }

        return Inertia::render('AbsensiGuru/Show', [
            'absensiGuru' => $absensiGuru->load('user')
        ]);
    }

    /**
     * Tampilkan form edit absensi guru
     */
    public function edit(AbsensiGuru $absensiGuru)
    {
        // Pastikan guru hanya bisa edit absensi mereka sendiri
        if ($absensiGuru->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit data ini.');
        }

        // Hanya bisa edit jika status masih dilaporkan
        if ($absensiGuru->status_laporan !== 'dilaporkan') {
            return back()->withErrors([
                'message' => 'Laporan yang sudah diterima tidak dapat diubah.'
            ]);
        }

        return Inertia::render('AbsensiGuru/Edit', [
            'absensiGuru' => $absensiGuru
        ]);
    }

    /**
     * Update data absensi guru
     */
    public function update(Request $request, AbsensiGuru $absensiGuru)
    {
        // Pastikan guru hanya bisa update absensi mereka sendiri
        if ($absensiGuru->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah data ini.');
        }

        // Hanya bisa update jika status masih dilaporkan
        if ($absensiGuru->status_laporan !== 'dilaporkan') {
            return back()->withErrors([
                'message' => 'Laporan yang sudah diterima tidak dapat diubah.'
            ]);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'jenis_ketidakhadiran' => 'required|in:sakit,izin,dinas,cuti',
            'alasan' => 'required|string|max:500',
            'keterangan' => 'nullable|string|max:1000'
        ]);

        // Cek apakah sudah ada absensi lain untuk tanggal tersebut
        $existingAbsensi = AbsensiGuru::where('user_id', auth()->id())
            ->where('tanggal', $request->tanggal)
            ->where('id', '!=', $absensiGuru->id)
            ->first();

        if ($existingAbsensi) {
            return back()->withErrors([
                'tanggal' => 'Anda sudah mengajukan absensi untuk tanggal tersebut.'
            ]);
        }

        $absensiGuru->update([
            'tanggal' => $request->tanggal,
            'jenis_ketidakhadiran' => $request->jenis_ketidakhadiran,
            'alasan' => $request->alasan,
            'keterangan' => $request->keterangan,
            'updated_at' => now()
        ]);

        return redirect()->route('absensi-guru.index')->with('success', 'Laporan absensi berhasil diperbarui.');
    }

    /**
     * Hapus data absensi guru
     */
    public function destroy(AbsensiGuru $absensiGuru)
    {
        // Pastikan guru hanya bisa hapus absensi mereka sendiri
        if ($absensiGuru->user_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses untuk menghapus data ini.');
        }

        // Hanya bisa hapus jika status masih dilaporkan
        if ($absensiGuru->status_laporan !== 'dilaporkan') {
            return back()->withErrors([
                'message' => 'Laporan yang sudah diterima tidak dapat dihapus.'
            ]);
        }

        $absensiGuru->delete();

        return redirect()->route('absensi-guru.index')->with('success', 'Laporan absensi berhasil dihapus.');
    }

    /**
     * Monitoring absensi guru (untuk tata usaha)
     */
    public function monitoring(Request $request)
    {
        $query = AbsensiGuru::with('user');

        // Filter berdasarkan status laporan
        if ($request->status_laporan) {
            $query->where('status_laporan', $request->status_laporan);
        }

        // Filter berdasarkan jenis ketidakhadiran
        if ($request->jenis_ketidakhadiran) {
            $query->where('jenis_ketidakhadiran', $request->jenis_ketidakhadiran);
        }

        // Filter berdasarkan bulan/tahun
        if ($request->bulan && $request->tahun) {
            $query->whereMonth('tanggal', $request->bulan)
                  ->whereYear('tanggal', $request->tahun);
        }

        // Filter berdasarkan guru tertentu
        if ($request->guru_id) {
            $query->where('user_id', $request->guru_id);
        }

        $absensiGuru = $query->orderBy('tanggal', 'desc')->paginate(15);

        // Dapatkan daftar guru untuk filter
        $daftarGuru = User::whereHas('role', function($query) {
            $query->where('name', 'guru');
        })->get(['id', 'name']);

        return Inertia::render('AbsensiGuru/Monitoring', [
            'absensiGuru' => $absensiGuru,
            'daftarGuru' => $daftarGuru,
            'filters' => $request->only(['status_laporan', 'jenis_ketidakhadiran', 'bulan', 'tahun', 'guru_id'])
        ]);
    }

    /**
     * Tandai laporan absensi sebagai diterima
     */
    public function updateStatus(Request $request, AbsensiGuru $absensiGuru)
    {
        // Hanya bisa mengubah status dari 'dilaporkan' ke 'diterima'
        if ($absensiGuru->status_laporan !== 'dilaporkan') {
            return back()->withErrors([
                'message' => 'Status laporan tidak dapat diubah.'
            ]);
        }

        $absensiGuru->update([
            'status_laporan' => 'diterima',
            'tanggal_diterima' => now(),
            'diterima_oleh' => auth()->id(),
            'updated_at' => now()
        ]);

        return back()->with('success', 'Laporan absensi guru telah ditandai sebagai diterima.');
    }
}
