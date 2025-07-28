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
        Schema::create('nilai_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('jenis_nilai_id')->constrained('jenis_nilai')->onDelete('cascade');
            $table->decimal('nilai', 5, 2); // Nilai angka (0-100)
            $table->date('tanggal_input');
            $table->enum('semester', ['ganjil', 'genap']);
            $table->string('tahun_ajaran', 20);
            $table->text('keterangan')->nullable();
            $table->foreignId('guru_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
            
            // Index untuk performa query dengan nama yang lebih pendek
            $table->index(['siswa_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran'], 'idx_nilai_siswa_main');
            $table->index(['guru_id', 'tanggal_input'], 'idx_nilai_guru_tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_siswa');
    }
};
