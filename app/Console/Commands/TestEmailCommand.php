<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Models\User;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Email address to send test to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Masukkan email tujuan untuk test');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Email tidak valid!');
            return 1;
        }

        $this->info('Mengirim test email ke: ' . $email);
        $this->info('Konfigurasi email saat ini:');
        $this->info('MAIL_MAILER: ' . config('mail.default'));
        $this->info('MAIL_HOST: ' . config('mail.mailers.smtp.host'));
        $this->info('MAIL_FROM: ' . config('mail.from.address'));

        try {
            Mail::raw('Ini adalah test email dari SIAKAD SMA Negeri 1 Kota Dumai. Jika Anda menerima email ini, berarti konfigurasi email sudah benar!', function ($message) use ($email) {
                $message->to($email)
                    ->subject('Test Email - SIAKAD SMA Negeri 1 Kota Dumai')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info('✅ Test email berhasil dikirim!');
            $this->info('Silakan cek email Anda (termasuk folder spam/junk)');
            
        } catch (\Exception $e) {
            $this->error('❌ Gagal mengirim email:');
            $this->error($e->getMessage());
            
            $this->newLine();
            $this->warn('Kemungkinan penyebab:');
            $this->warn('1. Konfigurasi MAIL di .env belum benar');
            $this->warn('2. Username/password email salah');
            $this->warn('3. Perlu menggunakan App Password untuk Gmail');
            $this->warn('4. Koneksi internet bermasalah');
            $this->newLine();
            $this->info('Lihat EMAIL_SETUP_GUIDE.md untuk panduan lengkap');
            
            return 1;
        }

        return 0;
    }
}
