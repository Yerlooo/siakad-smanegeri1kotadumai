<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaliKelasRemoved extends Notification
{
    use Queueable;

    protected $kelas;
    protected $remover;

    /**
     * Create a new notification instance.
     */
    public function __construct($kelas, $remover)
    {
        $this->kelas = $kelas;
        $this->remover = $remover;
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
            'title' => 'Penugasan Wali Kelas Dihapus',
            'message' => "Anda tidak lagi menjadi wali kelas {$this->kelas->nama_kelas}. Perubahan dilakukan oleh {$this->remover->name}",
            'type' => 'wali_kelas_removal',
            'kelas_id' => $this->kelas->id,
            'remover_id' => $this->remover->id,
            'action_url' => route('dashboard'),
            'icon' => 'user-minus',
            'color' => 'danger'
        ];
    }
}
