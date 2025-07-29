<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MuridProfileController extends Controller
{
    /**
     * Display the murid's profile form.
     */
    public function edit(Request $request)
    {
        $user = auth()->user();
        
        // Pastikan user adalah murid
        if (!$user->isMurid()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak. Hanya murid yang dapat mengakses halaman ini.');
        }

        // Ambil data siswa berdasarkan user yang login
        $siswa = Siswa::with('kelas')->where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data murid tidak ditemukan.');
        }

        // Konversi jenis_kelamin dari database ke format form
        if ($siswa->jenis_kelamin) {
            $siswa->jenis_kelamin_form = ($siswa->jenis_kelamin === 'Laki-laki' || $siswa->jenis_kelamin === 'L') ? 'L' : 'P';
        } else {
            $siswa->jenis_kelamin_form = '';
        }

        // Ambil semua kelas untuk dropdown (opsional, mungkin tidak bisa diubah oleh murid)
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        return Inertia::render('Murid/Profile', [
            'siswa' => $siswa,
            'kelasList' => $kelasList,
            'user' => $user
        ]);
    }

    /**
     * Update the murid's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        // Pastikan user adalah murid
        if (!$user->isMurid()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        // Ambil data siswa berdasarkan user yang login
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return redirect()->route('dashboard')->with('error', 'Data murid tidak ditemukan.');
        }

        // Validasi input - hanya field yang boleh diedit murid
        $validated = $request->validate([
            'no_telepon' => 'nullable|string|max:20',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Hapus foto dari validated jika tidak ada file yang diupload
        // agar tidak mengupdate kolom foto dengan null
        if (!$request->hasFile('foto')) {
            unset($validated['foto']);
        }

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
                Storage::disk('public')->delete($siswa->foto);
            }

            // Upload foto baru
            $path = $request->file('foto')->store('siswa-photos', 'public');
            $validated['foto'] = $path;
        }

        // Update data siswa
        $siswa->update($validated);

        return redirect()->back()->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Delete the murid's profile photo.
     */
    public function deletePhoto(Request $request)
    {
        $user = auth()->user();
        
        // Pastikan user adalah murid
        if (!$user->isMurid()) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        // Ambil data siswa berdasarkan user yang login
        $siswa = Siswa::where('user_id', $user->id)->first();
        
        if (!$siswa) {
            return response()->json(['error' => 'Data murid tidak ditemukan'], 404);
        }

        // Hapus foto jika ada
        if ($siswa->foto && Storage::disk('public')->exists($siswa->foto)) {
            Storage::disk('public')->delete($siswa->foto);
        }

        // Update database
        $siswa->update(['foto' => null]);

        return response()->json(['success' => 'Foto berhasil dihapus']);
    }
}
