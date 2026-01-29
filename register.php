<?php
// register.php
require 'vendor/autoload.php';
require 'db.php';

use Sonata\GoogleAuthenticator\GoogleAuthenticator;
use Sonata\GoogleAuthenticator\GoogleQrUrl;

$g = new GoogleAuthenticator();
$secret = $g->generateSecret();
$message = '';

// Fungsi membuat kode cadangan acak
function generateBackupCodes($qty = 5) {
    $codes = [];
    for ($i = 0; $i < $qty; $i++) {
        $codes[] = bin2hex(random_bytes(4)); // Contoh: a1b2c3d4
    }
    return $codes;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Cek Token CSRF sebelum memproses apapun
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('<div class="alert alert-danger">Error: Sesi kadaluarsa atau akses ilegal. Silakan refresh halaman.</div>');
    }

    $username = htmlspecialchars($_POST['username']);
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $secret_code = $_POST['secret'];
    
    // Ambil kode backup dari form
    $backup_codes_json = $_POST['backup_codes_static'];

    // --- PERBAIKAN LOGIKA DI SINI ---
    // 1. Cek Username ATAU Email sekaligus
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    
    if ($check->rowCount() > 0) {
        $message = '<div class="alert alert-danger">Username atau Email sudah terdaftar!</div>';
    } else {
        // 2. Jika aman, Lakukan INSERT lengkap (termasuk Email)
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, google_secret, backup_codes) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$username, $email, $password, $secret_code, $backup_codes_json])) {
            $message = '<div class="alert alert-success">Registrasi berhasil! Silakan <a href="login.php">Login</a>.</div>';
        } else {
            $message = '<div class="alert alert-danger">Terjadi kesalahan sistem.</div>';
        }
    }
}

// Generate tampilan kode untuk user
$display_codes = generateBackupCodes(); 
$codes_for_db = json_encode($display_codes);

$qrCodeUrl = GoogleQrUrl::generate($_SERVER['HTTP_HOST'], $secret, 'MyAppSecured');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register Akun</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center py-5">
        <div class="card shadow-sm" style="width: 500px;">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Daftar Akun</h3>
                <?php echo $message; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <input type="hidden" name="secret" value="<?php echo $secret; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                             <div class="text-center mb-3 p-2 bg-white border rounded">
                                <small class="text-muted">Scan QR Code:</small>
                                <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="img-fluid" style="max-width: 150px;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-warning p-2">
                                <strong>PENTING!</strong><br>
                                Simpan kode cadangan ini:
                                <ul class="mb-0 mt-2 ps-3 small text-monospace" style="font-family: monospace;">
                                    <?php foreach($display_codes as $code): ?>
                                        <li><?php echo $code; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <input type="hidden" name="backup_codes_static" value='<?php echo $codes_for_db; ?>'>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Daftar & Simpan Kode</button>
                    
                    <div class="mt-2 text-danger small fst-italic">
                        *Catatan: Saat Anda klik Daftar, sistem akan menyimpan kode-kode di atas. Pastikan dicatat.
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>