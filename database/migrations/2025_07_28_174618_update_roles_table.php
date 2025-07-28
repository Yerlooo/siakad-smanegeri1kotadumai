<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing kepala_sekolah role to kepala_tatausaha
        $kepalaSekolah = Role::where('name', 'kepala_sekolah')->first();
        if ($kepalaSekolah) {
            $kepalaSekolah->update([
                'name' => 'kepala_tatausaha',
                'display_name' => 'Kepala Tata Usaha',
                'description' => 'Akses penuh ke seluruh sistem',
            ]);
        }

        // Add new murid role if it doesn't exist
        $muridRole = Role::where('name', 'murid')->first();
        if (!$muridRole) {
            Role::create([
                'name' => 'murid',
                'display_name' => 'Murid',
                'description' => 'Akses terbatas untuk melihat jadwal, tugas, dan nilai',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert kepala_tatausaha back to kepala_sekolah
        $kepalaTataUsaha = Role::where('name', 'kepala_tatausaha')->first();
        if ($kepalaTataUsaha) {
            $kepalaTataUsaha->update([
                'name' => 'kepala_sekolah',
                'display_name' => 'Kepala Sekolah',
                'description' => 'Akses penuh ke seluruh sistem',
            ]);
        }

        // Remove murid role
        Role::where('name', 'murid')->delete();
    }
};
