<?php
// forgot_password.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Cek Token CSRF sebelum memproses apapun
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('<div class="alert alert-danger">Error: Sesi kadaluarsa atau akses ilegal. Silakan refresh halaman.</div>');
    }

    $email = $_POST['email'];

    // 1. Cek apakah email terdaftar?
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        // 2. Generate Token Unik & Waktu Expired (1 Jam dari sekarang)
        $token = bin2hex(random_bytes(32)); 

        // 3. Simpan Token ke Database (Gunakan DATE_ADD milik MySQL agar jamnya sinkron)
        $update = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?");
        $update->execute([$token, $email]);

        // 4. Kirim Email (Konfigurasi ini CONTOH, sesuaikan dengan SMTP Anda)
        $mail = new PHPMailer(true);

        try {
            // Setting Server SMTP (Gunakan Mailtrap.io untuk testing aman)
            $mail->isSMTP();
            $mail->Host       = 'sandbox.smtp.mailtrap.io'; // Ganti dengan SMTP host Anda (misal: smtp.gmail.com)
            $mail->SMTPAuth   = true;
            $mail->Username   = 'be37c06c723fd6'; // Ganti user SMTP
            $mail->Password   = '3eabbc99834d13'; // Ganti pass SMTP
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 2525;

            // Penerima
            $mail->setFrom('admin@myapp.com', 'Admin Sistem');
            $mail->addAddress($email);

            // Konten Email
            $link = "http://localhost/belajar_gauth/reset_password.php?email=".$email."&token=".$token;

            $mail->isHTML(true);
            $mail->Subject = 'Reset Password Anda';
            $mail->Body    = "Klik link ini untuk reset password: <a href='$link'>$link</a>.<br>Link ini kadaluarsa dalam 1 jam.";

            $mail->send();
            $message = '<div class="alert alert-success">Link reset telah dikirim ke email Anda! Cek Inbox/Spam.</div>';
        } catch (Exception $e) {
            $message = '<div class="alert alert-danger">Gagal kirim email: '.$mail->ErrorInfo.'</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Email tidak ditemukan!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Lupa Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="width: 400px;">
            <div class="card-body p-4">
                <h4 class="text-center mb-3">Lupa Password?</h4>
                <p class="text-muted small text-center">Masukkan email Anda untuk menerima link reset.</p>
                
                <?php echo $message; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email Anda" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Kirim Link Reset</button>
                    <div class="text-center mt-3">
                        <a href="login.php" class="text-decoration-none">Kembali ke Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>