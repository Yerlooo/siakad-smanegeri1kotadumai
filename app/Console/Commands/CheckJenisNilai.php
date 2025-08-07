<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\JenisNilai;

class CheckJenisNilai extends Command
{
    protected $signature = 'debug:jenis-nilai {id?}';
    protected $description = 'Check jenis nilai data';

    public function handle()
    {
        $id = $this->argument('id') ?? 3;
        
        $jenis = JenisNilai::find($id);
        
        if ($jenis) {
            $this->info("ID: " . $jenis->id);
            $this->info("Nama: " . $jenis->nama);
            $this->info("Guru ID: " . ($jenis->guru_id ?? 'null'));
            $this->info("Status: " . ($jenis->status ? 'aktif' : 'nonaktif'));
            $this->info("Mata Pelajaran ID: " . ($jenis->mata_pelajaran_id ?? 'null'));
            $this->info("Kelas ID: " . ($jenis->kelas_id ?? 'null'));
        } else {
            $this->error("Jenis nilai dengan ID {$id} tidak ditemukan");
        }
        
        $this->info("\n=== SEMUA JENIS NILAI ===");
        $allJenis = JenisNilai::all();
        foreach ($allJenis as $j) {
            $this->line("ID: {$j->id}, Nama: {$j->nama}, Guru ID: " . ($j->guru_id ?? 'null') . ", Status: " . ($j->status ? 'aktif' : 'nonaktif'));
        }
    }
}
