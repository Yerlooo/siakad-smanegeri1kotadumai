<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ApprovalRequest;

class ApprovalRequestProcessed extends Notification
{
    use Queueable;

    protected $approvalRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(ApprovalRequest $approvalRequest)
    {
        $this->approvalRequest = $approvalRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $isApproved = $this->approvalRequest->status === 'approved';
        $statusText = $isApproved ? 'disetujui' : 'ditolak';
        
        return [
            'approval_request_id' => $this->approvalRequest->id,
            'processor_name' => $this->approvalRequest->processedByUser->name ?? 'Tata Usaha',
            'siswa_name' => $this->approvalRequest->siswa->nama_lengkap,
            'mata_pelajaran' => $this->approvalRequest->mataPelajaran->nama_mapel,
            'jenis_nilai' => $this->approvalRequest->jenisNilai->nama,
            'old_value' => $this->approvalRequest->old_value,
            'new_value' => $this->approvalRequest->new_value,
            'status' => $this->approvalRequest->status,
            'rejection_reason' => $this->approvalRequest->rejection_reason,
            'action_url' => route('my-approval-requests.index'),
            'message' => "Permintaan perubahan nilai {$this->approvalRequest->mataPelajaran->nama_mapel} untuk siswa {$this->approvalRequest->siswa->nama_lengkap} telah {$statusText}",
            'title' => $isApproved ? 'Permintaan Disetujui' : 'Permintaan Ditolak',
            'type' => 'approval_request_processed',
            'color' => $isApproved ? 'green' : 'red'
        ];
    }
}
