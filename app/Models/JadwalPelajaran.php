<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    protected $table = 'jadwal_pelajaran';
    
    protected $fillable = [
        'mata_pelajaran_id',
        'kelas_id',
        'guru_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'semester',
        'tahun_ajaran',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'jam_mulai' => 'datetime:H:i',
            'jam_selesai' => 'datetime:H:i',
            'status' => 'boolean',
        ];
    }

    // Relationships
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Method untuk mendapatkan absensi berdasarkan tanggal tertentu
    public function getAbsensiByDate($tanggal)
    {
        $siswaIds = Siswa::where('kelas_id', $this->kelas_id)
                        ->where('status', 'aktif')
                        ->pluck('id');
                        
        return Absensi::where('mata_pelajaran_id', $this->mata_pelajaran_id)
                     ->where('tanggal', $tanggal)
                     ->whereIn('siswa_id', $siswaIds)
                     ->with('siswa:id,nama_lengkap,nis')
                     ->get();
    }
}
