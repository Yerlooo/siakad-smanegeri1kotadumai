<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // School Information
            [
                'key' => 'school_name',
                'value' => 'SMA Negeri 1 Kota Dumai',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Nama sekolah'
            ],
            [
                'key' => 'school_npsn',
                'value' => '10404563',
                'type' => 'string',
                'group' => 'school',
                'description' => 'NPSN sekolah'
            ],
            [
                'key' => 'school_address',
                'value' => 'Jl. Pendidikan No. 1, Kota Dumai, Riau',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Alamat sekolah'
            ],
            [
                'key' => 'school_head',
                'value' => 'Dr. Siti Nurhaliza, S.Pd., M.Pd',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Kepala tata usaha'
            ],
            [
                'key' => 'school_phone',
                'value' => '(0765) 123456',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Telepon sekolah'
            ],
            [
                'key' => 'school_email',
                'value' => 'info@smanegeri1kotadumai.sch.id',
                'type' => 'string',
                'group' => 'school',
                'description' => 'Email sekolah'
            ],
            
            // Academic Information
            [
                'key' => 'current_year',
                'value' => '2024/2025',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Tahun ajaran aktif'
            ],
            [
                'key' => 'current_semester',
                'value' => 'ganjil',
                'type' => 'string',
                'group' => 'academic',
                'description' => 'Semester aktif'
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
