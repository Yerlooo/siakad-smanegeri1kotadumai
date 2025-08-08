<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AbsensiGuru extends Model
{
    use HasFactory;

    protected $table = 'absensi_guru';

    protected $fillable = [
        'user_id',
        'tanggal',
        'jenis_ketidakhadiran',
        'alasan',
        'keterangan',
        'status_laporan',
        'tanggal_diterima',
        'diterima_oleh'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_diterima' => 'datetime'
    ];

    /**
     * Relasi ke model User (guru)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model User yang menerima laporan
     */
    public function penerima()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    /**
     * Accessor untuk mendapatkan text jenis ketidakhadiran
     */
    public function getJenisKetidakhadiranTextAttribute()
    {
        $jenis = [
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'dinas' => 'Dinas Luar Kota',
            'cuti' => 'Cuti'
        ];

        return $jenis[$this->jenis_ketidakhadiran] ?? $this->jenis_ketidakhadiran;
    }

    /**
     * Accessor untuk mendapatkan text status laporan
     */
    public function getStatusLaporanTextAttribute()
    {
        $status = [
            'dilaporkan' => 'Telah Dilaporkan',
            'diterima' => 'Diterima'
        ];

        return $status[$this->status_laporan] ?? $this->status_laporan;
    }

    /**
     * Accessor untuk mendapatkan badge class berdasarkan status laporan
     */
    public function getStatusLaporanBadgeClassAttribute()
    {
        $classes = [
            'dilaporkan' => 'bg-blue-100 text-blue-800',
            'diterima' => 'bg-green-100 text-green-800'
        ];

        return $classes[$this->status_laporan] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Accessor untuk mendapatkan badge class berdasarkan jenis ketidakhadiran
     */
    public function getJenisBadgeClassAttribute()
    {
        $classes = [
            'sakit' => 'bg-red-100 text-red-800',
            'izin' => 'bg-blue-100 text-blue-800',
            'dinas' => 'bg-purple-100 text-purple-800',
            'cuti' => 'bg-green-100 text-green-800'
        ];

        return $classes[$this->jenis_ketidakhadiran] ?? 'bg-gray-100 text-gray-800';
    }

    /**
     * Scope untuk filter berdasarkan status laporan
     */
    public function scopeByStatusLaporan($query, $status)
    {
        return $query->where('status_laporan', $status);
    }

    /**
     * Scope untuk filter berdasarkan jenis ketidakhadiran
     */
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_ketidakhadiran', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan bulan
     */
    public function scopeByMonth($query, $month, $year = null)
    {
        $query->whereMonth('tanggal', $month);
        
        if ($year) {
            $query->whereYear('tanggal', $year);
        }
        
        return $query;
    }

    /**
     * Scope untuk filter berdasarkan tahun
     */
    public function scopeByYear($query, $year)
    {
        return $query->whereYear('tanggal', $year);
    }

    /**
     * Scope untuk filter berdasarkan rentang tanggal
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Static method untuk mendapatkan statistik absensi guru
     */
    public static function getStatistik($userId = null, $month = null, $year = null)
    {
        $query = self::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($month && $year) {
            $query->whereMonth('tanggal', $month)
                  ->whereYear('tanggal', $year);
        }

        return [
            'total' => $query->count(),
            'dilaporkan' => $query->clone()->where('status_laporan', 'dilaporkan')->count(),
            'diterima' => $query->clone()->where('status_laporan', 'diterima')->count(),
            'sakit' => $query->clone()->where('jenis_ketidakhadiran', 'sakit')->count(),
            'izin' => $query->clone()->where('jenis_ketidakhadiran', 'izin')->count(),
            'dinas' => $query->clone()->where('jenis_ketidakhadiran', 'dinas')->count(),
            'cuti' => $query->clone()->where('jenis_ketidakhadiran', 'cuti')->count()
        ];
    }
}
