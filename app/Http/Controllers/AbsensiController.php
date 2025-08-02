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
     * Halaman rekap absensi untuk melihat data yang sudah diinputkan
     */
    public function rekap(Request $request)
    {
        $user = auth()->user();
        
        // Filter berdasarkan tanggal
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');
        $guruId = $request->get('guru_id');
        
        // Base query untuk absensi
        $absensiQuery = Absensi::with(['siswa.kelas', 'mataPelajaran', 'guru'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc');
        
        // Filter berdasarkan role
        if ($user->isGuru()) {
            // Guru hanya bisa melihat absensi yang dia input
            $absensiQuery->where('guru_id', $user->id);
        } elseif (!$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            // Role lain tidak punya akses
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }
        
        // Apply filters
        if ($kelasId) {
            $absensiQuery->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        
        if ($mataPelajaranId) {
            $absensiQuery->where('mata_pelajaran_id', $mataPelajaranId);
        }
        
        if ($guruId && ($user->isKepalaSekolah() || $user->isTataUsaha())) {
            $absensiQuery->where('guru_id', $guruId);
        }
        
        // Paginate results
        $absensiData = $absensiQuery->paginate(20)->withQueryString();
        
        // Statistik singkat - buat query terpisah tanpa pagination
        $statistikQuery = Absensi::with(['siswa.kelas', 'mataPelajaran', 'guru'])
            ->whereBetween('tanggal', [$startDate, $endDate]);
        
        // Apply same filters untuk statistik
        if ($user->isGuru()) {
            $statistikQuery->where('guru_id', $user->id);
        } elseif (!$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }
        
        if ($kelasId) {
            $statistikQuery->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }
        
        if ($mataPelajaranId) {
            $statistikQuery->where('mata_pelajaran_id', $mataPelajaranId);
        }
        
        if ($guruId && ($user->isKepalaSekolah() || $user->isTataUsaha())) {
            $statistikQuery->where('guru_id', $guruId);
        }
        
        $totalAbsensi = $statistikQuery->count();
        $totalHadir = (clone $statistikQuery)->where('status', 'hadir')->count();
        $totalSakit = (clone $statistikQuery)->where('status', 'sakit')->count();
        $totalIzin = (clone $statistikQuery)->where('status', 'izin')->count();
        $totalAlpha = (clone $statistikQuery)->where('status', 'alpha')->count();
        
        // Data untuk dropdown filters
        if ($user->isKepalaSekolah() || $user->isTataUsaha()) {
            $kelasList = Kelas::withCount('siswa')->orderBy('nama_kelas')->get();
            $mataPelajaranList = MataPelajaran::with(['jadwalPelajaran.guru:id,name'])
                ->orderBy('nama_mapel')
                ->get()
                ->map(function($mapel) {
                    $guru = $mapel->jadwalPelajaran->pluck('guru.name')->unique()->filter()->implode(', ');
                    $mapel->guru_name = $guru ?: 'Belum ada guru';
                    return $mapel;
                });
            $guruList = User::whereHas('role', function($q) {
                $q->where('name', 'guru');
            })->orderBy('name')->get();
        } else {
            // Guru hanya bisa lihat kelas dan mapel yang dia ajar
            $jadwalGuru = JadwalPelajaran::where('guru_id', $user->id)->with(['kelas', 'mataPelajaran'])->get();
            $kelasList = $jadwalGuru->pluck('kelas')->unique('id')->values()->map(function($kelas) {
                $kelas->siswa_count = $kelas->siswa()->count();
                return $kelas;
            });
            $mataPelajaranList = $jadwalGuru->pluck('mataPelajaran')->unique('id')->values()->map(function($mapel) {
                $mapel->guru_name = auth()->user()->name;
                return $mapel;
            });
            $guruList = collect(); // Guru tidak perlu dropdown guru
        }
        
        return Inertia::render('Absensi/Rekap', [
            'absensiData' => $absensiData,
            'kelasList' => $kelasList,
            'mataPelajaranList' => $mataPelajaranList,
            'guruList' => $guruList,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mataPelajaranId,
                'guru_id' => $guruId
            ],
            'statistics' => [
                'total' => $totalAbsensi,
                'hadir' => $totalHadir,
                'sakit' => $totalSakit,
                'izin' => $totalIzin,
                'alpha' => $totalAlpha
            ],
            'canViewAll' => $user->isKepalaSekolah() || $user->isTataUsaha()
        ]);
    }

    /**
     * Detail halaman rekap absensi untuk kelas dan mata pelajaran tertentu
     */
    public function rekapDetail(Request $request)
    {
        $user = auth()->user();
        
        // Filter berdasarkan tanggal - expand range untuk mengambil lebih banyak data
        $startDate = $request->get('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');
        $guruId = $request->get('guru_id');
        
        // Validasi kelas_id wajib ada
        if (!$kelasId) {
            return redirect()->route('absensi.rekap')->with('error', 'Kelas harus dipilih.');
        }
        
        // Debug logging
        \Log::info('RekapDetail Request Parameters:', [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'kelas_id' => $kelasId,
            'mata_pelajaran_id' => $mataPelajaranId,
            'guru_id' => $guruId,
            'user_role' => $user->role ?? 'no_role',
            'user_id' => $user->id
        ]);
        
        // Base query untuk absensi dengan eager loading untuk optimasi
        $absensiQuery = Absensi::with(['siswa', 'mataPelajaran', 'guru'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal', 'asc')  // Urutkan ascending untuk pertemuan
            ->orderBy('id', 'asc');      // Kemudian urutkan by id untuk multiple session di hari sama
        
        // Filter berdasarkan role
        if ($user->isGuru()) {
            // Guru hanya bisa melihat absensi yang dia input
            $absensiQuery->where('guru_id', $user->id);
        } elseif (!$user->isKepalaSekolah() && !$user->isTataUsaha()) {
            // Role lain tidak punya akses
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }
        
        // Apply filters
        $absensiQuery->whereHas('siswa', function($q) use ($kelasId) {
            $q->where('kelas_id', $kelasId);
        });
        
        if ($mataPelajaranId) {
            $absensiQuery->where('mata_pelajaran_id', $mataPelajaranId);
        }
        
        if ($guruId) {
            $absensiQuery->where('guru_id', $guruId);
        }
        
        // Log the query
        $sql = $absensiQuery->toSql();
        $bindings = $absensiQuery->getBindings();
        \Log::info('Absensi SQL Query:', [
            'sql' => $sql,
            'bindings' => $bindings
        ]);
        
        // Get all data (no pagination for detail view - we need all data for matrix)
        $absensiData = $absensiQuery->get();
        
        \Log::info('Absensi Data Retrieved:', [
            'total_records' => $absensiData->count(),
            'sample_data' => $absensiData->take(3)->map(function($item) {
                return [
                    'id' => $item->id,
                    'tanggal' => $item->tanggal,
                    'siswa_name' => $item->siswa->nama_lengkap ?? 'No Name',
                    'status' => $item->status,
                    'mata_pelajaran' => $item->mataPelajaran->nama_mapel ?? 'No Mapel'
                ];
            })
        ]);
        
        // Convert to paginator-like structure for frontend compatibility
        $absensiDataFormatted = [
            'data' => $absensiData,
            'total' => $absensiData->count(),
            'per_page' => $absensiData->count(),
            'current_page' => 1,
            'last_page' => 1,
            'from' => 1,
            'to' => $absensiData->count()
        ];
        
        // Statistik terpisah
        $totalAbsensi = $absensiData->count();
        $totalHadir = $absensiData->where('status', 'hadir')->count();
        $totalSakit = $absensiData->where('status', 'sakit')->count();
        $totalIzin = $absensiData->where('status', 'izin')->count();
        $totalAlpha = $absensiData->where('status', 'alpha')->count();
        
        // Data untuk informasi halaman
        $selectedKelas = Kelas::find($kelasId);
        $selectedMapel = $mataPelajaranId ? MataPelajaran::find($mataPelajaranId) : null;
        
        // Daftar mata pelajaran untuk filter (berdasarkan kelas yang dipilih)
        $mataPelajaranList = MataPelajaran::whereHas('jadwalPelajaran', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            })
            ->with(['jadwalPelajaran' => function($query) use ($kelasId) {
                $query->where('kelas_id', $kelasId)->with('guru');
            }])
            ->get()
            ->map(function($mapel) {
                $guru = $mapel->jadwalPelajaran->first();
                return [
                    'id' => $mapel->id,
                    'nama' => $mapel->nama_mapel,
                    'guru_name' => $guru && $guru->guru ? $guru->guru->name : 'Tidak ada guru'
                ];
            });
        
        \Log::info('Final Data Summary:', [
            'absensi_total' => $totalAbsensi,
            'selected_kelas' => $selectedKelas->nama_kelas ?? 'No Kelas',
            'selected_mapel' => $selectedMapel->nama_mapel ?? 'No Mapel',
            'mata_pelajaran_list_count' => $mataPelajaranList->count()
        ]);
        
        return Inertia::render('Absensi/RekapDetail', [
            'absensiData' => $absensiDataFormatted,
            'selectedKelas' => $selectedKelas,
            'selectedMapel' => $selectedMapel,
            'mataPelajaranList' => $mataPelajaranList,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'kelas_id' => $kelasId,
                'mata_pelajaran_id' => $mataPelajaranId,
                'guru_id' => $guruId
            ],
            'statistics' => [
                'total' => $totalAbsensi,
                'hadir' => $totalHadir,
                'sakit' => $totalSakit,
                'izin' => $totalIzin,
                'alpha' => $totalAlpha
            ],
            'canViewAll' => $user->isKepalaSekolah() || $user->isTataUsaha()
        ]);
    }

    /**
     * Display a listing of absensi - Dashboard untuk Guru
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Hanya guru yang bisa akses
        if (!$user->isGuru()) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');

        // Ambil mata pelajaran yang diajar guru ini
        // Ambil semua jadwal yang diajar guru (untuk dropdown)
        $jadwalAllQuery = JadwalPelajaran::with(['mataPelajaran', 'kelas', 'guru'])
            ->where('status', true)
            ->where('guru_id', $user->id); // Hanya jadwal guru yang login
        
        $jadwalAll = $jadwalAllQuery->get();

        // Untuk dropdown
        $kelasList = $jadwalAll->pluck('kelas')->unique('id')->values();
        $mataPelajaranList = $jadwalAll->pluck('mataPelajaran')->unique('id')->values();

        // Filter jadwal berdasarkan filter aktif
        $jadwalFiltered = $jadwalAll;
        if ($kelasId && $mataPelajaranId) {
            $jadwalFiltered = $jadwalAll->filter(function($jadwal) use ($kelasId, $mataPelajaranId) {
                return $jadwal->kelas_id == $kelasId && $jadwal->mata_pelajaran_id == $mataPelajaranId;
            });
        } elseif ($kelasId) {
            $jadwalFiltered = $jadwalAll->where('kelas_id', $kelasId);
        } elseif ($mataPelajaranId) {
            $jadwalFiltered = $jadwalAll->where('mata_pelajaran_id', $mataPelajaranId);
        }

        $absensiData = [];
        foreach ($jadwalFiltered as $jadwal) {
            $siswaKelas = Siswa::with('kelas')->where('kelas_id', $jadwal->kelas_id)->get();
            $absensiQuery = Absensi::with(['siswa.kelas'])
                ->where('tanggal', $tanggal)
                ->where('mata_pelajaran_id', $jadwal->mata_pelajaran_id);
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

        return Inertia::render('Absensi/Index', [
            'absensiData' => $absensiData,
            'tanggal' => $tanggal,
            'kelasList' => $kelasList,
            'mataPelajaranList' => $mataPelajaranList,
            'selectedKelas' => $kelasId,
            'selectedMataPelajaran' => $mataPelajaranId,
            'statusOptions' => Absensi::getStatusOptions(),
            'isKepalaSekolah' => false, // Selalu false karena hanya guru yang akses
            'isTataUsaha' => false // Selalu false karena hanya guru yang akses
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
                'absensi' => 'required|array',
                'absensi.*.siswa_id' => 'required|exists:siswa,id',
                // Perbaikan: validasi hanya menerima key dari status options
                'absensi.*.status' => ['required', Rule::in(array_keys(Absensi::getStatusOptions()))],
                'absensi.*.jam_masuk' => 'nullable|string',
                'absensi.*.jam_keluar' => 'nullable|string',
                'absensi.*.keterangan' => 'nullable|string'
            ]);
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

        // Validasi jadwal pelajaran untuk guru
        $jadwalGuruQuery = JadwalPelajaran::where('guru_id', $user->id);
        if ($kelasId) {
            $jadwalGuruQuery->where('kelas_id', $kelasId);
        }
        if ($mataPelajaranId) {
            $jadwalGuruQuery->where('mata_pelajaran_id', $mataPelajaranId);
        }
        $jadwalGuru = $jadwalGuruQuery->get();

        // Jika bukan kepala sekolah dan tidak ada jadwal yang cocok, return rekapData kosong
        if (!$user->isKepalaSekolah() && ($kelasId || $mataPelajaranId) && $jadwalGuru->isEmpty()) {
            $rekapData = [];
        } else {
            // Filter siswa berdasarkan kelas
            $siswaQuery = Siswa::with(['kelas', 'user']);
            if ($kelasId) {
                $siswaQuery->where('kelas_id', $kelasId);
            }
            if (!$user->isKepalaSekolah()) {
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
        }

        // Data untuk dropdown: hanya kelas dan mapel yang diajar guru
        if ($user->isKepalaSekolah()) {
            $kelasList = Kelas::all();
            $mataPelajaranList = MataPelajaran::all();
        } else {
            $jadwalGuru = JadwalPelajaran::where('guru_id', $user->id)->get();
            $kelasList = $jadwalGuru->pluck('kelas')->unique('id')->values();
            $mataPelajaranList = $jadwalGuru->pluck('mataPelajaran')->unique('id')->values();
        }

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

    /**
     * Monitoring real-time absensi untuk Kepala Tata Usaha dan Tata Usaha
     */
    public function monitoring(Request $request)
    {
        $user = auth()->user();
        
        // Hanya kepala tata usaha dan tata usaha yang bisa akses
        if (!$user->role || !in_array($user->role->name, ['kepala_tatausaha', 'tata_usaha'])) {
            return redirect()->route('dashboard')->with('error', 'Akses ditolak.');
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');
        
        // Ambil semua kelas untuk filter
        $kelasList = Kelas::select('id', 'nama_kelas', 'tingkat')
            ->orderBy('tingkat')
            ->orderBy('nama_kelas')
            ->get();
            
        // Ambil data absensi real-time
        $monitoringData = $this->getMonitoringData($tanggal, $kelasId);

        return Inertia::render('Absensi/Monitoring', [
            'monitoringData' => $monitoringData,
            'kelasList' => $kelasList,
            'filters' => [
                'tanggal' => $tanggal,
                'kelas_id' => $kelasId,
            ]
        ]);
    }

    /**
     * API endpoint untuk data monitoring real-time
     */
    public function monitoringApi(Request $request)
    {
        $user = auth()->user();
        
        // Hanya kepala tata usaha dan tata usaha yang bisa akses
        if (!$user->role || !in_array($user->role->name, ['kepala_tatausaha', 'tata_usaha'])) {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $tanggal = $request->get('tanggal', Carbon::today()->format('Y-m-d'));
        $kelasId = $request->get('kelas_id');
        
        $monitoringData = $this->getMonitoringData($tanggal, $kelasId);
        
        return response()->json([
            'data' => $monitoringData,
            'last_updated' => Carbon::now('Asia/Jakarta')->format('H:i:s'),
            'timestamp' => Carbon::now('Asia/Jakarta')->timestamp
        ]);
    }

    /**
     * Helper method untuk mengambil data monitoring
     */
    private function getMonitoringData($tanggal, $kelasId = null)
    {
                // Query dasar untuk jadwal hari ini
        $jadwalQuery = JadwalPelajaran::with([
            'mataPelajaran',
            'kelas',
            'guru'
        ])
        ->whereHas('kelas')
        ->where('status', true);
        
        // Filter berdasarkan hari
        $hari = Carbon::parse($tanggal)->locale('id')->dayName;
        $jadwalQuery->where('hari', 'like', '%' . $hari . '%');
        
        // Filter berdasarkan kelas jika dipilih
        if ($kelasId) {
            $jadwalQuery->where('kelas_id', $kelasId);
        }
        
        $jadwalList = $jadwalQuery->orderBy('jam_mulai')->get();
        
        // Proses data untuk monitoring
        $monitoringData = $jadwalList->map(function($jadwal) use ($tanggal) {
            // Hitung total siswa di kelas
            $totalSiswa = Siswa::where('kelas_id', $jadwal->kelas_id)
                              ->where('status', 'aktif')
                              ->count();
            
            // Ambil data absensi untuk mata pelajaran, tanggal, dan siswa di kelas ini
            $siswaIds = Siswa::where('kelas_id', $jadwal->kelas_id)
                            ->where('status', 'aktif')
                            ->pluck('id');
                            
            $absensiData = Absensi::where('mata_pelajaran_id', $jadwal->mata_pelajaran_id)
                                  ->where('tanggal', $tanggal)
                                  ->whereIn('siswa_id', $siswaIds) // Hanya siswa di kelas ini
                                  ->with('siswa:id,nama_lengkap,nis')
                                  ->get();
            
            // Hitung statistik absensi
            $absensiStats = [
                'total_siswa' => $totalSiswa,
                'hadir' => 0,
                'sakit' => 0,
                'izin' => 0,
                'alpha' => 0,
                'belum_absen' => $totalSiswa
            ];
            
            $siswaYangSudahAbsen = [];
            
            foreach ($absensiData as $absensi) {
                $siswaYangSudahAbsen[] = $absensi->siswa_id;
                $absensiStats[$absensi->status]++;
                $absensiStats['belum_absen']--;
            }
            
            // Ambil daftar siswa yang belum absen
            $siswaYangBelumAbsen = Siswa::where('kelas_id', $jadwal->kelas_id)
                                       ->where('status', 'aktif')
                                       ->whereNotIn('id', $siswaYangSudahAbsen)
                                       ->select('id', 'nama_lengkap', 'nis')
                                       ->get();
            
            // Ambil detail siswa berdasarkan status untuk kepala tata usaha dan tata usaha
            $siswaByStatus = [
                'hadir' => [],
                'sakit' => [],
                'izin' => [],
                'alpha' => []
            ];
            
            foreach ($absensiData as $absensi) {
                $siswaByStatus[$absensi->status][] = [
                    'id' => $absensi->siswa->id,
                    'nama_lengkap' => $absensi->siswa->nama_lengkap,
                    'nis' => $absensi->siswa->nis,
                    'jam_masuk' => $absensi->jam_masuk ? $absensi->jam_masuk->format('H:i') : null,
                    'jam_keluar' => $absensi->jam_keluar ? $absensi->jam_keluar->format('H:i') : null,
                    'keterangan' => $absensi->keterangan
                ];
            }
            
            return [
                'jadwal_id' => $jadwal->id,
                'mata_pelajaran' => $jadwal->mataPelajaran->nama_mapel ?? 'N/A',
                'kelas' => $jadwal->kelas->nama_kelas ?? 'N/A',
                'guru' => $jadwal->guru->name ?? 'N/A',
                'jam' => $jadwal->jam_mulai . ' - ' . $jadwal->jam_selesai,
                'hari' => $jadwal->hari,
                'statistik' => $absensiStats,
                'siswa_belum_absen' => $siswaYangBelumAbsen,
                'siswa_by_status' => $siswaByStatus,
                'last_input' => $absensiData->max('updated_at')?->setTimezone('Asia/Jakarta')?->format('H:i:s'),
                'persentase_kehadiran' => $totalSiswa > 0 ? round(($absensiStats['hadir'] / $totalSiswa) * 100, 1) : 0
            ];
        });
        
        return $monitoringData;
    }
}