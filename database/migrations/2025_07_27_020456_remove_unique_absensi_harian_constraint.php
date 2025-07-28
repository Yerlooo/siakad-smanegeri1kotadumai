<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // Hapus unique constraint untuk memungkinkan multiple absensi per hari
            $table->dropUnique('unique_absensi_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi', function (Blueprint $table) {
            // Kembalikan unique constraint jika rollback
            $table->unique(['siswa_id', 'tanggal', 'mata_pelajaran_id'], 'unique_absensi_harian');
        });
    }
};
