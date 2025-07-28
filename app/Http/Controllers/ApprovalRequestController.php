<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApprovalRequest;
use App\Models\NilaiSiswa;
use App\Models\User;
use App\Notifications\ApprovalRequestReceived;
use App\Notifications\ApprovalRequestProcessed;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ApprovalRequestController extends Controller
{
    /**
     * Display a listing of approval requests for tatausaha.
     */
    public function index()
    {
        $requests = ApprovalRequest::with([
            'user', 
            'siswa', 
            'mataPelajaran', 
            'jenisNilai',
            'processedByUser'
        ])
        ->orderBy('created_at', 'desc')
        ->get();

        return Inertia::render('ApprovalRequests/Index', [
            'requests' => $requests
        ]);
    }

    /**
     * Store a newly created approval request.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'jenis_nilai_id' => 'required|exists:jenis_nilai,id',
            'old_value' => 'required|numeric|min:0|max:100',
            'new_value' => 'required|numeric|min:0|max:100|different:old_value',
            'reason' => 'required|string|max:500',
        ]);

        $approvalRequest = ApprovalRequest::create([
            'requested_by' => Auth::id(),
            'siswa_id' => $validated['siswa_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'jenis_nilai_id' => $validated['jenis_nilai_id'],
            'old_value' => $validated['old_value'],
            'new_value' => $validated['new_value'],
            'reason' => $validated['reason'],
            'status' => 'pending',
        ]);

        // Load relationships for notification
        $approvalRequest->load(['siswa', 'mataPelajaran', 'jenisNilai', 'user']);

        // Send notification to all tata_usaha users
        $tataUsahaUsers = User::whereHas('role', function($query) {
            $query->where('name', 'tata_usaha');
        })->get();

        foreach ($tataUsahaUsers as $user) {
            $user->notify(new ApprovalRequestReceived($approvalRequest));
        }

        return redirect()->back()->with('success', 'Permintaan persetujuan berhasil diajukan.');
    }

    /**
     * Display the user's own approval requests.
     */
    public function myRequests()
    {
        $requests = ApprovalRequest::where('requested_by', auth()->id())
            ->with(['siswa', 'mataPelajaran', 'jenisNilai', 'processedByUser'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('ApprovalRequests/MyRequests', [
            'requests' => $requests
        ]);
    }

    /**
     * Approve an approval request.
     */
    public function approve(ApprovalRequest $approval)
    {
        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $approval->update([
            'status' => 'approved',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
        ]);

        // Execute the approved action (update the grade)
        $this->executeApprovedAction($approval);

        // Load relationships for notification
        $approval->load(['siswa', 'mataPelajaran', 'jenisNilai', 'user', 'processedByUser']);

        // Send notification to the requester
        $approval->user->notify(new ApprovalRequestProcessed($approval));

        return redirect()->back()->with('success', 'Permintaan berhasil disetujui dan nilai telah diperbarui.');
    }

    /**
     * Reject an approval request.
     */
    public function reject(Request $request, ApprovalRequest $approval)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        if ($approval->status !== 'pending') {
            return redirect()->back()->with('error', 'Permintaan ini sudah diproses sebelumnya.');
        }

        $approval->update([
            'status' => 'rejected',
            'processed_by' => Auth::id(),
            'processed_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // Load relationships for notification
        $approval->load(['siswa', 'mataPelajaran', 'jenisNilai', 'user', 'processedByUser']);

        // Send notification to the requester
        $approval->user->notify(new ApprovalRequestProcessed($approval));

        return redirect()->back()->with('success', 'Permintaan berhasil ditolak.');
    }

    /**
     * Execute approved action and update related records.
     */
    private function executeApprovedAction(ApprovalRequest $approvalRequest)
    {
        // Find the nilai_siswa record and update it
        $nilaiSiswa = NilaiSiswa::where('siswa_id', $approvalRequest->siswa_id)
            ->where('mata_pelajaran_id', $approvalRequest->mata_pelajaran_id)
            ->where('jenis_nilai_id', $approvalRequest->jenis_nilai_id)
            ->first();

        if ($nilaiSiswa) {
            $nilaiSiswa->update([
                'nilai' => $approvalRequest->new_value,
                'status' => 'final'
            ]);
        }
    }
}
