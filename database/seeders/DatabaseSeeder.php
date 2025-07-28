<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            MataPelajaranSeeder::class,
            JenisNilaiSeeder::class,
        ]);

        // Create default users
        $kepalaSekolahRole = \App\Models\Role::where('name', 'kepala_tatausaha')->first();
        $tataUsahaRole = \App\Models\Role::where('name', 'tata_usaha')->first();
        $guruRole = \App\Models\Role::where('name', 'guru')->first();

        User::create([
            'name' => 'Kepala Tata Usaha',
            'email' => 'kepala@smanegeri1kotadumai.sch.id',
            'password' => bcrypt('password'),
            'role_id' => $kepalaSekolahRole->id,
            'nip' => '198501012010011001',
        ]);

        User::create([
            'name' => 'Tata Usaha',
            'email' => 'tatausaha@smanegeri1kotadumai.sch.id',
            'password' => bcrypt('password'),
            'role_id' => $tataUsahaRole->id,
            'nip' => '198502012010012002',
        ]);

        User::create([
            'name' => 'Guru Matematika',
            'email' => 'guru@smanegeri1kotadumai.sch.id',
            'password' => bcrypt('password'),
            'role_id' => $guruRole->id,
            'nip' => '198503012010013003',
        ]);
    }
}
