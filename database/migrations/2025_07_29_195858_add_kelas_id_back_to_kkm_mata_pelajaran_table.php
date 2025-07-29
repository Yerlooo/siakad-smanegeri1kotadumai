<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing constraints if they exist
        try {
            Schema::table('kkm_mata_pelajaran', function (Blueprint $table) {
                $table->dropUnique('unique_kkm_per_mapel_kelas');
            });
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Update kelas_id yang null dengan kelas pertama yang ada
        DB::statement("UPDATE kkm_mata_pelajaran SET kelas_id = (SELECT id FROM kelas LIMIT 1) WHERE kelas_id IS NULL OR kelas_id = 0");
        
        Schema::table('kkm_mata_pelajaran', function (Blueprint $table) {
            // Add foreign key constraint for kelas_id if it doesn't exist
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            
            // Add new unique constraint that includes kelas_id
            $table->unique(['mata_pelajaran_id', 'kelas_id', 'semester', 'tahun_ajaran'], 'kkm_mapel_kelas_semester_tahun_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kkm_mata_pelajaran', function (Blueprint $table) {
            // Drop the unique constraint
            $table->dropUnique('kkm_mapel_kelas_semester_tahun_unique');
            
            // Drop foreign key constraint
            $table->dropForeign(['kelas_id']);
            
            // Drop kelas_id column
            $table->dropColumn('kelas_id');
            
            // Re-add the old unique constraint without kelas_id
            $table->unique(['mata_pelajaran_id', 'semester', 'tahun_ajaran'], 'kkm_mapel_semester_tahun_unique');
        });
    }
};
