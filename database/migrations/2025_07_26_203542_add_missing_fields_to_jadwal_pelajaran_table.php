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
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->enum('semester', ['ganjil', 'genap'])->after('ruangan');
            $table->string('tahun_ajaran')->after('semester');
            $table->boolean('status')->default(true)->after('tahun_ajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jadwal_pelajaran', function (Blueprint $table) {
            $table->dropColumn(['semester', 'tahun_ajaran', 'status']);
        });
    }
};
