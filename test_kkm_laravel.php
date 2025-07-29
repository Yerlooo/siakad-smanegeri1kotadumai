<?php

// Test KKM functionality using Laravel's bootstrap
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\KkmMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;

echo "Testing KKM functionality with Kelas...\n";

// Get first mata pelajaran and kelas
$mp = MataPelajaran::first();
$kelas = Kelas::first();

if ($mp && $kelas) {
    echo "Using mata pelajaran: {$mp->nama_mapel}\n";
    echo "Using kelas: {$kelas->nama_kelas}\n";
    
    try {
        // Check if KKM already exists
        $existing = KkmMataPelajaran::where('mata_pelajaran_id', $mp->id)
            ->where('kelas_id', $kelas->id)
            ->where('semester', 'ganjil')
            ->where('tahun_ajaran', '2024/2025')
            ->first();
            
        if (!$existing) {
            echo "Creating new KKM...\n";
            
            $kkm = KkmMataPelajaran::create([
                'mata_pelajaran_id' => $mp->id,
                'kelas_id' => $kelas->id,
                'kkm' => 75.50,
                'semester' => 'ganjil',
                'tahun_ajaran' => '2024/2025'
            ]);
            
            echo "✅ KKM created successfully with ID: {$kkm->id}\n";
            echo "   Mata Pelajaran: {$mp->nama_mapel}\n";
            echo "   Kelas: {$kelas->nama_kelas}\n";
            echo "   KKM Value: {$kkm->kkm}\n";
            echo "   Semester: {$kkm->semester}\n";
            echo "   Tahun Ajaran: {$kkm->tahun_ajaran}\n";
            
        } else {
            echo "KKM already exists:\n";
            echo "   ID: {$existing->id}\n";
            echo "   Mata Pelajaran: {$mp->nama_mapel}\n";
            echo "   Kelas: {$kelas->nama_kelas}\n";
            echo "   KKM Value: {$existing->kkm}\n";
        }
        
        // Count total KKM records
        $totalKkm = KkmMataPelajaran::whereNotNull('kelas_id')->count();
        echo "\nTotal KKM records with kelas: {$totalKkm}\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    }
} else {
    if (!$mp) echo "❌ No mata pelajaran found in database\n";
    if (!$kelas) echo "❌ No kelas found in database\n";
}
