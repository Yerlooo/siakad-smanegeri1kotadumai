<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'nip',
        'no_telepon',
        'alamat',
        'foto',
        'jenis_kelamin',
        'tanggal_lahir',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function kelasAsWali()
    {
        return $this->hasMany(Kelas::class, 'wali_kelas_id');
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class, 'guru_id');
    }

    // Helper methods
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    public function isKepalaSekolah()
    {
        return $this->hasRole('kepala_tatausaha');
    }

    public function isTataUsaha()
    {
        return $this->hasRole('tata_usaha');
    }

    public function isGuru()
    {
        return $this->hasRole('guru');
    }

    public function isSiswa()
    {
        return $this->hasRole('siswa');
    }

    public function isMurid()
    {
        return $this->hasRole('murid');
    }

    /**
     * Check if user is a wali kelas (homeroom teacher)
     */
    public function isWaliKelas()
    {
        return $this->isGuru() && $this->kelasAsWali()->exists();
    }

    /**
     * Get all classes where this user is assigned as wali kelas
     */
    public function getWaliKelasClasses()
    {
        return $this->kelasAsWali()->with(['siswa', 'jadwalPelajaran.mataPelajaran'])->get();
    }

    /**
     * Check if user can access specific kelas as wali kelas
     */
    public function canAccessAsWaliKelas($kelasId)
    {
        return $this->isWaliKelas() && $this->kelasAsWali()->where('id', $kelasId)->exists();
    }

    /**
     * Get siswa IDs from classes where user is wali kelas
     */
    public function getWaliKelasSiswaIds()
    {
        if (!$this->isWaliKelas()) {
            return collect([]);
        }

        return $this->kelasAsWali()
            ->with('siswa')
            ->get()
            ->pluck('siswa')
            ->flatten()
            ->pluck('id');
    }

    /**
     * Check if user has extended privileges as wali kelas for a specific student
     */
    public function hasWaliKelasPrivilegeForSiswa($siswaId)
    {
        return $this->getWaliKelasSiswaIds()->contains($siswaId);
    }

    // Relasi ke model Siswa
    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}
