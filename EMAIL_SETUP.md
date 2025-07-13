# Email Setup Guide for VMS App

## Overview
Sistem email akan mengirim notifikasi ke `siregarfresnel@gmail.com` ketika ada visitor yang memilih "Dept A" dalam form registrasi.

## Step-by-Step Setup

### 1. Konfigurasi .env File
Buat atau edit file `.env` di root project dengan konfigurasi berikut:

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="VMS App"
```

### 2. Setup Gmail App Password
1. Buka Google Account Settings
2. Aktifkan 2-Factor Authentication
3. Generate App Password:
   - Buka Security Settings
   - Pilih "App passwords"
   - Generate password untuk "Mail"
   - Gunakan password yang di-generate di `MAIL_PASSWORD`

### 3. Testing Email

#### Option 1: Using Laravel Command
```bash
php artisan email:test
```

#### Option 2: Simple PHP Test (if Laravel command fails)
```bash
php test-email.php
```

#### Option 3: Manual Testing
1. Buka form visitor registration di browser
2. Pilih "Dept A" di dropdown Department Purpose
3. Isi semua field yang required
4. Pilih tanggal dan waktu di masa depan
5. Submit form
6. Cek email di `siregarfresnel@gmail.com`

### 4. Testing Form
1. Buka form visitor registration
2. Pilih "Dept A" di dropdown Department Purpose
3. Isi form dan submit
4. Email akan dikirim ke `siregarfresnel@gmail.com`

## Troubleshooting

### Error: "Failed to send email"
1. Periksa konfigurasi .env
2. Pastikan App Password benar
3. Cek log di `storage/logs/laravel.log`

### Error: "Invalid credentials"
1. Pastikan 2FA aktif di Gmail
2. Gunakan App Password, bukan password biasa
3. Pastikan "Less secure app access" tidak aktif

### Error: "Connection timeout"
1. Periksa koneksi internet
2. Pastikan port 587 tidak diblokir
3. Coba gunakan port 465 dengan SSL

## Files Created/Modified

1. `app/Mail/VisitorNotification.php` - Mail class
2. `resources/views/emails/visitor-notification.blade.php` - Email template
3. `app/Http/Controllers/VisitorController.php` - Updated dengan email logic
4. `app/Console/Commands/TestEmail.php` - Command untuk testing
5. `config/mail-testing.php` - Konfigurasi email untuk testing

## Email Content
Email akan berisi:
- Nama visitor
- NIK
- Company
- Phone
- Department & Section purpose
- Visit date & time
- Registration date

## Security Notes
- Email hanya dikirim ketika "Dept A" dipilih
- Error handling mencegah crash jika email gagal
- Log error untuk debugging
- Tidak menyertakan foto dalam email (untuk keamanan) 