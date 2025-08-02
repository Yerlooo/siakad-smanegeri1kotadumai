<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Absensi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Siswa;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AbsensiRekapExport;

class AbsensiExportController extends Controller
{
    public function export(Request $request)
    {
        $format = $request->get('format', 'pdf');
        $kelasId = $request->get('kelas_id');
        $mataPelajaranId = $request->get('mata_pelajaran_id');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Validate required parameters
        if (!$kelasId) {
            return response()->json(['error' => 'Kelas ID is required'], 400);
        }

        // Get data
        $kelas = Kelas::findOrFail($kelasId);
        $mataPelajaran = $mataPelajaranId ? MataPelajaran::find($mataPelajaranId) : null;
        
        // Build query
        $query = Absensi::with(['siswa'])
            ->whereHas('siswa', function($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });

        if ($mataPelajaranId) {
            $query->where('mata_pelajaran_id', $mataPelajaranId);
        }

        if ($startDate) {
            $query->where('tanggal', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('tanggal', '<=', $endDate);
        }

        $absensiData = $query->orderBy('tanggal')
            ->orderBy('created_at')
            ->get();

        // Process data similar to frontend
        $processedData = $this->processAbsensiData($absensiData);

        $exportData = [
            'kelas' => $kelas,
            'mataPelajaran' => $mataPelajaran,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'studentData' => $processedData['students'],
            'meetingDates' => $processedData['meetings'],
            'statistics' => $processedData['statistics']
        ];

        if ($format === 'excel') {
            return Excel::download(new AbsensiRekapExport($exportData), $this->generateFilename($kelas, $mataPelajaran, 'xlsx'));
        } else {
            $pdf = Pdf::loadView('exports.absensi-rekap', $exportData)
                ->setPaper('a4', 'landscape')
                ->setOptions([
                    'dpi' => 150,
                    'defaultFont' => 'DejaVu Sans',
                    'isHtml5ParserEnabled' => true,
                    'isPhpEnabled' => true
                ]);
            return $pdf->download($this->generateFilename($kelas, $mataPelajaran, 'pdf'));
        }
    }

    private function processAbsensiData($absensiData)
    {
        // Group by session (similar to frontend logic)
        $sessionMap = collect();
        
        foreach ($absensiData as $absensi) {
            $sessionTime = \Carbon\Carbon::parse($absensi->created_at)->setSecond(0)->toISOString();
            
            if (!$sessionMap->has($sessionTime)) {
                $sessionMap->put($sessionTime, [
                    'tanggal' => $absensi->tanggal,
                    'created_at' => $absensi->created_at,
                    'sessionTime' => $sessionTime
                ]);
            }
        }

        $meetings = $sessionMap->values()->sortBy('created_at')->values()->all();

        // Process student data
        $studentMap = collect();
        
        foreach ($absensiData as $absensi) {
            if (!$studentMap->has($absensi->siswa->id)) {
                $studentMap->put($absensi->siswa->id, [
                    'id' => $absensi->siswa->id,
                    'nis' => $absensi->siswa->nis,
                    'nama_lengkap' => $absensi->siswa->nama_lengkap,
                    'attendance' => [],
                    'totalHadir' => 0,
                    'totalPertemuan' => 0,
                    'percentage' => 0,
                    'recordsBySession' => collect()
                ]);
            }

            $student = $studentMap->get($absensi->siswa->id);
            $sessionTime = \Carbon\Carbon::parse($absensi->created_at)->setSecond(0)->toISOString();
            
            if (!$student['recordsBySession']->has($sessionTime)) {
                $student['recordsBySession']->put($sessionTime, collect());
            }
            
            $student['recordsBySession']->get($sessionTime)->push([
                'id' => $absensi->id,
                'tanggal' => $absensi->tanggal,
                'status' => $absensi->status,
                'created_at' => $absensi->created_at,
                'sessionKey' => $sessionTime
            ]);
            
            $studentMap->put($absensi->siswa->id, $student);
        }

        // Process attendance for each student
        foreach ($studentMap as $studentId => $student) {
            foreach ($meetings as $index => $session) {
                $pertemuanNumber = $index + 1;
                $recordsForSession = $student['recordsBySession']->get($session['sessionTime']);
                
                if ($recordsForSession && $recordsForSession->count() > 0) {
                    $latestRecord = $recordsForSession->last();
                    $student['attendance'][$pertemuanNumber] = $latestRecord['status'];
                    $student['totalPertemuan']++;
                    
                    if ($latestRecord['status'] === 'hadir') {
                        $student['totalHadir']++;
                    }
                }
            }
            
            $student['percentage'] = $student['totalPertemuan'] > 0 
                ? round(($student['totalHadir'] / $student['totalPertemuan']) * 100) 
                : 0;
            
            unset($student['recordsBySession']);
            $studentMap->put($studentId, $student);
        }

        $students = $studentMap->values()->sortBy('nama_lengkap')->values()->all();

        // Calculate statistics
        $totalStudents = count($students);
        $completedMeetings = count($meetings);
        $classAverageAttendance = $totalStudents > 0 
            ? round(collect($students)->avg('percentage')) 
            : 0;

        return [
            'students' => $students,
            'meetings' => $meetings,
            'statistics' => [
                'totalStudents' => $totalStudents,
                'completedMeetings' => $completedMeetings,
                'classAverageAttendance' => $classAverageAttendance
            ]
        ];
    }

    private function generateFilename($kelas, $mataPelajaran, $extension)
    {
        $className = str_replace(' ', '_', $kelas->nama_kelas);
        $mapelName = $mataPelajaran ? str_replace(' ', '_', $mataPelajaran->nama_mapel) : 'Semua_Mapel';
        $currentDate = now()->format('Y-m-d');
        
        return "Rekap_Absensi_{$className}_{$mapelName}_{$currentDate}.{$extension}";
    }
}
