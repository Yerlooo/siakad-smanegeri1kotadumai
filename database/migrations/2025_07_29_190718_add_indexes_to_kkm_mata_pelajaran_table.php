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
        Schema::table('kkm_mata_pelajaran', function (Blueprint $table) {
            // Add composite index for filtering by semester and tahun_ajaran
            $table->index(['semester', 'tahun_ajaran'], 'idx_kkm_semester_tahun');
            
            // Add composite index for unique combination
            $table->index(['mata_pelajaran_id', 'kelas_id', 'semester', 'tahun_ajaran'], 'idx_kkm_unique_combination');
            
            // Add individual indexes for foreign keys if not exists
            if (!Schema::hasIndex('kkm_mata_pelajaran', 'kkm_mata_pelajaran_mata_pelajaran_id_index')) {
                $table->index('mata_pelajaran_id');
            }
            
            if (!Schema::hasIndex('kkm_mata_pelajaran', 'kkm_mata_pelajaran_kelas_id_index')) {
                $table->index('kelas_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kkm_mata_pelajaran', function (Blueprint $table) {
            // Drop composite indexes
            $table->dropIndex('idx_kkm_semester_tahun');
            $table->dropIndex('idx_kkm_unique_combination');
        });
    }
};
