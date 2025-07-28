<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class NilaiSiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithTitle
{
    private $mataPelajaran;
    private $kelas;
    private $siswaList;
    private $nilaiSiswa;
    private $jenisNilai;

    public function __construct($mataPelajaran, $kelas, $siswaList, $nilaiSiswa, $jenisNilai)
    {
        $this->mataPelajaran = $mataPelajaran;
        $this->kelas = $kelas;
        $this->siswaList = $siswaList;
        $this->nilaiSiswa = $nilaiSiswa;
        $this->jenisNilai = $jenisNilai;
    }

    public function collection()
    {
        return collect($this->siswaList);
    }

    public function headings(): array
    {
        $headings = [
            'No',
            'NIS',
            'Nama Siswa',
        ];

        // Tambahkan heading untuk setiap jenis nilai
        foreach ($this->jenisNilai as $jenis) {
            $headings[] = $jenis->nama . ' (' . $jenis->bobot . '%)';
        }

        $headings[] = 'Rata-rata';
        $headings[] = 'Status';

        return $headings;
    }

    public function map($siswa): array
    {
        static $counter = 0;
        $counter++;
        
        $row = [
            $counter,
            $siswa->nis,
            $siswa->nama_lengkap,
        ];

        $totalNilai = 0;
        $totalBobot = 0;
        $hasNilai = false;

        // Tambahkan nilai untuk setiap jenis nilai
        foreach ($this->jenisNilai as $jenis) {
            $nilai = collect($this->nilaiSiswa)->first(function ($n) use ($siswa, $jenis) {
                return $n->siswa_id == $siswa->id && $n->jenis_nilai_id == $jenis->id;
            });

            if ($nilai) {
                $nilaiValue = $nilai->nilai;
                $row[] = $nilaiValue . ($nilai->status === 'draft' ? ' (Draft)' : '');
                
                if ($nilai->status === 'final') {
                    $totalNilai += $nilaiValue * ($jenis->bobot / 100);
                    $totalBobot += $jenis->bobot / 100;
                    $hasNilai = true;
                }
            } else {
                $row[] = '-';
            }
        }

        // Hitung rata-rata berbobot
        $rataRata = $totalBobot > 0 ? $totalNilai / $totalBobot : 0;
        $row[] = $hasNilai ? number_format($rataRata, 1) : '-';

        // Status
        $allFinal = true;
        foreach ($this->jenisNilai as $jenis) {
            $nilai = collect($this->nilaiSiswa)->first(function ($n) use ($siswa, $jenis) {
                return $n->siswa_id == $siswa->id && $n->jenis_nilai_id == $jenis->id && $n->status === 'final';
            });
            if (!$nilai) {
                $allFinal = false;
                break;
            }
        }

        $status = $hasNilai ? ($allFinal ? 'Lengkap' : 'Sebagian') : 'Belum Ada';
        $row[] = $status;

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = count($this->headings());
        $lastRow = count($this->siswaList) + 1;

        // Header row style
        $sheet->getStyle('A1:' . chr(64 + $lastColumn) . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '3B82F6'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        // Data rows style
        $sheet->getStyle('A2:' . chr(64 + $lastColumn) . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC'],
                ],
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ]);

        // Center align number columns
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Center align nilai columns (starting from column D)
        for ($i = 4; $i <= $lastColumn - 2; $i++) {
            $sheet->getStyle(chr(63 + $i) . '2:' . chr(63 + $i) . $lastRow)
                  ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }

    public function title(): string
    {
        return 'Nilai ' . $this->kelas->nama_kelas;
    }
}
