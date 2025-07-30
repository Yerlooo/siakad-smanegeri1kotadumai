<?php
/**
 * Script untuk menjalankan seeder kelas dan siswa
 * Jalankan dengan: php run_student_seeders.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== SEEDER KELAS DAN SISWA SMA NEGERI 1 KOTA DUMAI ===\n\n";

echo "🔄 Menjalankan seeder untuk:\n";
echo "   📚 8 Kelas: X.2, X.3, XI.1, XI.2, XI.3, XII.1, XII.2, XII.3\n";
echo "   👨‍🎓 150 Siswa dengan distribusi merata\n\n";

try {
    // 1. Jalankan KelasSeeder
    echo "1️⃣ Membuat data kelas...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'KelasSeeder'
    ]);
    echo "   ✅ KelasSeeder berhasil dijalankan\n\n";
    
    // 2. Jalankan SiswaDataSeeder
    echo "2️⃣ Membuat data 150 siswa...\n";
    \Illuminate\Support\Facades\Artisan::call('db:seed', [
        '--class' => 'SiswaDataSeeder'
    ]);
    echo "   ✅ SiswaDataSeeder berhasil dijalankan\n\n";
    
    // 3. Tampilkan ringkasan
    echo "📊 RINGKASAN DATA:\n";
    $totalKelas = \App\Models\Kelas::count();
    $totalSiswa = \App\Models\Siswa::count();
    $totalUsers = \App\Models\User::where('role_id', function($query) {
        $query->select('id')->from('roles')->where('name', 'murid');
    })->count();
    
    echo "   📚 Total Kelas: {$totalKelas}\n";
    echo "   👨‍🎓 Total Siswa: {$totalSiswa}\n";
    echo "   👤 Total User Murid: {$totalUsers}\n\n";
    
    // 4. Tampilkan distribusi per kelas
    echo "📋 DISTRIBUSI SISWA PER KELAS:\n";
    $kelasList = \App\Models\Kelas::with(['siswa'])->get();
    foreach ($kelasList as $kelas) {
        $jumlahSiswa = $kelas->siswa->count();
        echo "   🏫 {$kelas->nama_kelas} ({$kelas->jurusan}): {$jumlahSiswa} siswa\n";
    }
    
    echo "\n✅ SEEDER BERHASIL DIJALANKAN!\n\n";
    
    echo "📝 CARA LOGIN SISWA:\n";
    echo "   🌐 URL: http://127.0.0.1:8000/login\n";
    echo "   📧 Email: [nama.siswa][nomor]@student.smanse1dumai.sch.id\n";
    echo "   🔐 Password: password123\n";
    echo "   👤 Role: murid\n\n";
    
    echo "📝 CONTOH LOGIN:\n";
    $contohSiswa = \App\Models\Siswa::with('user')->first();
    if ($contohSiswa) {
        echo "   📧 Email: {$contohSiswa->user->email}\n";
        echo "   🔐 Password: password123\n";
        echo "   📚 Kelas: {$contohSiswa->kelas->nama_kelas}\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "🔧 Troubleshooting:\n";
    echo "   1. Pastikan database sudah dibuat\n";
    echo "   2. Jalankan: php artisan migrate\n";
    echo "   3. Pastikan role 'murid' sudah ada (jalankan RoleSeeder)\n";
}
