<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\User;
use App\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SiswaController extends Controller
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
        // Build query dengan filter
        $query = Siswa::with(['kelas']);

        // Filter berdasarkan search
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                  ->orWhere('nis', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan kelas
        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Statistik berdasarkan filter (untuk info hasil filter)
        $filteredTotal = $query->count();

        // Execute query dengan pagination
        $siswa = $query->latest()->paginate(10)->withQueryString();

        // Ambil semua kelas untuk filter dropdown
        $allKelas = Kelas::select('id', 'nama_kelas', 'tingkat')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();

        // Ambil statistik siswa untuk dashboard
        $hasFilter = $request->filled('search') || $request->filled('status') || $request->filled('kelas_id');
        
        if ($hasFilter) {
            // Jika ada filter, hitung statistik berdasarkan filter
            $baseQuery = Siswa::query();
            
            // Terapkan filter yang sama
            if ($request->filled('search')) {
                $searchTerm = $request->search;
                $baseQuery->where(function ($q) use ($searchTerm) {
                    $q->where('nama_lengkap', 'like', '%' . $searchTerm . '%')
                      ->orWhere('nis', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
                });
            }
            
            if ($request->filled('status')) {
                $baseQuery->where('status', $request->status);
            }
            
            if ($request->filled('kelas_id')) {
                $baseQuery->where('kelas_id', $request->kelas_id);
            }
            
            // Hitung statistik berdasarkan filter
            $totalSiswa = $baseQuery->count();
            $siswaAktif = (clone $baseQuery)->where('status', 'aktif')->count();
            $siswaLulus = (clone $baseQuery)->where('status', 'lulus')->count();
            $siswaPindah = (clone $baseQuery)->where('status', 'pindah')->count();
            $siswaKeluar = (clone $baseQuery)->where('status', 'keluar')->count();
            $filteredTotal = $totalSiswa;
        } else {
            // Jika tidak ada filter, gunakan statistik global
            $totalSiswa = Siswa::count();
            $siswaAktif = Siswa::where('status', 'aktif')->count();
            $siswaLulus = Siswa::where('status', 'lulus')->count();
            $siswaPindah = Siswa::where('status', 'pindah')->count();
            $siswaKeluar = Siswa::where('status', 'keluar')->count();
            $filteredTotal = $totalSiswa;
        }

        return Inertia::render('Siswa/Index', [
            'siswa' => $siswa,
            'allKelas' => $allKelas,
            'statistics' => [
                'totalSiswa' => $totalSiswa,
                'siswaAktif' => $siswaAktif,
                'siswaLulus' => $siswaLulus,
                'siswaPindah' => $siswaPindah,
                'siswaKeluar' => $siswaKeluar,
                'filteredTotal' => $filteredTotal,
                'hasFilter' => $hasFilter,
                'globalTotalSiswa' => Siswa::count(), // Tetap kirim total global untuk referensi
            ],
            'filters' => [
                'search' => $request->search,
                'status' => $request->status,
                'kelas_id' => $request->kelas_id,
            ]
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kelas = Kelas::all();
        
        return Inertia::render('Siswa/Create', [
            'kelas' => $kelas
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string|unique:siswa,nis',
            'nisn' => 'nullable|string',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P,Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'agama' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'nullable|string',
            'email' => 'nullable|email|unique:siswa,email|unique:users,email',
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tahun_masuk' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ]);

        // Normalize jenis_kelamin data
        $data = $request->all();
        if ($data['jenis_kelamin'] === 'L') {
            $data['jenis_kelamin'] = 'Laki-laki';
        } elseif ($data['jenis_kelamin'] === 'P') {
            $data['jenis_kelamin'] = 'Perempuan';
        }

        // Gunakan database transaction untuk memastikan konsistensi data
        $user = null;
        DB::transaction(function () use ($data, &$user) {
            // Buat user jika email disediakan
            if (!empty($data['email'])) {
                // Cari role murid
                $muridRole = Role::where('name', 'murid')->first();
                
                if ($muridRole) {
                    // Format tanggal lahir untuk password (YYYY-MM-DD -> YYYYMMDD)
                    $passwordFromDate = str_replace('-', '', $data['tanggal_lahir']);
                    
                    // Buat user dengan role murid
                    $user = User::create([
                        'name' => $data['nama_lengkap'],
                        'email' => $data['email'],
                        'password' => Hash::make($passwordFromDate), // Default password menggunakan tanggal lahir
                        'role_id' => $muridRole->id,
                    ]);
                }
            }

            // Tambahkan user_id ke data siswa
            $data['user_id'] = $user ? $user->id : null;

            // Buat record siswa
            Siswa::create($data);
        });

        $successMessage = 'Data siswa berhasil ditambahkan.';
        if ($user) {
            $successMessage .= ' Akun login untuk siswa juga telah dibuat dengan password default tanggal lahir (format: YYYYMMDD).';
        }

        return redirect()->route('siswa.index')
            ->with('success', $successMessage);
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        $siswa->load(['kelas']);
        
        return Inertia::render('Siswa/Show', [
            'siswa' => $siswa
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        $kelas = Kelas::all();
        
        // Ensure all nullable fields are strings to prevent Vue warnings
        $siswaData = $siswa->load('kelas');
        $siswaData->nisn = $siswaData->nisn ?? '';
        $siswaData->tempat_lahir = $siswaData->tempat_lahir ?? '';
        $siswaData->agama = $siswaData->agama ?? '';
        $siswaData->alamat = $siswaData->alamat ?? '';
        $siswaData->email = $siswaData->email ?? '';
        $siswaData->no_telepon = $siswaData->no_telepon ?? '';
        $siswaData->nama_ayah = $siswaData->nama_ayah ?? '';
        $siswaData->pekerjaan_ayah = $siswaData->pekerjaan_ayah ?? '';
        $siswaData->nama_ibu = $siswaData->nama_ibu ?? '';
        $siswaData->pekerjaan_ibu = $siswaData->pekerjaan_ibu ?? '';
        $siswaData->tahun_masuk = $siswaData->tahun_masuk ?? date('Y');
        
        return Inertia::render('Siswa/Edit', [
            'siswa' => $siswaData,
            'kelas' => $kelas
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => ['required', 'string', Rule::unique('siswa')->ignore($siswa->id)],
            'nisn' => 'nullable|string',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P,Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'tempat_lahir' => 'required|string|max:255',
            'agama' => 'nullable|string|max:255',
            'alamat' => 'required|string',
            'no_telepon' => 'nullable|string',
            'email' => ['nullable', 'email', Rule::unique('siswa')->ignore($siswa->id)],
            'nama_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'nullable|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tahun_masuk' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'status' => 'required|in:aktif,lulus,pindah,keluar',
        ]);

        // Normalize jenis_kelamin data
        $data = $request->all();
        if ($data['jenis_kelamin'] === 'L') {
            $data['jenis_kelamin'] = 'Laki-laki';
        } elseif ($data['jenis_kelamin'] === 'P') {
            $data['jenis_kelamin'] = 'Perempuan';
        }

        $siswa->update($data);

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
