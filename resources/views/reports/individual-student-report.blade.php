<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Siswa - {{ $siswa->nama_lengkap }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .student-info {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border: 1px solid #ddd;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            padding: 8px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            font-weight: bold;
            width: 30%;
        }
        .info-value {
            display: table-cell;
            padding: 8px;
            border: 1px solid #ddd;
        }
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .stats-row {
            display: table-row;
        }
        .stats-cell {
            display: table-cell;
            width: 25%;
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .stats-number {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .stats-label {
            font-size: 10px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
        .ranking-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            color: white;
        }
        .ranking-1 { background-color: #ffd700; color: #333; }
        .ranking-2 { background-color: #c0c0c0; color: #333; }
        .ranking-3 { background-color: #cd7f32; color: white; }
        .ranking-other { background-color: #6c757d; }
        .status-berprestasi { color: #28a745; font-weight: bold; }
        .status-baik { color: #007bff; font-weight: bold; }
        .status-cukup { color: #ffc107; font-weight: bold; }
        .status-perlu-perhatian { color: #dc3545; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN INDIVIDUAL SISWA</h1>
        <p>SMA Negeri 1 Kota Dumai</p>
        <p>Kelas: {{ $siswa->kelas->nama_kelas }} | Periode: {{ $tanggal }}</p>
    </div>

    <!-- Informasi Siswa -->
    <div class="section">
        <div class="section-title">INFORMASI SISWA</div>
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">{{ $siswa->nama_lengkap }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NISN</div>
                <div class="info-value">{{ $siswa->nisn }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value">{{ ucfirst($siswa->jenis_kelamin) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kelas</div>
                <div class="info-value">{{ $siswa->kelas->nama_kelas }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Peringkat di Kelas</div>
                <div class="info-value">
                    <span class="ranking-badge {{ $ranking <= 3 ? 'ranking-' . $ranking : 'ranking-other' }}">
                        #{{ $ranking }} dari {{ $totalSiswa }} siswa
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Akademik -->
    <div class="section">
        <div class="section-title">STATISTIK AKADEMIK</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <div class="stats-number">{{ $rataRata }}</div>
                    <div class="stats-label">Rata-rata Nilai</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">{{ $kehadiranPersentase }}%</div>
                    <div class="stats-label">Kehadiran</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">#{{ $ranking }}</div>
                    <div class="stats-label">Peringkat Kelas</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">{{ $nilaiSiswa->count() }}</div>
                    <div class="stats-label">Total Nilai</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Per Mata Pelajaran -->
    <div class="section">
        <div class="section-title">NILAI PER MATA PELAJARAN</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 40%">Mata Pelajaran</th>
                    <th style="width: 20%">Rata-rata</th>
                    <th style="width: 20%">Jumlah Nilai</th>
                    <th style="width: 20%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($nilaiSiswa as $mapel => $nilai)
                @php
                    $rataMapel = round($nilai->avg('nilai'), 2);
                    $statusClass = $rataMapel >= 85 ? 'status-berprestasi' : 
                                  ($rataMapel >= 75 ? 'status-baik' : 
                                  ($rataMapel >= 65 ? 'status-cukup' : 'status-perlu-perhatian'));
                    $statusText = $rataMapel >= 85 ? 'Berprestasi' : 
                                 ($rataMapel >= 75 ? 'Baik' : 
                                 ($rataMapel >= 65 ? 'Cukup' : 'Perlu Perhatian'));
                @endphp
                <tr>
                    <td>{{ $mapel }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $rataMapel }}</td>
                    <td style="text-align: center;">{{ $nilai->count() }}</td>
                    <td class="{{ $statusClass }}">{{ $statusText }}</td>
                </tr>
                @endforeach
                @if($nilaiSiswa->count() === 0)
                <tr>
                    <td colspan="4" style="text-align: center; color: #666;">Belum ada nilai yang tercatat</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Statistik Kehadiran -->
    <div class="section">
        <div class="section-title">STATISTIK KEHADIRAN</div>
        <table style="width: 70%;">
            <thead>
                <tr>
                    <th>Status</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Hadir</td>
                    <td style="text-align: center;">{{ $absensiStats['hadir'] }}</td>
                    <td style="text-align: center; color: #28a745; font-weight: bold;">
                        {{ round(($absensiStats['hadir'] / max(array_sum($absensiStats), 1)) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td style="text-align: center;">{{ $absensiStats['sakit'] }}</td>
                    <td style="text-align: center; color: #ffc107;">
                        {{ round(($absensiStats['sakit'] / max(array_sum($absensiStats), 1)) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td>Izin</td>
                    <td style="text-align: center;">{{ $absensiStats['izin'] }}</td>
                    <td style="text-align: center; color: #007bff;">
                        {{ round(($absensiStats['izin'] / max(array_sum($absensiStats), 1)) * 100, 1) }}%
                    </td>
                </tr>
                <tr>
                    <td>Alpha</td>
                    <td style="text-align: center;">{{ $absensiStats['alpha'] }}</td>
                    <td style="text-align: center; color: #dc3545;">
                        {{ round(($absensiStats['alpha'] / max(array_sum($absensiStats), 1)) * 100, 1) }}%
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Rekomendasi -->
    <div class="section">
        <div class="section-title">REKOMENDASI</div>
        <div style="padding: 10px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
            @if($rataRata >= 85)
            <strong>Prestasi Sangat Baik:</strong> Siswa menunjukkan performa akademik yang luar biasa. 
            Pertahankan konsistensi dan dorong untuk mengambil tantangan yang lebih tinggi.
            @elseif($rataRata >= 75)
            <strong>Prestasi Baik:</strong> Siswa memiliki performa yang baik. 
            Fokus pada peningkatan di mata pelajaran yang masih kurang optimal.
            @elseif($rataRata >= 65)
            <strong>Perlu Peningkatan:</strong> Siswa memerlukan bimbingan tambahan untuk mencapai potensi optimal. 
            Identifikasi mata pelajaran yang perlu diprioritaskan.
            @else
            <strong>Perlu Perhatian Khusus:</strong> Siswa memerlukan bimbingan intensif dan strategi pembelajaran yang disesuaikan. 
            Pertimbangkan konsultasi dengan orang tua dan guru mata pelajaran.
            @endif
            
            @if($kehadiranPersentase < 80)
            <br><br><strong>Kehadiran:</strong> Tingkat kehadiran siswa perlu ditingkatkan untuk mendukung proses pembelajaran yang optimal.
            @endif
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Wali Kelas: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
