<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Rekap Absensi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 15mm;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10px;
            margin: 0;
            padding: 0;
            line-height: 1.2;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 16px;
            margin: 0 0 5px 0;
            font-weight: bold;
        }
        
        .header h2 {
            font-size: 14px;
            margin: 0;
            font-weight: normal;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            font-size: 9px;
        }
        
        .info-table td {
            padding: 3px 5px;
            border: none;
            vertical-align: top;
        }
        
        .info-table .label {
            font-weight: bold;
            width: 150px;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 8px;
        }
        
        .attendance-table th,
        .attendance-table td {
            border: 0.5px solid #333;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }
        
        .attendance-table th {
            background-color: #e5e5e5;
            font-weight: bold;
            font-size: 7px;
        }
        
        .student-name {
            text-align: left !important;
            font-weight: bold;
            padding-left: 5px !important;
        }
        
        .meeting-header {
            width: 18px;
            min-width: 18px;
            max-width: 18px;
            font-size: 6px;
            padding: 1px !important;
        }
        
        .meeting-date {
            font-size: 5px;
            font-weight: normal;
            margin-top: 2px;
        }
        
        .attendance-symbol {
            font-weight: bold;
            font-size: 8px;
        }
        
        .hadir { color: #059669; }
        .sakit { color: #d97706; }
        .izin { color: #dc2626; }
        .alpha { color: #991b1b; }
        
        .summary {
            margin-top: 15px;
            background-color: #f9f9f9;
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 9px;
        }
        
        .summary h3 {
            margin: 0 0 8px 0;
            font-size: 11px;
        }
        
        .summary-row {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-cell;
            text-align: center;
            width: 33.33%;
            padding: 5px;
        }
        
        .summary-item .label {
            font-weight: bold;
            display: block;
            margin-bottom: 3px;
        }
        
        .summary-item .value {
            font-size: 11px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .footer-info {
            margin-top: 20px;
            font-size: 8px;
        }
        
        .print-date {
            text-align: right;
            margin-bottom: 10px;
        }
        
        .legend {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP KEHADIRAN SISWA</h1>
        <h2>{{ $kelas->nama_kelas }}</h2>
    </div>
    
    <table class="info-table">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td>: {{ $mataPelajaran ? $mataPelajaran->nama_mapel : 'Semua Mata Pelajaran' }}</td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td>: {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d F Y') : '-' }} s/d {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d F Y') : '-' }}</td>
        </tr>
        <tr>
            <td class="label">Total Pertemuan Terlaksana</td>
            <td>: {{ $statistics['completedMeetings'] }} dari 16 pertemuan</td>
        </tr>
        <tr>
            <td class="label">Jumlah Siswa</td>
            <td>: {{ $statistics['totalStudents'] }} siswa</td>
        </tr>
        <tr>
            <td class="label">Rata-rata Kehadiran Kelas</td>
            <td>: {{ $statistics['classAverageAttendance'] }}%</td>
        </tr>
    </table>
    
    <table class="attendance-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 60px;">NIS</th>
                <th rowspan="2" style="width: 120px;">Nama Siswa</th>
                <th colspan="16" style="font-size: 8px;">Pertemuan</th>
                <th rowspan="2" style="width: 40px;">Total</th>
                <th rowspan="2" style="width: 35px;">%</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 16; $i++)
                    <th class="meeting-header">
                        <div>P{{ $i }}</div>
                        @if (isset($meetingDates[$i-1]))
                            <div class="meeting-date">{{ \Carbon\Carbon::parse($meetingDates[$i-1]['tanggal'])->format('d/m') }}</div>
                        @endif
                    </th>
                @endfor
            </tr>
        </thead>
        <tbody>
            @foreach ($studentData as $index => $student)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $student['nis'] }}</td>
                <td class="student-name">{{ $student['nama_lengkap'] }}</td>
                
                @for ($i = 1; $i <= 16; $i++)
                    <td>
                        @if (isset($student['attendance'][$i]))
                            @php
                                $status = $student['attendance'][$i];
                                $symbol = [
                                    'hadir' => 'H',
                                    'sakit' => 'S', 
                                    'izin' => 'I',
                                    'alpha' => 'A'
                                ][$status] ?? '';
                            @endphp
                            <span class="attendance-symbol {{ $status }}">{{ $symbol }}</span>
                        @else
                            -
                        @endif
                    </td>
                @endfor
                
                <td style="font-weight: bold;">{{ $student['totalHadir'] }}/{{ $student['totalPertemuan'] }}</td>
                <td style="font-weight: bold;">{{ $student['percentage'] }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="summary">
        <h3>Ringkasan Kehadiran</h3>
        <div class="summary-row">
            <div class="summary-item">
                <span class="label">Total Siswa</span>
                <span class="value">{{ $statistics['totalStudents'] }}</span>
            </div>
            <div class="summary-item">
                <span class="label">Pertemuan Terlaksana</span>
                <span class="value">{{ $statistics['completedMeetings'] }}/16</span>
            </div>
            <div class="summary-item">
                <span class="label">Rata-rata Kehadiran</span>
                <span class="value">{{ $statistics['classAverageAttendance'] }}%</span>
            </div>
        </div>
    </div>
    
    <div class="footer-info">
        <div class="print-date">
            <p>Dicetak pada: {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
        
        <div class="legend">
            <p><strong>Keterangan:</strong></p>
            <p>H = Hadir, S = Sakit, I = Izin, A = Alpha</p>
        </div>
    </div>
</body>
</html>
