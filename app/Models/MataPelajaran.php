<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    protected $table = 'mata_pelajaran';
    
    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'deskripsi',
        'jam_pelajaran'
    ];

    protected $casts = [
        'status' => 'boolean',
        'jam_tatap_muka' => 'integer',
        'sks' => 'integer',
        'kkm' => 'integer'
    ];

    // Relationships
    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    // Helper methods
    public function getKategoriLabelAttribute()
    {
        $labels = [
            'wajib' => 'Mata Pelajaran Wajib',
            'peminatan' => 'Mata Pelajaran Peminatan',
            'lintas_minat' => 'Lintas Minat',
            'pendalaman_minat' => 'Pendalaman Minat',
            'muatan_lokal' => 'Muatan Lokal'
        ];
        
        return $labels[$this->kategori] ?? $this->kategori;
    }

    public function getJenisLabelAttribute()
    {
        $labels = [
            'teori' => 'Teori',
            'praktik' => 'Praktik',
            'teori_praktik' => 'Teori dan Praktik'
        ];
        
        return $labels[$this->jenis] ?? $this->jenis;
    }
}
