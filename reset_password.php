<?php
// reset_password.php
require 'db.php';

$message = '';
$valid_token = false;

// Ambil data dari URL
if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Cek Token Valid & Belum Expired
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_token = ? AND reset_expires > NOW()");
    $stmt->execute([$email, $token]);

    if ($stmt->rowCount() > 0) {
        $valid_token = true;
    } else {
        $message = '<div class="alert alert-danger">Link tidak valid atau sudah kadaluarsa!</div>';
    }
} else {
    header("Location: login.php");
    exit;
}

// Proses Ganti Password Baru
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $valid_token) {

    // Cek Token CSRF sebelum memproses apapun
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('<div class="alert alert-danger">Error: Sesi kadaluarsa atau akses ilegal. Silakan refresh halaman.</div>');
    }

    $new_pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Update Password & Hapus Token (supaya link tidak bisa dipakai 2x)
    $update = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE email = ?");
    
    if ($update->execute([$new_pass, $email])) {
        $message = '<div class="alert alert-success">Password berhasil diubah! Silakan <a href="login.php">Login</a>.</div>';
        $valid_token = false; // Sembunyikan form
    } else {
        $message = '<div class="alert alert-danger">Gagal mereset password.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="width: 400px;">
            <div class="card-body p-4">
                <h4 class="text-center mb-3">Buat Password Baru</h4>
                
                <?php echo $message; ?>

                <?php if ($valid_token): ?>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label>Password Baru</label>
                        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Simpan Password</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>