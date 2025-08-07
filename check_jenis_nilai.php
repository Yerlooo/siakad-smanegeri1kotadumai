<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';

use App\Models\JenisNilai;

$jenis = JenisNilai::find(3);

if ($jenis) {
    echo "ID: " . $jenis->id . "\n";
    echo "Nama: " . $jenis->nama . "\n";
    echo "Guru ID: " . ($jenis->guru_id ?? 'null') . "\n";
    echo "Status: " . ($jenis->status ? 'aktif' : 'nonaktif') . "\n";
    echo "Mata Pelajaran ID: " . ($jenis->mata_pelajaran_id ?? 'null') . "\n";
    echo "Kelas ID: " . ($jenis->kelas_id ?? 'null') . "\n";
} else {
    echo "Jenis nilai dengan ID 3 tidak ditemukan\n";
}

// Cek semua jenis nilai
echo "\n=== SEMUA JENIS NILAI ===\n";
$allJenis = JenisNilai::all();
foreach ($allJenis as $j) {
    echo "ID: {$j->id}, Nama: {$j->nama}, Guru ID: " . ($j->guru_id ?? 'null') . ", Status: " . ($j->status ? 'aktif' : 'nonaktif') . "\n";
}
