<?php
// verify_2fa.php
session_start();
require 'vendor/autoload.php';
require 'db.php'; // Kita butuh koneksi DB untuk hapus kode yang dipakai

use Sonata\GoogleAuthenticator\GoogleAuthenticator;

if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Cek Token CSRF sebelum memproses apapun
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('<div class="alert alert-danger">Error: Sesi kadaluarsa atau akses ilegal. Silakan refresh halaman.</div>');
    }
    
    $code = trim($_POST['code']);
    $secret = $_SESSION['temp_secret'];
    $user_id = $_SESSION['temp_user_id'];
    
    $login_success = false;

    // 1. Cek Apakah ini Kode Google Auth? (Biasanya 6 digit angka)
    $g = new GoogleAuthenticator();
    if ($g->checkCode($secret, $code)) {
        $login_success = true;
    } 
    // 2. Jika bukan, Cek Apakah ini Kode Cadangan?
    else {
        // Ambil data backup codes dari DB
        $stmt = $pdo->prepare("SELECT backup_codes FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $data = $stmt->fetch();
        
        $backup_codes = json_decode($data['backup_codes'], true); // Decode jadi Array PHP

        // Cek apakah kode yang diinput ada di dalam array backup_codes?
        if (is_array($backup_codes) && in_array($code, $backup_codes)) {
            $login_success = true;

            // HAPUS Kode yang sudah dipakai (Security Best Practice)
            // Cari posisi kode dalam array, lalu hapus
            $key = array_search($code, $backup_codes);
            unset($backup_codes[$key]);
            
            // Kembalikan ke format JSON dan Update Database
            $new_json = json_encode(array_values($backup_codes));
            $update = $pdo->prepare("UPDATE users SET backup_codes = ? WHERE id = ?");
            $update->execute([$new_json, $user_id]);
        }
    }

    if ($login_success) {
        // --- FITUR REMEMBER DEVICE ---
        if (isset($_POST['trust_device'])) {
            // 1. Buat Token Random
            $token = bin2hex(random_bytes(32));
            
            // 2. Simpan di Database
            $upd = $pdo->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
            $upd->execute([$token, $user_id]);

            // 3. Simpan di Cookie Browser (30 Hari)
            // setcookie(nama, nilai, expired, path, domain, secure, httponly)
            setcookie('remember_2fa', $token, time() + (30 * 24 * 60 * 60), "/", "", false, true);
        }
        // -----------------------------
        
        // PROSES LOGIN SUKSES
        $_SESSION['user_id'] = $_SESSION['temp_user_id'];
        $_SESSION['username'] = $_SESSION['temp_username'];
        
        unset($_SESSION['temp_user_id']);
        unset($_SESSION['temp_secret']);
        unset($_SESSION['temp_username']);
        
        header("Location: dashboard.php");
        exit;
    } else {
        $error = '<div class="alert alert-danger text-center">Kode OTP atau Kode Cadangan Salah!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm text-center" style="width: 400px;">
            <div class="card-body p-4">
                <h4 class="mb-3">Keamanan Tambahan</h4>
                <p class="text-muted small">
                    Masukkan kode dari aplikasi <strong>Google Authenticator</strong><br>
                    ATAU<br>
                    Masukkan salah satu <strong>Kode Cadangan</strong> Anda.
                </p>
                
                <?php echo $error; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-4">
                        <input type="text" name="code" class="form-control form-control-lg text-center" placeholder="Kode OTP / Backup Code" required autocomplete="off">
                    </div>
                    <div class="form-check mb-3 text-start">
                        <input class="form-check-input" type="checkbox" name="trust_device" id="trustDevice">
                        <label class="form-check-label" for="trustDevice">
                            Ingat perangkat ini selama 30 hari
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Verifikasi Masuk</button>
                    <a href="login.php" class="btn btn-link mt-2 text-decoration-none text-muted">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>