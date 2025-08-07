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
        'status',
        'guru_id',
        'mata_pelajaran_id',
        'kelas_id'
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

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
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
