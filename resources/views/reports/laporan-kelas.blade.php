<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kelas {{ $kelas->nama_kelas }}</title>
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
            font-size: 20px;
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
        .ranking-1 { background-color: #fff3cd; }
        .ranking-2 { background-color: #f8f9fa; }
        .ranking-3 { background-color: #fff3e0; }
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
        <h1>LAPORAN KELAS {{ strtoupper($kelas->nama_kelas) }}</h1>
        <p>SMA Negeri 1 Kota Dumai</p>
        <p>Periode: {{ $tanggal }}</p>
    </div>

    <!-- Statistik Umum -->
    <div class="section">
        <div class="section-title">STATISTIK UMUM</div>
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <div class="stats-number">{{ $statistik['total_siswa'] }}</div>
                    <div class="stats-label">Total Siswa</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">{{ $statistik['rata_rata_kelas'] }}</div>
                    <div class="stats-label">Rata-rata Kelas</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">{{ $statistik['kehadiran_rata'] }}%</div>
                    <div class="stats-label">Kehadiran Rata-rata</div>
                </div>
                <div class="stats-cell">
                    <div class="stats-number">{{ $statistik['prestasi_count'] }}</div>
                    <div class="stats-label">Siswa Berprestasi</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Ranking Akademik -->
    <div class="section">
        <div class="section-title">RANKING AKADEMIK SISWA</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%">Rank</th>
                    <th style="width: 15%">NISN</th>
                    <th style="width: 30%">Nama Lengkap</th>
                    <th style="width: 12%">L/P</th>
                    <th style="width: 12%">Rata-rata</th>
                    <th style="width: 12%">Kehadiran</th>
                    <th style="width: 15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingSiswa as $index => $ranking)
                <tr class="{{ $index < 3 ? 'ranking-' . ($index + 1) : '' }}">
                    <td style="text-align: center; font-weight: bold;">
                        {{ $index + 1 }}
                        @if($index == 0) 🥇
                        @elseif($index == 1) 🥈
                        @elseif($index == 2) 🥉
                        @endif
                    </td>
                    <td>{{ $ranking['siswa']->nisn }}</td>
                    <td>{{ $ranking['siswa']->nama_lengkap }}</td>
                    <td style="text-align: center;">{{ strtoupper(substr($ranking['siswa']->jenis_kelamin, 0, 1)) }}</td>
                    <td style="text-align: center; font-weight: bold;">{{ $ranking['rata_rata'] }}</td>
                    <td style="text-align: center;">{{ $ranking['kehadiran'] }}%</td>
                    <td class="status-{{ strtolower(str_replace(' ', '-', $ranking['status'])) }}">
                        {{ $ranking['status'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Distribusi Gender -->
    <div class="section">
        <div class="section-title">DISTRIBUSI SISWA</div>
        <table style="width: 50%;">
            <thead>
                <tr>
                    <th>Jenis Kelamin</th>
                    <th>Jumlah</th>
                    <th>Persentase</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Laki-laki</td>
                    <td style="text-align: center;">{{ $statistik['siswa_laki'] }}</td>
                    <td style="text-align: center;">{{ round(($statistik['siswa_laki'] / $statistik['total_siswa']) * 100, 1) }}%</td>
                </tr>
                <tr>
                    <td>Perempuan</td>
                    <td style="text-align: center;">{{ $statistik['siswa_perempuan'] }}</td>
                    <td style="text-align: center;">{{ round(($statistik['siswa_perempuan'] / $statistik['total_siswa']) * 100, 1) }}%</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate pada {{ now()->format('d F Y H:i:s') }}</p>
        <p>Wali Kelas: {{ auth()->user()->name }}</p>
    </div>
</body>
</html>
