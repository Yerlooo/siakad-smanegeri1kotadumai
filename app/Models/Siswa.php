<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    
    protected $fillable = [
        'user_id',
        'nis',
        'nisn',
        'nama_lengkap',
        'jenis_kelamin',
        'tanggal_lahir',
        'tempat_lahir',
        'agama',
        'alamat',
        'no_telepon',
        'email',
        'nama_ayah',
        'nama_ibu',
        'pekerjaan_ayah',
        'pekerjaan_ibu',
        'kelas_id',
        'tahun_masuk',
        'status',
        'foto',
    ];

    protected $appends = [
        'tanggal_lahir_formatted'
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'tahun_masuk' => 'integer',
        ];
    }

    // Accessor untuk memastikan tanggal_lahir dalam format yang tepat untuk frontend
    public function getTanggalLahirFormattedAttribute()
    {
        return $this->tanggal_lahir ? $this->tanggal_lahir->format('Y-m-d') : null;
    }

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function nilaiSiswa()
    {
        return $this->hasMany(NilaiSiswa::class);
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class);
    }

    // Helper methods
    public function getUmurAttribute()
    {
        return $this->tanggal_lahir->age;
    }

    public function getNamaAttribute()
    {
        return $this->nama_lengkap;
    }
}
