<?php
/**
 * Script untuk memperbaiki masalah role user yang menyebabkan error 401
 * Jalankan dengan: php fix_user_roles.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel app
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== DIAGNOSA MASALAH 401 UNAUTHORIZED ===\n\n";

// 1. Periksa apakah tabel roles ada dan berisi data
echo "1. Memeriksa data roles...\n";
$roles = Role::all();
if ($roles->isEmpty()) {
    echo "❌ ERROR: Tabel roles kosong! Ini penyebab utama error 401.\n";
    echo "   Solusi: Jalankan seeder untuk membuat role default.\n\n";
    
    // Buat role default
    $defaultRoles = [
        ['name' => 'kepala_tatausaha', 'display_name' => 'Kepala Tata Usaha'],
        ['name' => 'tata_usaha', 'display_name' => 'Tata Usaha'],
        ['name' => 'guru', 'display_name' => 'Guru'],
        ['name' => 'murid', 'display_name' => 'Murid/Siswa'],
    ];
    
    foreach ($defaultRoles as $roleData) {
        Role::firstOrCreate(['name' => $roleData['name']], $roleData);
        echo "✅ Role '{$roleData['name']}' berhasil dibuat.\n";
    }
    echo "\n";
} else {
    echo "✅ Role ditemukan:\n";
    foreach ($roles as $role) {
        echo "   - {$role->name} ({$role->display_name})\n";
    }
    echo "\n";
}

// 2. Periksa user tanpa role
echo "2. Memeriksa user tanpa role...\n";
$usersWithoutRole = User::whereNull('role_id')->get();
if (!$usersWithoutRole->isEmpty()) {
    echo "❌ Ditemukan " . $usersWithoutRole->count() . " user tanpa role:\n";
    foreach ($usersWithoutRole as $user) {
        echo "   - {$user->name} ({$user->email})\n";
    }
    
    // Assign role default ke user pertama (biasanya admin)
    $adminRole = Role::where('name', 'kepala_tatausaha')->first();
    if ($adminRole && $usersWithoutRole->count() > 0) {
        $firstUser = $usersWithoutRole->first();
        $firstUser->role_id = $adminRole->id;
        $firstUser->save();
        echo "✅ User '{$firstUser->name}' berhasil diberi role 'kepala_tatausaha'.\n";
    }
    echo "\n";
} else {
    echo "✅ Semua user sudah memiliki role.\n\n";
}

// 3. Tampilkan mapping user dan role
echo "3. Mapping user dan role saat ini:\n";
$users = User::with('role')->get();
foreach ($users as $user) {
    $roleName = $user->role ? $user->role->name : 'NO ROLE';
    echo "   - {$user->name} ({$user->email}) => {$roleName}\n";
}
echo "\n";

// 4. Test akses route dengan role
echo "4. Panduan akses route berdasarkan role:\n";
echo "   📍 Dashboard: Semua user yang sudah login\n";
echo "   📍 Nilai Siswa: role:guru,kepala_tatausaha\n";
echo "   📍 Nilai Saya: role:murid\n";
echo "   📍 Absensi: role:guru,kepala_tatausaha\n";
echo "   📍 Settings: role:kepala_tatausaha\n";
echo "   📍 Wali Kelas: role:kepala_tatausaha,tata_usaha\n\n";

echo "=== SELESAI ===\n";
echo "Jika masih ada error 401, pastikan:\n";
echo "1. User sudah login (session valid)\n";
echo "2. User memiliki role yang sesuai untuk route yang diakses\n";
echo "3. Middleware 'role' sudah terdaftar di bootstrap/app.php\n";
