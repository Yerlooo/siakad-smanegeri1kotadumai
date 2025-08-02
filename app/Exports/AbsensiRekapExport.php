<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class AbsensiRekapExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function array(): array
    {
        $rows = [];
        
        // Header information
        $rows[] = ['REKAP ABSENSI SISWA'];
        $rows[] = ['Kelas', $this->data['kelas']->nama_kelas];
        $rows[] = ['Mata Pelajaran', $this->data['mataPelajaran'] ? $this->data['mataPelajaran']->nama_mapel : 'Semua Mata Pelajaran'];
        $rows[] = ['Periode', ($this->data['startDate'] ? $this->data['startDate'] : '-') . ' s/d ' . ($this->data['endDate'] ? $this->data['endDate'] : '-')];
        $rows[] = ['Total Pertemuan Terlaksana', $this->data['statistics']['completedMeetings']];
        $rows[] = ['Rata-rata Kehadiran Kelas', $this->data['statistics']['classAverageAttendance'] . '%'];
        $rows[] = []; // Empty row

        // Table headers
        $headers = ['No', 'NIS', 'Nama Siswa'];
        
        // Add meeting columns (up to 16)
        for ($i = 1; $i <= 16; $i++) {
            $meetingDate = isset($this->data['meetingDates'][$i-1]) 
                ? \Carbon\Carbon::parse($this->data['meetingDates'][$i-1]['tanggal'])->format('d/m') 
                : '-';
            $headers[] = "P{$i}\n({$meetingDate})";
        }
        
        $headers[] = 'Total Hadir';
        $headers[] = 'Total Pertemuan';
        $headers[] = 'Persentase';
        
        $rows[] = $headers;

        // Student data
        foreach ($this->data['studentData'] as $index => $student) {
            $row = [
                $index + 1,
                $student['nis'],
                $student['nama_lengkap']
            ];
            
            // Add attendance for each meeting
            for ($i = 1; $i <= 16; $i++) {
                $status = isset($student['attendance'][$i]) ? $student['attendance'][$i] : '';
                $symbol = $this->getAttendanceSymbol($status);
                $row[] = $symbol;
            }
            
            $row[] = $student['totalHadir'];
            $row[] = $student['totalPertemuan'];
            $row[] = $student['percentage'] . '%';
            
            $rows[] = $row;
        }

        // Summary row
        $rows[] = []; // Empty row
        $summaryRow = ['', '', 'RINGKASAN'];
        for ($i = 1; $i <= 16; $i++) {
            $summaryRow[] = '';
        }
        $summaryRow[] = 'Total Siswa: ' . $this->data['statistics']['totalStudents'];
        $summaryRow[] = 'Rata-rata: ' . $this->data['statistics']['classAverageAttendance'] . '%';
        $summaryRow[] = '';
        $rows[] = $summaryRow;

        return $rows;
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Rekap Absensi';
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 5,  // No
            'B' => 12, // NIS
            'C' => 25, // Nama
        ];
        
        // Meeting columns
        for ($i = 4; $i <= 19; $i++) { // D to S (16 columns)
            $widths[chr(64 + $i)] = 8;
        }
        
        $widths['T'] = 12; // Total Hadir
        $widths['U'] = 15; // Total Pertemuan
        $widths['V'] = 12; // Persentase
        
        return $widths;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header title
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
            ],
            
            // Info rows
            '2:6' => [
                'font' => ['bold' => true],
            ],
            
            // Table header
            8 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Merge title cell
                $sheet->mergeCells('A1:V1');
                
                // Add borders to data table
                $lastRow = count($this->data['studentData']) + 8;
                $sheet->getStyle("A8:V{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);
                
                // Center align attendance columns
                $sheet->getStyle("D8:S{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align percentage column
                $sheet->getStyle("V8:V{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Wrap text for header
                $sheet->getStyle('A8:V8')
                    ->getAlignment()
                    ->setWrapText(true);
                
                // Set row height for header
                $sheet->getRowDimension(8)->setRowHeight(40);
            },
        ];
    }

    private function getAttendanceSymbol($status)
    {
        $symbols = [
            'hadir' => 'H',
            'sakit' => 'S',
            'izin' => 'I',
            'alpha' => 'A'
        ];
        
        return $symbols[$status] ?? '';
    }
}
