<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisNilai extends Model
{
    protected $table = 'jenis_nilai';
    
    protected $fillable = [
        'nama',
        'kategori',
        'bobot',
        'deskripsi',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'bobot' => 'decimal:2',
            'status' => 'boolean',
        ];
    }

    // Relationships
    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class);
    }

    // Helper methods
    public function getKategoriLabelAttribute()
    {
        $labels = [
            'pengetahuan' => 'Pengetahuan (Kognitif)',
            'keterampilan' => 'Keterampilan (Psikomotorik)',
            'sikap' => 'Sikap (Afektif)'
        ];
        
        return $labels[$this->kategori] ?? $this->kategori;
    }

    // Scopes
    public function scopeAktif($query)
    {
        return $query->where('status', true);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }
}
