<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaliKelasAssigned extends Notification
{
    use Queueable;

    protected $kelas;
    protected $assigner;

    /**
     * Create a new notification instance.
     */
    public function __construct($kelas, $assigner)
    {
        $this->kelas = $kelas;
        $this->assigner = $assigner;
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
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Penugasan Wali Kelas',
            'message' => "Anda telah ditugaskan sebagai wali kelas {$this->kelas->nama_kelas} oleh {$this->assigner->name}",
            'type' => 'wali_kelas_assignment',
            'kelas_id' => $this->kelas->id,
            'assigner_id' => $this->assigner->id,
            'action_url' => route('kelas.show', $this->kelas->id),
            'icon' => 'user-check',
            'color' => 'success'
        ];
    }
}
