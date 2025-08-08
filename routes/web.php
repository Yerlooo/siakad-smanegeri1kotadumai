<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\MataPelajaranController;
use App\Http\Controllers\JadwalPelajaranController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\NilaiSiswaController;
use App\Http\Controllers\NilaiSayaController;
use App\Http\Controllers\MuridProfileController;
use App\Http\Controllers\KkmController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AbsensiGuruController;
use App\Http\Controllers\AbsensiExportController;
use App\Http\Controllers\ApprovalRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\WaliKelasController;
use App\Http\Controllers\WaliKelasPrivilegeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\TestingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Global Search - menggunakan GET untuk menghindari CSRF
    Route::get('/search/global', [SearchController::class, 'globalSearch'])->name('search.global');
    
    // Functional Testing (only in development)
    if (config('app.env') === 'local' || config('app.debug')) {
        Route::get('/functional-test', function () {
            return view('functional-test');
        })->name('functional-test');
    }
    
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Data Guru (temporarily accessible by all for testing)
    Route::resource('guru', GuruController::class)->parameters(['guru' => 'user']);
    
    // Data Siswa (accessible by all aucthenticated users)
    Route::resource('siswa', SiswaController::class);
    
    // Kelas Management (accessible by all authenticated users)
    Route::resource('kelas', KelasController::class)->parameters(['kelas' => 'kelas']);
    
    // Mata Pelajaran (accessible by all authenticated users)
    Route::resource('mata-pelajaran', MataPelajaranController::class);
    
    // Jadwal Pelajaran (accessible by all authenticated users)
    Route::resource('jadwal-pelajaran', JadwalPelajaranController::class);
    
    // === SISTEM PENILAIAN === //
    // Nilai Siswa (accessible by Guru and Kepala Tata Usaha)
    Route::middleware('role:guru,kepala_tatausaha')->group(function () {
        Route::get('/nilai-siswa', [NilaiSiswaController::class, 'index'])->name('nilai-siswa.index');
        Route::get('/nilai-siswa/input', [NilaiSiswaController::class, 'create'])->name('nilai-siswa.create');
        Route::post('/nilai-siswa', [NilaiSiswaController::class, 'store'])->name('nilai-siswa.store');
        Route::get('/nilai-siswa/detail', [NilaiSiswaController::class, 'show'])->name('nilai-siswa.show');
        
        // Export routes
        Route::get('/nilai-siswa/export/excel', [NilaiSiswaController::class, 'exportExcel'])->name('nilai-siswa.export.excel');
        Route::get('/nilai-siswa/export/pdf', [NilaiSiswaController::class, 'exportPdf'])->name('nilai-siswa.export.pdf');
        
        // API untuk jenis nilai (hanya untuk guru)
        Route::middleware('role:guru')->group(function () {
            Route::get('/api/jenis-nilai', [NilaiSiswaController::class, 'getJenisNilai'])->name('api.jenis-nilai.get');
            Route::post('/api/jenis-nilai', [NilaiSiswaController::class, 'storeJenisNilai'])->name('api.jenis-nilai.store');
            Route::put('/api/jenis-nilai/{id}', [NilaiSiswaController::class, 'updateJenisNilai'])->name('api.jenis-nilai.update');
            Route::delete('/api/jenis-nilai/{id}', [NilaiSiswaController::class, 'deleteJenisNilai'])->name('api.jenis-nilai.delete');
            Route::post('/api/jenis-nilai/settings', [NilaiSiswaController::class, 'saveJenisNilaiSettings'])->name('api.jenis-nilai.settings');
        });
    });
    
    // Nilai Saya - untuk Murid
    Route::middleware('role:murid')->group(function () {
        Route::get('/nilai-saya', [NilaiSayaController::class, 'index'])->name('nilai-saya.index');
        Route::get('/nilai-saya/export/pdf', [NilaiSayaController::class, 'exportPdf'])->name('nilai-saya.export-pdf');
        
        // Profile Murid
        Route::get('/murid/profile', [MuridProfileController::class, 'edit'])->name('murid.profile.edit');
        Route::post('/murid/profile', [MuridProfileController::class, 'update'])->name('murid.profile.update');
        Route::delete('/murid/profile/photo', [MuridProfileController::class, 'deletePhoto'])->name('murid.profile.delete-photo');
    });
    
    // === SISTEM ABSENSI === //
    // Absensi (accessible by Guru only)
    Route::middleware('role:guru')->group(function () {
        Route::get('/absensi', [AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store');
        Route::get('/absensi/rekap-siswa', [AbsensiController::class, 'rekapSiswa'])->name('absensi.rekap-siswa');
        Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy'])->name('absensi.destroy');
    });
    
    // Rekap Absensi (accessible by Guru, Kepala Tata Usaha and Tata Usaha)
    Route::middleware('role:guru,kepala_tatausaha,tata_usaha')->group(function () {
        Route::get('/absensi/rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
        Route::get('/absensi/rekap/detail', [AbsensiController::class, 'rekapDetail'])->name('absensi.rekap.detail');
        Route::get('/absensi/rekap/export', [AbsensiExportController::class, 'export'])->name('absensi.rekap.export');
    });
    
    // Laporan Absensi (accessible by Kepala Tata Usaha and Tata Usaha)
    Route::middleware('role:kepala_tatausaha,tata_usaha')->group(function () {
        Route::get('/absensi/laporan', [AbsensiController::class, 'laporan'])->name('absensi.laporan');
        Route::get('/absensi/monitoring', [AbsensiController::class, 'monitoring'])->name('absensi.monitoring');
        Route::get('/absensi/monitoring/api', [AbsensiController::class, 'monitoringApi'])->name('absensi.monitoring.api');
    });
    
    // === ABSENSI GURU === //
    // Monitoring Absensi Guru (accessible by Kepala Tata Usaha and Tata Usaha) - HARUS DI ATAS ROUTES DENGAN PARAMETER
    Route::middleware('role:kepala_tatausaha,tata_usaha')->group(function () {
        Route::get('/absensi-guru/monitoring', [AbsensiGuruController::class, 'monitoring'])->name('absensi-guru.monitoring');
        Route::put('/absensi-guru/{absensiGuru}/status', [AbsensiGuruController::class, 'updateStatus'])->name('absensi-guru.update-status');
    });
    
    // Absensi Guru (accessible by Guru only)
    Route::middleware('role:guru')->group(function () {
        Route::get('/absensi-guru', [AbsensiGuruController::class, 'index'])->name('absensi-guru.index');
        Route::get('/absensi-guru/create', [AbsensiGuruController::class, 'create'])->name('absensi-guru.create');
        Route::post('/absensi-guru', [AbsensiGuruController::class, 'store'])->name('absensi-guru.store');
        Route::get('/absensi-guru/{absensiGuru}', [AbsensiGuruController::class, 'show'])->name('absensi-guru.show');
        Route::get('/absensi-guru/{absensiGuru}/edit', [AbsensiGuruController::class, 'edit'])->name('absensi-guru.edit');
        Route::put('/absensi-guru/{absensiGuru}', [AbsensiGuruController::class, 'update'])->name('absensi-guru.update');
        Route::delete('/absensi-guru/{absensiGuru}', [AbsensiGuruController::class, 'destroy'])->name('absensi-guru.destroy');
    });
    
    // KKM Management (accessible by Guru, Kepala Tata Usaha and Tata Usaha)
    Route::middleware('role:guru,kepala_tatausaha,tata_usaha')->group(function () {
        Route::get('/kkm', [KkmController::class, 'index'])->name('kkm.index');
        Route::post('/kkm', [KkmController::class, 'store'])->name('kkm.store');
        Route::put('/kkm/{id}', [KkmController::class, 'update'])->name('kkm.update');
        Route::delete('/kkm/{id}', [KkmController::class, 'destroy'])->name('kkm.destroy');
        Route::post('/kkm/bulk', [KkmController::class, 'bulkStore'])->name('kkm.bulk-store');
        Route::post('/kkm/bulk-delete', [KkmController::class, 'bulkDelete'])->name('kkm.bulk-delete');
    });
    
    // Settings (accessible by Kepala Tata Usaha only)
    Route::middleware('role:kepala_tatausaha')->group(function () {
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });

    // === WALI KELAS PRIVILEGES === //
    // Routes khusus untuk guru yang ditugaskan sebagai wali kelas (HARUS DI ATAS SEBELUM WALI KELAS MANAGEMENT)
    Route::middleware('wali_kelas:wali_kelas_only')->group(function () {
        Route::get('/wali-kelas-dashboard', [WaliKelasPrivilegeController::class, 'dashboard'])->name('wali-kelas.dashboard');
        Route::get('/wali-kelas/monitoring-siswa', [WaliKelasPrivilegeController::class, 'monitoringSiswa'])->name('wali-kelas.monitoring-siswa');
        Route::get('/wali-kelas/konsultasi-siswa', [WaliKelasPrivilegeController::class, 'konsultasiSiswa'])->name('wali-kelas.konsultasi-siswa');
        Route::get('/wali-kelas/laporan-kelas/{kelasId}', [WaliKelasPrivilegeController::class, 'laporanKelas'])->name('wali-kelas.laporan-kelas');
        Route::get('/wali-kelas/laporan-kelas/{kelasId}/export-pdf', [WaliKelasPrivilegeController::class, 'exportLaporanPDF'])->name('wali-kelas.export-pdf');
        Route::get('/wali-kelas/laporan-kelas/{kelasId}/export-excel', [WaliKelasPrivilegeController::class, 'exportLaporanExcel'])->name('wali-kelas.export-excel');
        Route::get('/wali-kelas/laporan-kelas/{kelasId}/student/{siswaId}/pdf', [WaliKelasPrivilegeController::class, 'exportStudentReportPDF'])->name('wali-kelas.student-report-pdf');
        Route::get('/wali-kelas/student-report/{kelasId}/{siswaId}', [WaliKelasPrivilegeController::class, 'exportStudentReportPDF'])->name('wali-kelas.student-report-pdf');
        Route::get('/wali-kelas/edit-siswa/{siswaId}', [WaliKelasPrivilegeController::class, 'editSiswa'])->name('wali-kelas.edit-siswa');
        Route::put('/wali-kelas/update-siswa/{siswaId}', [WaliKelasPrivilegeController::class, 'updateSiswa'])->name('wali-kelas.update-siswa');
    });
    
    // Wali Kelas Management (accessible by Kepala Tata Usaha and Tata Usaha)
    Route::middleware('role:kepala_tatausaha,tata_usaha')->group(function () {
        Route::get('/wali-kelas', [WaliKelasController::class, 'index'])->name('wali-kelas.index');
        Route::get('/wali-kelas/{kelas}', [WaliKelasController::class, 'show'])->name('wali-kelas.show');
        Route::post('/wali-kelas/{kelas}/assign', [WaliKelasController::class, 'assign'])->name('wali-kelas.assign');
        Route::delete('/wali-kelas/{kelas}/remove', [WaliKelasController::class, 'remove'])->name('wali-kelas.remove');
    });

    // === SISTEM PERSETUJUAN === //
    // Approval Request Routes
    Route::middleware('role:tata_usaha')->group(function () {
        Route::get('/approval-requests', [ApprovalRequestController::class, 'index'])->name('approval-requests.index');
        Route::patch('/approval-requests/{approval}/approve', [ApprovalRequestController::class, 'approve'])->name('approval-requests.approve');
        Route::patch('/approval-requests/{approval}/reject', [ApprovalRequestController::class, 'reject'])->name('approval-requests.reject');
    });
    
    // My Approval Requests (accessible by all authenticated users)
    Route::get('/my-approval-requests', [ApprovalRequestController::class, 'myRequests'])->name('my-approval-requests.index');
    Route::post('/approval-requests', [ApprovalRequestController::class, 'store'])->name('approval-requests.store');
    
    // === SISTEM NOTIFIKASI === //
    // Notification Routes (accessible by all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::patch('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/delete-read', [NotificationController::class, 'deleteRead'])->name('notifications.delete-read');
    
    // API routes for notifications
    Route::get('/api/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
    Route::get('/api/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');
    
    // === AKSES KHUSUS MURID === //
    // Routes untuk Murid (read-only access)
    Route::middleware('role:murid')->group(function () {
        Route::get('/murid/jadwal', [JadwalPelajaranController::class, 'index'])->name('murid.jadwal');
        Route::get('/murid/nilai', [NilaiSiswaController::class, 'show'])->name('murid.nilai');
        Route::get('/murid/absensi', [AbsensiController::class, 'show'])->name('murid.absensi');
        Route::get('/murid/profil', [SiswaController::class, 'profile'])->name('murid.profil');
    });
});

// Include testing routes if in development environment
if (config('app.env') === 'local' || config('app.debug')) {
    require __DIR__.'/testing.php';
}

require __DIR__.'/auth.php';

// Load debug routes in development
if (app()->environment('local')) {
    require __DIR__.'/debug.php';
}
