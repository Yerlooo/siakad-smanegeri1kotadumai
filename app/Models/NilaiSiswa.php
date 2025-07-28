<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiSiswa extends Model
{
    protected $table = 'nilai_siswa';
    
    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'jenis_nilai_id',
        'nilai',
        'tanggal_input',
        'semester',
        'tahun_ajaran',
        'keterangan',
        'guru_id',
        'status'
    ];

    protected function casts(): array
    {
        return [
            'nilai' => 'decimal:2',
            'tanggal_input' => 'date',
        ];
    }

    // Relationships
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function jenisNilai()
    {
        return $this->belongsTo(JenisNilai::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Helper methods
    public function getPredikatAttribute()
    {
        $kkm = $this->getKkm();
        return $this->convertToPredikat($this->nilai, $kkm);
    }

    public function getKkm()
    {
        return KkmMataPelajaran::where('mata_pelajaran_id', $this->mata_pelajaran_id)
            ->where('semester', $this->semester)
            ->where('tahun_ajaran', $this->tahun_ajaran)
            ->value('kkm') ?? 75;
    }

    public function isTuntas()
    {
        return $this->nilai >= $this->getKkm();
    }

    // Static helper method
    public static function convertToPredikat($nilai, $kkm = 75)
    {
        if ($nilai >= 90) return ['huruf' => 'A', 'predikat' => 'Sangat Baik'];
        if ($nilai >= 80) return ['huruf' => 'B+', 'predikat' => 'Baik'];
        if ($nilai >= $kkm) return ['huruf' => 'B', 'predikat' => 'Baik'];
        if ($nilai >= $kkm - 10) return ['huruf' => 'C+', 'predikat' => 'Cukup'];
        if ($nilai >= $kkm - 20) return ['huruf' => 'C', 'predikat' => 'Cukup'];
        return ['huruf' => 'D', 'predikat' => 'Kurang'];
    }

    // Scopes
    public function scopeByGuru($query, $guruId)
    {
        return $query->where('guru_id', $guruId);
    }

    public function scopeBySemester($query, $semester, $tahunAjaran)
    {
        return $query->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran);
    }

    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }
}
