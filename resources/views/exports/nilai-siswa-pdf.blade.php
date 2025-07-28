<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Nilai Siswa - {{ $mataPelajaran->nama_mapel }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
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
        
        .header h2 {
            margin: 5px 0;
            font-size: 14px;
            color: #666;
        }
        
        .info-table {
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }
        
        .info-table .label {
            width: 120px;
            font-weight: bold;
        }
        
        .nilai-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9px;
        }
        
        .nilai-table th,
        .nilai-table td {
            border: 1px solid #333;
            padding: 4px 2px;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
        }
        
        .nilai-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .nilai-table td {
            font-size: 9px;
            line-height: 1.1;
        }
        
        .text-left {
            text-align: left !important;
            padding-left: 4px !important;
        }
        
        .nilai-bagus {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        
        .nilai-cukup {
            background-color: #cce7ff;
            color: #004085;
        }
        
        .nilai-kurang {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .nilai-buruk {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .status-lengkap {
            background-color: #d4edda;
            color: #155724;
            font-weight: bold;
        }
        
        .status-sebagian {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status-belum {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        
        .signature {
            margin-top: 50px;
            text-align: right;
        }
        
        .page-break {
            page-break-before: always;
        }
        
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN NILAI SISWA</h1>
        <h2>SMA NEGERI 1 KOTA DUMAI</h2>
        <div style="margin-top: 10px; font-size: 12px;">
            Tahun Ajaran {{ $tahunAjaran ?? '2024/2025' }} - Semester {{ ucfirst($semester ?? 'Ganjil') }}
        </div>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td>: {{ $mataPelajaran->nama_mapel }}</td>
            <td class="label" style="padding-left: 40px;">Kelas</td>
            <td>: {{ $kelas->nama_kelas }}</td>
        </tr>
        <tr>
            <td class="label">Kode Mapel</td>
            <td>: {{ $mataPelajaran->kode_mapel }}</td>
            <td class="label" style="padding-left: 40px;">Tingkat</td>
            <td>: {{ $kelas->tingkat }} {{ $kelas->jurusan }}</td>
        </tr>
        <tr>
            <td class="label">Guru Pengajar</td>
            <td>: {{ $guru }}</td>
            <td class="label" style="padding-left: 40px;">Tanggal Cetak</td>
            <td>: {{ $tanggal }}</td>
        </tr>
    </table>

    <table class="nilai-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 25px;">No</th>
                <th rowspan="2" style="width: 60px;">NIS</th>
                <th rowspan="2" style="width: 100px;">Nama Siswa</th>
                @foreach($jenisNilai as $jenis)
                <th colspan="1" style="width: 45px;">
                    {{ $jenis->nama }}<br>
                    <small>({{ $jenis->bobot }}%)</small>
                </th>
                @endforeach
                <th rowspan="2" style="width: 40px;">Rata-rata</th>
                <th rowspan="2" style="width: 50px;">Status</th>
            </tr>
            <tr>
                <!-- Header kedua untuk jenis nilai jika diperlukan -->
                @foreach($jenisNilai as $jenis)
                <th style="font-size: 8px; padding: 2px;">Nilai</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($siswaList as $index => $siswa)
            @php
                $totalNilai = 0;
                $totalBobot = 0;
                $hasNilai = false;
                $allFinal = true;
                $siswaValues = [];
                
                foreach($jenisNilai as $jenis) {
                    $nilai = $nilaiSiswa->first(function($n) use ($siswa, $jenis) {
                        return $n->siswa_id == $siswa->id && $n->jenis_nilai_id == $jenis->id;
                    });
                    
                    if($nilai) {
                        $siswaValues[$jenis->id] = $nilai;
                        if($nilai->status === 'final') {
                            $totalNilai += $nilai->nilai * ($jenis->bobot / 100);
                            $totalBobot += $jenis->bobot / 100;
                            $hasNilai = true;
                        } else {
                            $allFinal = false;
                        }
                    } else {
                        $siswaValues[$jenis->id] = null;
                        $allFinal = false;
                    }
                }
                
                $rataRata = $totalBobot > 0 ? $totalNilai / $totalBobot : 0;
                $status = $hasNilai ? ($allFinal ? 'Lengkap' : 'Sebagian') : 'Belum Ada';
            @endphp
            
            <tr>
                <td style="font-size: 8px;">{{ $index + 1 }}</td>
                <td style="font-size: 8px;">{{ $siswa->nis }}</td>
                <td class="text-left" style="font-size: 8px; padding: 2px 4px;">
                    {{ strlen($siswa->nama_lengkap) > 20 ? substr($siswa->nama_lengkap, 0, 20) . '...' : $siswa->nama_lengkap }}
                </td>
                
                @foreach($jenisNilai as $jenis)
                @php
                    $nilai = $siswaValues[$jenis->id] ?? null;
                    $nilaiClass = '';
                    if($nilai) {
                        $nilaiValue = $nilai->nilai;
                        if($nilaiValue >= 85) $nilaiClass = 'nilai-bagus';
                        elseif($nilaiValue >= 70) $nilaiClass = 'nilai-cukup';
                        elseif($nilaiValue >= 55) $nilaiClass = 'nilai-kurang';
                        else $nilaiClass = 'nilai-buruk';
                    }
                @endphp
                <td class="{{ $nilaiClass }}" style="font-size: 8px; padding: 2px;">
                    @if($nilai)
                        {{ $nilai->nilai }}
                        @if($nilai->status === 'draft')
                            <span style="font-size: 6px; color: red;">*</span>
                        @endif
                    @else
                        -
                    @endif
                </td>
                @endforeach
                
                @php
                    $rataClass = '';
                    if($rataRata >= 85) $rataClass = 'nilai-bagus';
                    elseif($rataRata >= 70) $rataClass = 'nilai-cukup';
                    elseif($rataRata >= 55) $rataClass = 'nilai-kurang';
                    elseif($rataRata > 0) $rataClass = 'nilai-buruk';
                    
                    $statusClass = '';
                    if($status === 'Lengkap') $statusClass = 'status-lengkap';
                    elseif($status === 'Sebagian') $statusClass = 'status-sebagian';
                    else $statusClass = 'status-belum';
                @endphp
                
                <td class="{{ $rataClass }}" style="font-size: 8px; font-weight: bold; padding: 2px;">
                    {{ $hasNilai ? number_format($rataRata, 1) : '-' }}
                </td>
                <td class="{{ $statusClass }}" style="font-size: 7px; padding: 2px;">
                    @if($status === 'Lengkap')
                        L
                    @elseif($status === 'Sebagian')
                        S
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 10px;">
        <strong>Keterangan:</strong><br>
        - Nilai dengan tanda (*) masih berstatus draft<br>
        - Status: L = Lengkap (semua nilai final), S = Sebagian (beberapa nilai ada), - = Belum ada nilai<br>
        - Warna: Hijau ≥85, Biru ≥70, Kuning ≥55, Merah &lt;55
    </div>

    <div class="signature">
        <div>Dumai, {{ $tanggal }}</div>
        <div style="margin-top: 10px;">Guru Mata Pelajaran</div>
        <div style="margin-top: 60px; border-bottom: 1px solid #333; width: 200px; margin-left: auto;"></div>
        <div style="margin-top: 5px;"><strong>{{ $guru }}</strong></div>
    </div>
</body>
</html>
