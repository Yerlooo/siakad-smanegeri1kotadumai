<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KkmMataPelajaran extends Model
{
    protected $table = 'kkm_mata_pelajaran';
    
    protected $fillable = [
        'mata_pelajaran_id',
        'kkm',
        'semester',
        'tahun_ajaran'
    ];

    protected function casts(): array
    {
        return [
            'kkm' => 'decimal:2',
        ];
    }

    // Relationships
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    // Scopes
    public function scopeBySemester($query, $semester, $tahunAjaran)
    {
        return $query->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran);
    }

    public function scopeByMapel($query, $mataPelajaranId)
    {
        return $query->where('mata_pelajaran_id', $mataPelajaranId);
    }
}
