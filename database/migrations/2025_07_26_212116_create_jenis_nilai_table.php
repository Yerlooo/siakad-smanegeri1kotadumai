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
        Schema::create('jenis_nilai', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100); // 'Tugas Harian', 'UTS', 'UAS', 'Praktik'
            $table->enum('kategori', ['pengetahuan', 'keterampilan', 'sikap']);
            $table->decimal('bobot', 5, 2); // Persentase bobot (20.00)
            $table->text('deskripsi')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_nilai');
    }
};
