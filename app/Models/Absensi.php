<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'siswa_id',
        'tanggal',
        'status', // hadir, sakit, izin, alpha
        'keterangan',
        'jam_masuk',
        'jam_keluar',
        'mata_pelajaran_id',
        'guru_id',
        'semester',
        'tahun_ajaran'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam_masuk' => 'datetime',
        'jam_keluar' => 'datetime'
    ];

    // Konstanta status absensi
    const STATUS_HADIR = 'hadir';
    const STATUS_SAKIT = 'sakit';
    const STATUS_IZIN = 'izin';
    const STATUS_ALPHA = 'alpha';

    // Relasi ke model Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    // Relasi ke model MataPelajaran
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    // Relasi ke model User (Guru)
    public function guru()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByTanggal($query, $tanggal)
    {
        return $query->where('tanggal', $tanggal);
    }

    // Scope untuk filter berdasarkan bulan
    public function scopeByBulan($query, $bulan, $tahun)
    {
        return $query->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun);
    }

    // Scope untuk filter berdasarkan semester
    public function scopeBySemester($query, $semester, $tahunAjaran)
    {
        return $query->where('semester', $semester)
                    ->where('tahun_ajaran', $tahunAjaran);
    }

    // Accessor untuk format tanggal Indonesia
    public function getTanggalIndonesiaAttribute()
    {
        return $this->tanggal->format('d/m/Y');
    }

    // Accessor untuk format jam masuk
    public function getJamMasukFormatAttribute()
    {
        return $this->jam_masuk ? $this->jam_masuk->format('H:i') : '-';
    }

    // Accessor untuk format jam keluar
    public function getJamKeluarFormatAttribute()
    {
        return $this->jam_keluar ? $this->jam_keluar->format('H:i') : '-';
    }

    // Method untuk mendapatkan warna status
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case self::STATUS_HADIR:
                return 'text-green-600 bg-green-100';
            case self::STATUS_SAKIT:
                return 'text-yellow-600 bg-yellow-100';
            case self::STATUS_IZIN:
                return 'text-blue-600 bg-blue-100';
            case self::STATUS_ALPHA:
                return 'text-red-600 bg-red-100';
            default:
                return 'text-gray-600 bg-gray-100';
        }
    }

    // Static method untuk mendapatkan daftar status
    public static function getStatusOptions()
    {
        return [
            self::STATUS_HADIR => 'Hadir',
            self::STATUS_SAKIT => 'Sakit',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_ALPHA => 'Alpha'
        ];
    }

    // Method untuk menghitung total kehadiran siswa
    public static function hitungKehadiran($siswaId, $semester, $tahunAjaran)
    {
        $total = self::where('siswa_id', $siswaId)
                    ->bySemester($semester, $tahunAjaran)
                    ->count();

        $hadir = self::where('siswa_id', $siswaId)
                    ->bySemester($semester, $tahunAjaran)
                    ->where('status', self::STATUS_HADIR)
                    ->count();

        $sakit = self::where('siswa_id', $siswaId)
                    ->bySemester($semester, $tahunAjaran)
                    ->where('status', self::STATUS_SAKIT)
                    ->count();

        $izin = self::where('siswa_id', $siswaId)
                    ->bySemester($semester, $tahunAjaran)
                    ->where('status', self::STATUS_IZIN)
                    ->count();

        $alpha = self::where('siswa_id', $siswaId)
                    ->bySemester($semester, $tahunAjaran)
                    ->where('status', self::STATUS_ALPHA)
                    ->count();

        return [
            'total' => $total,
            'hadir' => $hadir,
            'sakit' => $sakit,
            'izin' => $izin,
            'alpha' => $alpha,
            'persentase_hadir' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0
        ];
    }
}
