<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\User;
use App\Models\MataPelajaran;
use Illuminate\Support\Facades\DB;

class DebugDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== DEBUG DATA ===');
        
        // Cek tabel siswa
        $siswaCount = Siswa::count();
        $this->command->info("Total Siswa: {$siswaCount}");
        
        if ($siswaCount > 0) {
            $sampleSiswa = Siswa::first();
            $this->command->info("Sample Siswa ID: {$sampleSiswa->id}");
            $this->command->info("Sample Siswa Name: {$sampleSiswa->nama_lengkap}");
        }
        
        // Cek tabel users dengan role
        $userCount = User::count();
        $this->command->info("Total Users: {$userCount}");
        
        // Cek role table
        $roleCount = DB::table('roles')->count();
        $this->command->info("Total Roles: {$roleCount}");
        
        if ($roleCount > 0) {
            $roles = DB::table('roles')->get();
            foreach ($roles as $role) {
                $userWithRole = User::where('role_id', $role->id)->count();
                $this->command->info("Role {$role->name}: {$userWithRole} users");
            }
        }
        
        // Cek mata pelajaran
        $mataPelajaranCount = MataPelajaran::count();
        $this->command->info("Total Mata Pelajaran: {$mataPelajaranCount}");
        
        // Cek foreign key constraints
        $constraints = DB::select("
            SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'absensi' 
            AND TABLE_SCHEMA = DATABASE()
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");
        
        $this->command->info('=== FOREIGN KEY CONSTRAINTS ===');
        foreach ($constraints as $constraint) {
            $this->command->info("{$constraint->CONSTRAINT_NAME}: {$constraint->COLUMN_NAME} -> {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME}");
        }
    }
}
