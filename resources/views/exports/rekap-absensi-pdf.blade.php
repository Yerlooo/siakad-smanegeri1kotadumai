<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #2c3e50;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 5px 0;
            font-size: 14px;
        }
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        .data-table th,
        .data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .data-table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .data-table td.number {
            text-align: center;
        }
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            text-align: center;
        }
        .status-hadir { background-color: #d4edda; color: #155724; }
        .status-sakit { background-color: #fff3cd; color: #856404; }
        .status-izin { background-color: #cce7ff; color: #004085; }
        .status-alpha { background-color: #f8d7da; color: #721c24; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>REKAP ABSENSI SISWA</h1>
        <p>SMA Negeri 1 Kota Dumai</p>
    </div>

    <table class="info-table">
        <tr>
            <td width="150"><strong>Periode</strong></td>
            <td>: {{ $namaBulan }}</td>
        </tr>
        
        @if(isset($currentUser))
        <tr>
            <td><strong>Guru yang Cetak</strong></td>
            <td>: {{ $currentUser->name }}</td>
        </tr>
        @endif
        
        @if(isset($selectedMataPelajaran) && $selectedMataPelajaran)
        <tr>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: {{ $selectedMataPelajaran->nama_mapel }} ({{ $selectedMataPelajaran->kode_mapel }})</td>
        </tr>
        @elseif(isset($uniqueMataPelajaran) && $uniqueMataPelajaran->count() > 0)
        <tr>
            <td><strong>Mata Pelajaran</strong></td>
            <td>: 
                @if($uniqueMataPelajaran->count() == 1)
                    {{ $uniqueMataPelajaran->first()->nama_mapel }} ({{ $uniqueMataPelajaran->first()->kode_mapel }})
                @else
                    {{ $uniqueMataPelajaran->count() }} Mata Pelajaran - 
                    {{ $uniqueMataPelajaran->pluck('nama_mapel')->take(3)->join(', ') }}
                    @if($uniqueMataPelajaran->count() > 3), dll.@endif
                @endif
            </td>
        </tr>
        @endif
        
        @if(isset($selectedKelas) && $selectedKelas)
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: {{ $selectedKelas->nama_kelas }}</td>
        </tr>
        @elseif(isset($uniqueKelas) && $uniqueKelas->count() > 0)
        <tr>
            <td><strong>Kelas</strong></td>
            <td>: 
                @if($uniqueKelas->count() == 1)
                    {{ $uniqueKelas->first()->nama_kelas }}
                @else
                    {{ $uniqueKelas->count() }} Kelas - 
                    {{ $uniqueKelas->pluck('nama_kelas')->take(3)->join(', ') }}
                    @if($uniqueKelas->count() > 3), dll.@endif
                @endif
            </td>
        </tr>
        @endif
        
        @if(isset($selectedGuru) && $selectedGuru)
        <tr>
            <td><strong>Guru Pengajar</strong></td>
            <td>: {{ $selectedGuru->name }}</td>
        </tr>
        @elseif(isset($uniqueGuru) && $uniqueGuru->count() > 0 && $uniqueGuru->count() <= 3)
        <tr>
            <td><strong>Guru Pengajar</strong></td>
            <td>: {{ $uniqueGuru->pluck('name')->join(', ') }}</td>
        </tr>
        @elseif(isset($uniqueGuru) && $uniqueGuru->count() > 3)
        <tr>
            <td><strong>Guru Pengajar</strong></td>
            <td>: {{ $uniqueGuru->count() }} Guru - {{ $uniqueGuru->pluck('name')->take(2)->join(', ') }}, dll.</td>
        </tr>
        @endif
        
        <tr>
            <td><strong>Tanggal Cetak</strong></td>
            <td>: {{ $tanggalCetak }}</td>
        </tr>
        <tr>
            <td><strong>Total Siswa</strong></td>
            <td>: {{ count($rekapData) }} siswa</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Siswa</th>
                <th width="15%">Kelas</th>
                <th width="15%">NISN</th>
                <th width="8%">Hadir</th>
                <th width="8%">Sakit</th>
                <th width="8%">Izin</th>
                <th width="8%">Alpha</th>
                <th width="8%">Total</th>
                <th width="10%">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapData as $index => $data)
            <tr>
                <td class="number">{{ $index + 1 }}</td>
                <td>{{ $data['siswa']['nama_lengkap'] }}</td>
                <td class="number">{{ $data['siswa']['kelas']['nama_kelas'] ?? 'N/A' }}</td>
                <td class="number">{{ $data['siswa']['nisn'] }}</td>
                <td class="number status-badge status-hadir">{{ $data['statistik']['hadir'] }}</td>
                <td class="number status-badge status-sakit">{{ $data['statistik']['sakit'] }}</td>
                <td class="number status-badge status-izin">{{ $data['statistik']['izin'] }}</td>
                <td class="number status-badge status-alpha">{{ $data['statistik']['alpha'] }}</td>
                <td class="number"><strong>{{ $data['statistik']['total'] }}</strong></td>
                <td class="number"><strong>{{ $data['statistik']['persentase_hadir'] }}%</strong></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak dari Sistem Informasi Akademik (SIAKAD)</p>
        <p>SMA Negeri 1 Kota Dumai</p>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print / Save as PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ❌ Close
        </button>
    </div>
</body>
</html>
