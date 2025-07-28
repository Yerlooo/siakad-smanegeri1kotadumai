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
        // Drop tables if exists (in correct order due to foreign keys)
        Schema::dropIfExists('nilai_siswa');
        Schema::dropIfExists('kkm_mata_pelajaran');
        
        // Create nilai_siswa table
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
            
            // Index untuk performa query
            $table->index(['siswa_id', 'mata_pelajaran_id', 'semester', 'tahun_ajaran'], 'idx_nilai_siswa_mapel_semester');
            $table->index(['guru_id', 'tanggal_input'], 'idx_nilai_guru_tanggal');
        });
        
        // Create kkm_mata_pelajaran table
        Schema::create('kkm_mata_pelajaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('cascade');
            $table->decimal('kkm', 5, 2); // Nilai KKM (75.00)
            $table->enum('semester', ['ganjil', 'genap']);
            $table->string('tahun_ajaran', 20);
            $table->timestamps();
            
            // Unique constraint untuk mencegah duplikasi
            $table->unique(['mata_pelajaran_id', 'kelas_id', 'semester', 'tahun_ajaran'], 'unique_kkm_per_mapel_kelas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_siswa');
        Schema::dropIfExists('kkm_mata_pelajaran');
    }
};
