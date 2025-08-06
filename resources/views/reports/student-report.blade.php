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
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info-grid {
            display: table;
            width: 100%;
        }
        .info-row {
            display: table-row;
        }
        .info-label {
            display: table-cell;
            width: 30%;
            padding: 5px 10px 5px 0;
            font-weight: bold;
            color: #333;
        }
        .info-value {
            display: table-cell;
            padding: 5px 0;
            color: #666;
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
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 15px;
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
            margin-top: 3px;
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
        .grade-excellent { background-color: #d4edda; color: #155724; }
        .grade-good { background-color: #cce7ff; color: #004085; }
        .grade-average { background-color: #fff3cd; color: #856404; }
        .grade-poor { background-color: #f8d7da; color: #721c24; }
        .ranking-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            color: white;
        }
        .ranking-1 { background-color: #ffd700; color: #333; }
        .ranking-2 { background-color: #c0c0c0; color: #333; }
        .ranking-3 { background-color: #cd7f32; color: white; }
        .ranking-other { background-color: #6c757d; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 10px;
            color: #666;
        }
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin-top: 50px;
            padding-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN INDIVIDUAL SISWA</h1>
        <p>SMA Negeri 1 Kota Dumai</p>
        <p>{{ $siswa->kelas->nama_kelas }} • {{ $tanggal }}</p>
    </div>

    <!-- Informasi Siswa -->
    <div class="student-info">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Nama Lengkap</div>
                <div class="info-value">: {{ $siswa->nama_lengkap }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">NISN</div>
                <div class="info-value">: {{ $siswa->nisn }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kelas</div>
                <div class="info-value">: {{ $siswa->kelas->nama_kelas }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value">: {{ ucfirst($siswa->jenis_kelamin) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Peringkat di Kelas</div>
                <div class="info-value">: 
                    <span class="ranking-badge {{ $ranking <= 3 ? 'ranking-' . $ranking : 'ranking-other' }}">
                        {{ $ranking }} dari {{ $totalSiswa }}
                        @if($ranking == 1) 🥇
                        @elseif($ranking == 2) 🥈
                        @elseif($ranking == 3) 🥉
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Akademik -->
    <div class="section">
        <div class="section-title">RINGKASAN AKADEMIK</div>
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
                    <div class="stats-number">{{ $ranking }}</div>
                    <div class="stats-label">Peringkat</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">{{ count($nilaiSiswa) }}</div>
                    <div class="stats-label">Mata Pelajaran</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai per Mata Pelajaran -->
    <div class="section">
        <div class="section-title">NILAI PER MATA PELAJARAN</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No</th>
                    <th style="width: 40%">Mata Pelajaran</th>
                    <th style="width: 20%">Rata-rata</th>
                    <th style="width: 15%">Tertinggi</th>
                    <th style="width: 15%">Terendah</th>
                    <th style="width: 15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @php $no = 1; @endphp
                @foreach($nilaiSiswa as $mapel => $nilaiList)
                @php
                    $avg = round($nilaiList->avg('nilai'), 2);
                    $max = $nilaiList->max('nilai');
                    $min = $nilaiList->min('nilai');
                    $gradeClass = '';
                    $status = '';
                    if ($avg >= 85) { $gradeClass = 'grade-excellent'; $status = 'Sangat Baik'; }
                    elseif ($avg >= 75) { $gradeClass = 'grade-good'; $status = 'Baik'; }
                    elseif ($avg >= 65) { $gradeClass = 'grade-average'; $status = 'Cukup'; }
                    else { $gradeClass = 'grade-poor'; $status = 'Perlu Perbaikan'; }
                @endphp
                <tr>
                    <td style="text-align: center;">{{ $no++ }}</td>
                    <td>{{ $mapel }}</td>
                    <td style="text-align: center;" class="{{ $gradeClass }}"><strong>{{ $avg }}</strong></td>
                    <td style="text-align: center;">{{ $max }}</td>
                    <td style="text-align: center;">{{ $min }}</td>
                    <td style="text-align: center;" class="{{ $gradeClass }}">{{ $status }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Statistik Kehadiran -->
    <div class="section">
        <div class="section-title">STATISTIK KEHADIRAN</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell" style="background-color: #d4edda;">
                    <div class="stats-number">{{ $absensiStats['hadir'] }}</div>
                    <div class="stats-label">Hadir</div>
                </div>
                <div class="stats-cell" style="background-color: #fff3cd;">
                    <div class="stats-number">{{ $absensiStats['sakit'] }}</div>
                    <div class="stats-label">Sakit</div>
                </div>
                <div class="stats-cell" style="background-color: #cce7ff;">
                    <div class="stats-number">{{ $absensiStats['izin'] }}</div>
                    <div class="stats-label">Izin</div>
                </div>
                <div class="stats-cell" style="background-color: #f8d7da;">
                    <div class="stats-number">{{ $absensiStats['alpha'] }}</div>
                    <div class="stats-label">Alpha</div>
                </div>
            </div>
        </div>
        
        <p style="text-align: center; margin-top: 10px; font-style: italic;">
            Persentase Kehadiran: <strong>{{ $kehadiranPersentase }}%</strong>
        </p>
    </div>

    <!-- Rekomendasi -->
    <div class="section">
        <div class="section-title">REKOMENDASI</div>
        <div style="background-color: #f8f9fa; padding: 15px; border-left: 4px solid #007bff;">
            @if($rataRata >= 85 && $kehadiranPersentase >= 90)
            <p><strong>Prestasi Sangat Baik:</strong> Siswa menunjukkan performa akademik dan kehadiran yang sangat baik. Pertahankan konsistensi dan berikan tantangan yang lebih menantang.</p>
            @elseif($rataRata >= 75 && $kehadiranPersentase >= 80)
            <p><strong>Prestasi Baik:</strong> Siswa memiliki performa yang baik. Berikan motivasi untuk mencapai prestasi yang lebih tinggi dan tingkatkan kehadiran.</p>
            @elseif($rataRata < 65 || $kehadiranPersentase < 70)
            <p><strong>Memerlukan Perhatian Khusus:</strong> Siswa memerlukan bimbingan intensif dalam akademik dan kedisiplinan kehadiran. Lakukan konsultasi dengan orang tua dan berikan program remedial.</p>
            @else
            <p><strong>Performa Standar:</strong> Siswa memiliki performa dalam batas normal. Berikan motivasi dan dukungan untuk meningkatkan prestasi akademik dan kehadiran.</p>
            @endif
        </div>
    </div>

    <div class="signature">
        <div class="signature-box">
            <p>{{ now()->format('d F Y') }}</p>
            <p>Wali Kelas {{ $siswa->kelas->nama_kelas }}</p>
            <div class="signature-line">
                {{ auth()->user()->name }}
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate secara otomatis pada {{ now()->format('d F Y H:i:s') }}</p>
    </div>
</body>
</html>
