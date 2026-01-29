# Sistem Login PHP Native dengan Google Authenticator (2FA)

Aplikasi sistem autentikasi yang aman dan siap pakai, dibangun menggunakan **PHP Native** dan terintegrasi dengan **Google Authenticator** untuk Two-Factor Authentication (2FA). Proyek ini dirancang dengan standar keamanan modern untuk melindungi akun pengguna.

## 🔥 Fitur Unggulan

Aplikasi ini tidak hanya sekedar login biasa, tapi mencakup fitur keamanan _Enterprise Grade_:

- **🔐 Two-Factor Authentication (2FA):** Integrasi QR Code dengan aplikasi Google Authenticator.
- **🆘 Kode Cadangan (Backup Codes):** Solusi login darurat jika HP hilang.
- **🛡️ Rate Limiting (Brute Force Protection):** Akun terkunci otomatis selama 15 menit jika salah password 5 kali.
- **🍪 Trust Device (Remember Me):** Opsi "Ingat Perangkat Ini" selama 30 hari untuk melewati 2FA di browser terpercaya.
- **🌐 CSRF Protection:** Melindungi semua formulir dari serangan Cross-Site Request Forgery.
- **📧 Lupa Password:** Fitur reset password menggunakan email (PHPMailer).
- **👤 Manajemen Profil:** Update password dengan verifikasi password lama.
- **🎨 UI Modern:** Tampilan responsif menggunakan **Bootstrap 5**.

## 🛠️ Teknologi yang Digunakan

- **Bahasa:** PHP Native (>= 7.4)
- **Database:** MySQL / MariaDB
- **Frontend:** Bootstrap 5
- **Library (via Composer):**
    - `sonata-project/google-authenticator` (Untuk logika OTP)
    - `phpmailer/phpmailer` (Untuk kirim email reset password)

## 🚀 Cara Instalasi (Step-by-Step)

Ikuti langkah ini untuk menjalankan proyek di komputer lokal (Localhost):

### 1. Clone Repository

Download atau clone proyek ini ke folder web server Anda (htdocs/www).

```bash
git clone [https://github.com/rizalizul/php-google-authenticator-login.git](https://github.com/rizalizul/php-google-authenticator-login.git)
```

### 2. Install Library (Composer)

Proyek ini tidak menyertakan folder vendor/ demi efisiensi. Anda perlu menginstalnya manual. Pastikan Composer sudah terinstall di komputer Anda.

```bash
composer install
```

### 3. Konfigurasi Database

1. Buat database baru di MySQL/phpMyAdmin (misalnya: auth_db).
2. Import file database.sql yang ada di dalam folder proyek ke database tersebut.
3. ATAU, jika tidak ada file SQL, jalankan query berikut manual:

<details> <summary>klik untuk melihat Query SQL</summary>
```bash
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    google_secret VARCHAR(255) NULL,
    backup_codes TEXT NULL,
    reset_token VARCHAR(255) NULL,
    reset_expires DATETIME NULL,
    failed_attempts INT DEFAULT 0,
    locked_until DATETIME NULL,
    remember_token VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
</details>

### 4. Konfigurasi Koneksi (db.php)

File `db.php` tidak disertakan karena alasan keamanan. Anda harus membuatnya dari contoh yang ada.

1. Ubah nama file `db.example.php` menjadi `db.php`.
2. Buka `db.php` dan sesuaikan kredensial database Anda:

```php
$host = 'localhost';
$db   = 'auth_db'; // Sesuaikan nama DB
$user = 'root';    // Default XAMPP/Laragon
$pass = '';        // Default kosong
```

### 5. Konfigurasi Email (SMTP)

Untuk fitur Lupa Password, buka file `forgot_password.php`. Sesuaikan bagian SMTP. Rekomendasi: Gunakan Mailtrap.io untuk testing di localhost agar email tidak masuk Spam/Blokir.

```php
$mail->Host       = 'sandbox.smtp.mailtrap.io';
$mail->Username   = 'USER_MAILTRAP_ANDA';
$mail->Password   = 'PASS_MAILTRAP_ANDA';
$mail->Port       = 2525;
```

## 📖 Cara Penggunaan

1. Buka browser dan akses `http://localhost/php-google-authenticator-login/register.php`.
2. Registrasi: Daftar akun baru.
3. Scan QR: Buka aplikasi Google Authenticator di HP, scan QR Code yang muncul.
4. Simpan Kode Backup: Salin kode cadangan ke tempat aman.
5. Login: Masukkan Username & Password, lalu masukkan 6 digit kode dari aplikasi.

## ⚠️ Troubleshooting

- Kode Authenticator Selalu Salah? Pastikan jam di Server (Laptop/PC) dan HP Anda sinkron. Perbedaan waktu 1 menit saja akan menyebabkan kode gagal (Time-based OTP).

- Email tidak terkirim? Pastikan konfigurasi SMTP di forgot_password.php sudah benar. Localhost biasanya tidak bisa mengirim email langsung ke Gmail/Yahoo tanpa konfigurasi SMTP Relay.

## 📝 Lisensi

Open Source. Silakan digunakan untuk pembelajaran atau dasar proyek komersial.
