<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Set up database connection similar to Laravel
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Test inserting KKM data
try {
    // Check current KKM data
    echo "Current KKM data:\n";
    $kkms = Capsule::table('kkm_mata_pelajaran')->get();
    echo "Total records: " . $kkms->count() . "\n";
    
    // Check mata pelajaran data
    echo "\nAvailable mata pelajaran:\n";
    $mataPelajaran = Capsule::table('mata_pelajaran')->limit(3)->get();
    foreach ($mataPelajaran as $mp) {
        echo "ID: {$mp->id}, Nama: {$mp->nama_mapel}\n";
    }
    
    // Test insert
    if ($mataPelajaran->count() > 0) {
        $testMp = $mataPelajaran->first();
        
        // Check if already exists
        $existing = Capsule::table('kkm_mata_pelajaran')
            ->where('mata_pelajaran_id', $testMp->id)
            ->where('semester', 'ganjil')
            ->where('tahun_ajaran', '2024/2025')
            ->whereNull('kelas_id')
            ->first();
            
        if (!$existing) {
            echo "\nTesting KKM insert for mata pelajaran: {$testMp->nama_mapel}\n";
            
            $inserted = Capsule::table('kkm_mata_pelajaran')->insert([
                'mata_pelajaran_id' => $testMp->id,
                'kelas_id' => null,
                'kkm' => 75.00,
                'semester' => 'ganjil',
                'tahun_ajaran' => '2024/2025',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($inserted) {
                echo "✅ KKM successfully inserted!\n";
                
                // Verify
                $newRecord = Capsule::table('kkm_mata_pelajaran')
                    ->where('mata_pelajaran_id', $testMp->id)
                    ->where('semester', 'ganjil')
                    ->where('tahun_ajaran', '2024/2025')
                    ->whereNull('kelas_id')
                    ->first();
                    
                if ($newRecord) {
                    echo "✅ Record verified: ID={$newRecord->id}, KKM={$newRecord->kkm}\n";
                } else {
                    echo "❌ Record not found after insert\n";
                }
            } else {
                echo "❌ Insert failed\n";
            }
        } else {
            echo "\nKKM already exists for {$testMp->nama_mapel}: ID={$existing->id}, KKM={$existing->kkm}\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
