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
        Schema::table('absensi_guru', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['approved_by']);
            
            // Hapus kolom yang terkait dengan sistem persetujuan
            $table->dropColumn([
                'status',
                'approved_by',
                'approved_at',
                'catatan_admin'
            ]);
            
            // Tambahkan kolom untuk status sederhana (opsional)
            $table->enum('status_laporan', ['dilaporkan', 'diterima'])->default('dilaporkan')->after('keterangan');
            $table->timestamp('tanggal_diterima')->nullable()->after('status_laporan');
            $table->unsignedBigInteger('diterima_oleh')->nullable()->after('tanggal_diterima');
            
            // Foreign key untuk yang menerima laporan (kepala sekolah/tata usaha)
            $table->foreign('diterima_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('absensi_guru', function (Blueprint $table) {
            // Kembalikan kolom sistem persetujuan
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('keterangan');
            $table->text('catatan_admin')->nullable()->after('status');
            $table->unsignedBigInteger('approved_by')->nullable()->after('catatan_admin');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            
            // Hapus kolom baru
            $table->dropForeign(['diterima_oleh']);
            $table->dropColumn([
                'status_laporan',
                'tanggal_diterima',
                'diterima_oleh'
            ]);
            
            // Tambahkan kembali foreign key
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};