<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Setting;
use App\Models\User;
use App\Models\Siswa;
use App\Models\MataPelajaran;
use App\Models\JadwalPelajaran;

class SettingsController extends Controller
{
    public function __construct()
    {
        // Temporarily disable middleware for debugging
        // $this->middleware(function ($request, $next) {
        //     $userRole = auth()->user()->role->name ?? null;
            
        //     if ($userRole !== 'kepala_sekolah') {
        //         return redirect()->route('dashboard')
        //             ->with('error', 'Anda tidak memiliki akses untuk mengakses pengaturan sistem.');
        //     }
            
        //     return $next($request);
        // });
    }

    /**
     * Display the settings page.
     */
    public function index()
    {
        try {
            // Get all settings with default fallback
            $schoolSettings = [];
            $academicSettings = [];
            
            try {
                $schoolSettings = Setting::getByGroup('school');
                $academicSettings = Setting::getByGroup('academic');
            } catch (\Exception $e) {
                // If settings don't exist, use defaults
                $schoolSettings = collect([]);
                $academicSettings = collect([]);
            }
            
            // Combine all settings with defaults
            $settings = array_merge($schoolSettings->toArray(), $academicSettings->toArray());
            
            // Add default values if not exist
            $defaultSettings = [
                'school_name' => 'SMA Negeri 1 Kota Dumai',
                'school_npsn' => '10404563',
                'school_address' => 'Jl. Pendidikan No. 1, Kota Dumai, Riau',
                'school_principal' => 'Dr. H. Ahmad Dahlan, M.Pd',
                'school_phone' => '(0765) 123456',
                'academic_year' => '2024/2025',
                'academic_semester' => 'Ganjil'
            ];
            
            $settings = array_merge($defaultSettings, $settings);
            
            // Get statistics
            $statistics = [
                'total_guru' => User::whereHas('role', function($q) {
                    $q->whereIn('name', ['guru', 'kepala_tatausaha', 'tata_usaha']);
                })->count(),
                'total_siswa' => Siswa::count(),
                'total_mata_pelajaran' => MataPelajaran::count(),
                'total_jadwal' => JadwalPelajaran::where('status', true)->count(),
            ];

            return Inertia::render('Settings/Index', [
                'settings' => $settings,
                'statistics' => $statistics,
                'canEdit' => true, // Kepala tata usaha bisa edit semua
            ]);
        } catch (\Exception $e) {
            // Return error page with basic data
            return Inertia::render('Settings/Index', [
                'settings' => [
                    'school_name' => 'SMA Negeri 1 Kota Dumai',
                    'school_npsn' => '10404563',
                    'school_address' => 'Jl. Pendidikan No. 1, Kota Dumai, Riau',
                    'school_principal' => 'Dr. H. Ahmad Dahlan, M.Pd',
                    'school_phone' => '(0765) 123456',
                    'academic_year' => '2024/2025',
                    'academic_semester' => 'Ganjil'
                ],
                'statistics' => [
                    'total_guru' => 0,
                    'total_siswa' => 0,
                    'total_mata_pelajaran' => 0,
                    'total_jadwal' => 0
                ],
                'canEdit' => true,
                'error' => 'Error loading settings: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        $userRole = auth()->user()->role->name ?? null;
        
        if ($userRole !== 'kepala_tatausaha') {
            return redirect()->route('dashboard')
                ->with('error', 'Anda tidak memiliki akses untuk mengubah pengaturan sistem.');
        }

        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_npsn' => 'required|string|max:20',
            'school_address' => 'required|string|max:500',
            'school_principal' => 'required|string|max:255',
            'school_phone' => 'nullable|string|max:20',
            'academic_year' => 'required|string|max:20',
            'academic_semester' => 'required|in:Ganjil,Genap',
        ]);

        // Update school settings
        Setting::set('school_name', $request->school_name, 'string', 'school', 'Nama sekolah');
        Setting::set('school_npsn', $request->school_npsn, 'string', 'school', 'NPSN sekolah');
        Setting::set('school_address', $request->school_address, 'string', 'school', 'Alamat sekolah');
        Setting::set('school_principal', $request->school_principal, 'string', 'school', 'Kepala tata usaha');
        Setting::set('school_phone', $request->school_phone ?? '', 'string', 'school', 'Telepon sekolah');
        
        // Update academic settings
        Setting::set('academic_year', $request->academic_year, 'string', 'academic', 'Tahun ajaran aktif');
        Setting::set('academic_semester', $request->academic_semester, 'string', 'academic', 'Semester aktif');

        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}
