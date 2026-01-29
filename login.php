<?php
// login.php
session_start();
require 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Cek Token CSRF sebelum memproses apapun
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die('<div class="alert alert-danger">Error: Sesi kadaluarsa atau akses ilegal. Silakan refresh halaman.</div>');
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Ambil data user beserta status login-nya
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user) {
        // 2. CEK APAKAH AKUN TERKUNCI?
        // Kita bandingkan waktu sekarang dengan waktu 'locked_until'
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            // Hitung sisa waktu dalam menit
            $sisa_waktu = ceil((strtotime($user['locked_until']) - time()) / 60);
            $error = '<div class="alert alert-danger text-center">
                        Akun terkunci karena terlalu banyak percobaan gagal.<br>
                        Silakan coba lagi dalam <strong>'.$sisa_waktu.' menit</strong>.
                      </div>';
        } 
        else {
            // 3. JIKA TIDAK TERKUNCI, CEK PASSWORD
            if (password_verify($password, $user['password'])) {
                // --- CEK APAKAH PERANGKAT INI SUDAH DIPERCAYA? ---
                $skip_2fa = false;

                // Cek apakah ada cookie DI BROWSER user & token DI DATABASE
                if (isset($_COOKIE['remember_2fa']) && !empty($user['remember_token'])) {
                    // Bandingkan isinya
                    if (hash_equals($user['remember_token'], $_COOKIE['remember_2fa'])) {
                        $skip_2fa = true;
                    }
                }

                // --- KEPUTUSAN ---
                if ($skip_2fa) {
                    // JIKA PERANGKAT DIPERCAYA -> LANGSUNG DASHBOARD
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Reset counter gagal
                    $reset = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                    $reset->execute([$user['id']]);

                    header("Location: dashboard.php");
                    exit;
                } else {
                    // PERLU 2FA (Seperti Biasa)
                    
                    // Reset counter gagal
                    $reset = $pdo->prepare("UPDATE users SET failed_attempts = 0, locked_until = NULL WHERE id = ?");
                    $reset->execute([$user['id']]);

                    $_SESSION['temp_user_id'] = $user['id'];
                    $_SESSION['temp_secret'] = $user['google_secret'];
                    $_SESSION['temp_username'] = $user['username'];
                    
                    header("Location: verify_2fa.php");
                    exit;
                }

            } else {
                // --- PASSWORD SALAH ---
                
                // Tambah counter gagal +1
                $new_attempts = $user['failed_attempts'] + 1;
                
                // Jika sudah 5 kali gagal, KUNCI AKUN 15 Menit
                if ($new_attempts >= 5) {
                    $lock_stmt = $pdo->prepare("UPDATE users SET failed_attempts = ?, locked_until = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id = ?");
                    $lock_stmt->execute([$new_attempts, $user['id']]);
                    
                    $error = '<div class="alert alert-danger text-center">
                                Batas percobaan terlampaui!<br>Akun Anda dikunci selama 15 menit.
                              </div>';
                } else {
                    // Belum 5 kali, cuma update counter
                    $update = $pdo->prepare("UPDATE users SET failed_attempts = ? WHERE id = ?");
                    $update->execute([$new_attempts, $user['id']]);
                    
                    $sisa_coba = 5 - $new_attempts;
                    $error = '<div class="alert alert-danger text-center">
                                Password salah! Sisa percobaan: <strong>'.$sisa_coba.'</strong>
                              </div>';
                }
            }
        }
    } else {
        // User tidak ditemukan (Demi keamanan, jangan bilang "User tidak ada", bilang saja "Username/Pass salah")
        $error = '<div class="alert alert-danger text-center">Username atau Password salah!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Aman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="card shadow-sm" style="width: 350px;">
            <div class="card-body p-4">
                <h3 class="text-center mb-4">Login System</h3>
                
                <?php echo $error; ?>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    
                    <div class="text-center mt-3 d-flex justify-content-between">
                        <small><a href="forgot_password.php" class="text-decoration-none">Lupa Password?</a></small>
                        <small><a href="register.php" class="text-decoration-none">Daftar Akun</a></small>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>