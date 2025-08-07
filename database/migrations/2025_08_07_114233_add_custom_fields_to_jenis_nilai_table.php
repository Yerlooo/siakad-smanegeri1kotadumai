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
        Schema::table('jenis_nilai', function (Blueprint $table) {
            // Tambah kolom untuk custom jenis nilai per guru/mata pelajaran/kelas
            $table->foreignId('guru_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            $table->foreignId('mata_pelajaran_id')->nullable()->after('guru_id')->constrained('mata_pelajaran')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->after('mata_pelajaran_id')->constrained('kelas')->onDelete('cascade');
            
            // Modifikasi kategori untuk lebih fleksibel (drop enum, buat string)
            $table->string('kategori_baru', 50)->default('lainnya')->after('nama');
            
            // Index untuk performa query
            $table->index(['guru_id', 'mata_pelajaran_id', 'kelas_id']);
            $table->index(['status']);
        });
        
        // Update kategori_baru berdasarkan kategori lama
        DB::statement("UPDATE jenis_nilai SET kategori_baru = kategori");
        
        // Drop kolom kategori lama dan rename kategori_baru
        Schema::table('jenis_nilai', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
        
        Schema::table('jenis_nilai', function (Blueprint $table) {
            $table->renameColumn('kategori_baru', 'kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jenis_nilai', function (Blueprint $table) {
            // Kembalikan kategori ke enum
            $table->enum('kategori_enum', ['pengetahuan', 'keterampilan', 'sikap'])->after('nama');
        });
        
        // Copy data kategori ke enum
        DB::statement("UPDATE jenis_nilai SET kategori_enum = CASE 
            WHEN kategori IN ('pengetahuan', 'keterampilan', 'sikap') THEN kategori 
            ELSE 'pengetahuan' 
            END");
        
        Schema::table('jenis_nilai', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['guru_id', 'mata_pelajaran_id', 'kelas_id']);
            $table->dropIndex(['status']);
            
            // Drop foreign keys
            $table->dropForeign(['guru_id']);
            $table->dropForeign(['mata_pelajaran_id']);
            $table->dropForeign(['kelas_id']);
            
            // Drop columns
            $table->dropColumn(['guru_id', 'mata_pelajaran_id', 'kelas_id', 'kategori']);
        });
        
        Schema::table('jenis_nilai', function (Blueprint $table) {
            $table->renameColumn('kategori_enum', 'kategori');
        });
    }
};
