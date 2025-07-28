<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'kepala_tatausaha',
                'display_name' => 'Kepala Tata Usaha',
                'description' => 'Akses penuh ke seluruh sistem',
            ],
            [
                'name' => 'tata_usaha',
                'display_name' => 'Tata Usaha',
                'description' => 'Akses terbatas ke beberapa menu sistem',
            ],
            [
                'name' => 'guru',
                'display_name' => 'Guru',
                'description' => 'Akses ke dashboard, data siswa, jadwal, kelas, mata pelajaran, dan pengaturan',
            ],
            [
                'name' => 'murid',
                'display_name' => 'Murid',
                'description' => 'Akses terbatas untuk melihat jadwal, tugas, dan nilai',
            ],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
