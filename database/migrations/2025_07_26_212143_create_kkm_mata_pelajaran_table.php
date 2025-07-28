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
        Schema::dropIfExists('kkm_mata_pelajaran');
    }
};
