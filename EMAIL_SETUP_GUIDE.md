# 📧 Panduan Konfigurasi Email untuk Reset Password

## 🔧 Konfigurasi Email di .env

Untuk membuat fitur reset password bekerja dengan benar, Anda perlu mengkonfigurasi email di file `.env`:

### 1. Gmail SMTP (Rekomendasi)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-school-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@smanegeri1kotadumai.sch.id"
MAIL_FROM_NAME="SIAKAD SMA Negeri 1 Kota Dumai"
```

### 2. Outlook/Hotmail SMTP
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=your-school-email@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@smanegeri1kotadumai.sch.id"
MAIL_FROM_NAME="SIAKAD SMA Negeri 1 Kota Dumai"
```

### 3. Untuk Testing (Log Only)
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@smanegeri1kotadumai.sch.id"
MAIL_FROM_NAME="SIAKAD SMA Negeri 1 Kota Dumai"
```

## 🔐 Cara Mendapatkan App Password Gmail

1. **Aktifkan 2-Factor Authentication** di akun Gmail
2. Masuk ke **Google Account Settings**
3. Pilih **Security** → **2-Step Verification**
4. Scroll ke bawah dan pilih **App passwords**
5. Pilih **Mail** dan **Other (Custom name)**
6. Masukkan nama: "SIAKAD SMA Negeri 1"
7. Gunakan password yang dihasilkan di `MAIL_PASSWORD`

## 🛠️ Langkah-langkah Setup

### 1. Update .env file
```bash
# Edit file .env dan sesuaikan konfigurasi email
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=siakad@smanegeri1kotadumai.sch.id
MAIL_PASSWORD=your-16-digit-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@smanegeri1kotadumai.sch.id"
MAIL_FROM_NAME="SIAKAD SMA Negeri 1 Kota Dumai"
```

### 2. Clear Config Cache
```bash
php artisan config:clear
php artisan cache:clear
```

### 3. Test Email
```bash
php artisan tinker
Mail::raw('Test email from SIAKAD', function ($message) {
    $message->to('test@email.com')->subject('Test Email');
});
```

## 📋 Checklist Verifikasi

- [ ] Konfigurasi SMTP sudah benar di .env
- [ ] App password Gmail sudah dibuat
- [ ] Config cache sudah di-clear
- [ ] Test email berhasil terkirim
- [ ] Reset password link berfungsi
- [ ] Email template sudah sesuai

## 🔍 Troubleshooting

### Email tidak terkirim?
1. Cek log Laravel: `storage/logs/laravel.log`
2. Pastikan internet connection stabil
3. Verifikasi username/password email
4. Cek spam folder di email penerima

### Error "Authentication failed"?
1. Pastikan menggunakan App Password, bukan password biasa
2. Aktifkan 2FA di Gmail terlebih dahulu
3. Pastikan "Less secure app access" dimatikan

### Email masuk spam?
1. Setup SPF record di DNS
2. Setup DKIM authentication
3. Gunakan domain email sekolah yang resmi
4. Hindari kata-kata spam di subject/content

## 📧 Email Template

Email reset password akan otomatis menggunakan template Laravel default. Untuk kustomisasi:

```bash
php artisan vendor:publish --tag=laravel-notifications
```

Template ada di: `resources/views/notifications/reset-password.blade.php`

## 🚀 Production Setup

Untuk production, disarankan menggunakan:
- **AWS SES** (untuk volume besar)
- **SendGrid** (mudah setup)
- **Mailgun** (reliable)
- **Email server sekolah** (jika ada)

Example AWS SES:
```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-key
AWS_SECRET_ACCESS_KEY=your-secret
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS="noreply@smanegeri1kotadumai.sch.id"
```
