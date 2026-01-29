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
git clone [https://github.com/USERNAME_ANDA/login-2FA-AuthGoogle-php-native.git](https://github.com/USERNAME_ANDA/login-2FA-AuthGoogle-php-native.git)
```
