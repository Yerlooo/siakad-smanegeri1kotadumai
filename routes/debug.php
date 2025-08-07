<?php

use App\Models\JenisNilai;
use App\Models\NilaiSiswa;

Route::get('/debug/jenis-nilai', function() {
    $custom = JenisNilai::whereNotNull('guru_id')->get();
    $result = [];
    
    foreach($custom as $j) {
        $nilaiCount = NilaiSiswa::where('jenis_nilai_id', $j->id)->count();
        $result[] = [
            'id' => $j->id,
            'nama' => $j->nama,
            'guru_id' => $j->guru_id,
            'status' => $j->status,
            'nilai_siswa_count' => $nilaiCount
        ];
    }
    
    return response()->json([
        'custom_jenis_nilai' => $result,
        'total' => count($result)
    ]);
});
