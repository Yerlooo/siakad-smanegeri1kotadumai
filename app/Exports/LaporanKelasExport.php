<?php

namespace App\Exports;

use App\Models\Kelas;
use App\Models\NilaiSiswa;
use App\Models\Absensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKelasExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $kelasId;

    public function __construct($kelasId)
    {
        $this->kelasId = $kelasId;
    }

    public function collection()
    {
        $kelas = Kelas::with(['siswa'])->findOrFail($this->kelasId);
        
        return $kelas->siswa->map(function ($siswa) {
            $rataRata = round(NilaiSiswa::where('siswa_id', $siswa->id)->avg('nilai') ?? 0, 2);
            $totalAbsensi = Absensi::where('siswa_id', $siswa->id)->count();
            $hadir = Absensi::where('siswa_id', $siswa->id)->where('status', 'hadir')->count();
            $kehadiran = $totalAbsensi > 0 ? round(($hadir / $totalAbsensi) * 100, 2) : 0;
            
            return [
                'siswa' => $siswa,
                'rata_rata' => $rataRata,
                'kehadiran' => $kehadiran,
                'status' => $this->getStatus($rataRata)
            ];
        })->sortByDesc('rata_rata')->values();
    }

    public function headings(): array
    {
        return [
            'No',
            'NISN',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Rata-rata Nilai',
            'Kehadiran (%)',
            'Status',
            'Keterangan'
        ];
    }

    public function map($row): array
    {
        static $no = 1;
        
        return [
            $no++,
            $row['siswa']->nisn,
            $row['siswa']->nama_lengkap,
            $row['siswa']->jenis_kelamin,
            $row['rata_rata'],
            $row['kehadiran'] . '%',
            $row['status'],
            $this->getKeterangan($row['rata_rata'], $row['kehadiran'])
        ];
    }

    public function title(): string
    {
        $kelas = Kelas::find($this->kelasId);
        return 'Laporan ' . $kelas->nama_kelas;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
            'A:H' => ['alignment' => ['horizontal' => 'center']],
        ];
    }

    private function getStatus($rataRata)
    {
        if ($rataRata >= 85) return 'Berprestasi';
        if ($rataRata >= 75) return 'Baik';
        if ($rataRata >= 65) return 'Cukup';
        return 'Perlu Perhatian';
    }

    private function getKeterangan($rataRata, $kehadiran)
    {
        if ($rataRata >= 85 && $kehadiran >= 90) return 'Sangat Baik';
        if ($rataRata >= 75 && $kehadiran >= 80) return 'Baik';
        if ($rataRata < 65 || $kehadiran < 70) return 'Memerlukan Bimbingan Khusus';
        return 'Standar';
    }
}
