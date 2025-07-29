<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;

$app = require_once 'bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

// Import models
use App\Models\KkmMataPelajaran;
use App\Models\MataPelajaran;
use App\Models\Kelas;

echo "=== Testing KKM Integration ===\n";

// Ambil sample mata pelajaran dan kelas
$mataPelajaran = MataPelajaran::first();
$kelas = Kelas::first();

if (!$mataPelajaran || !$kelas) {
    echo "Error: Tidak ada data mata pelajaran atau kelas!\n";
    exit;
}

echo "Mata Pelajaran: {$mataPelajaran->nama_mapel}\n";
echo "Kelas: {$kelas->nama_kelas}\n";

// Cek apakah ada KKM untuk kombinasi ini
$semester = 'ganjil';
$tahunAjaran = '2024/2025';

$kkm = KkmMataPelajaran::forInputNilai(
    $mataPelajaran->id,
    $kelas->id,
    $semester,
    $tahunAjaran
)->first();

if ($kkm) {
    echo "KKM ditemukan: {$kkm->kkm}\n";
} else {
    echo "KKM tidak ditemukan, membuat KKM baru...\n";
    
    // Buat KKM baru untuk testing
    $kkm = KkmMataPelajaran::create([
        'mata_pelajaran_id' => $mataPelajaran->id,
        'kelas_id' => $kelas->id,
        'kkm' => 78, // KKM testing
        'semester' => $semester,
        'tahun_ajaran' => $tahunAjaran
    ]);
    
    echo "KKM baru dibuat dengan nilai: {$kkm->kkm}\n";
}

// Test query yang sama seperti di controller
echo "\n=== Test Query Controller ===\n";
$kkmFromController = KkmMataPelajaran::where('mata_pelajaran_id', $mataPelajaran->id)
    ->where('kelas_id', $kelas->id)
    ->where('semester', $semester)
    ->where('tahun_ajaran', $tahunAjaran)
    ->first();

if ($kkmFromController) {
    echo "Query controller berhasil, KKM: {$kkmFromController->kkm}\n";
} else {
    echo "Query controller gagal!\n";
}

echo "\n=== Daftar KKM yang Ada ===\n";
$allKkm = KkmMataPelajaran::with(['mataPelajaran', 'kelas'])->take(5)->get();
foreach ($allKkm as $item) {
    echo "- KKM: {$item->kkm} | Mapel: {$item->mataPelajaran->nama_mapel} | Kelas: {$item->kelas->nama_kelas} | {$item->semester} {$item->tahun_ajaran}\n";
}

echo "\nIntegrasi KKM siap untuk testing!\n";
echo "Akses halaman input nilai untuk melihat KKM yang sudah diset.\n";
