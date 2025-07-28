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
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('alpha');
            $table->text('keterangan')->nullable();
            $table->timestamp('jam_masuk')->nullable();
            $table->timestamp('jam_keluar')->nullable();
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajaran')->onDelete('set null');
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->string('tahun_ajaran', 20)->default('2024/2025');
            $table->timestamps();

            // Index untuk performance
            $table->index(['siswa_id', 'tanggal']);
            $table->index(['tanggal', 'mata_pelajaran_id']);
            $table->index(['semester', 'tahun_ajaran']);
            
            // Unique constraint untuk mencegah duplikasi absensi per siswa per hari per mata pelajaran
            $table->unique(['siswa_id', 'tanggal', 'mata_pelajaran_id'], 'unique_absensi_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};