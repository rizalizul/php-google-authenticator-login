<?php
// profile.php
require 'db.php';

// 1. CEK LOGIN
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$user_id = $_SESSION['user_id'];

// 2. PROSES UBAH PASSWORD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Cek CSRF
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        die("Akses ditolak: Token CSRF tidak valid.");
    }

    $current_pass = $_POST['current_password'];
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];

    // Ambil password lama dari DB untuk verifikasi
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $data = $stmt->fetch();

    // Validasi
    if (!password_verify($current_pass, $data['password'])) {
        $message = '<div class="alert alert-danger">Password Lama salah!</div>';
    } elseif ($new_pass !== $confirm_pass) {
        $message = '<div class="alert alert-danger">Konfirmasi password baru tidak cocok!</div>';
    } elseif (strlen($new_pass) < 6) {
        $message = '<div class="alert alert-danger">Password baru minimal 6 karakter!</div>';
    } else {
        // Eksekusi Ganti Password
        $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
        
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($update->execute([$new_hash, $user_id])) {
            $message = '<div class="alert alert-success">Password berhasil diubah!</div>';
        } else {
            $message = '<div class="alert alert-danger">Gagal mengubah password.</div>';
        }
    }
}

// 3. AMBIL DATA PROFIL (Untuk ditampilkan)
$stmt = $pdo->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Aplikasi Aman</a>
            <div class="d-flex">
                <a href="dashboard.php" class="btn btn-outline-light btn-sm me-2">Dashboard</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row justify-content-center">
            
            <div class="col-md-4 mb-3">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        Info Akun
                    </div>
                    <div class="card-body text-center">
                        <img src="https://ui-avatars.com/api/?name=<?php echo $user['username']; ?>&background=random" class="rounded-circle mb-3" width="80">
                        <h5><?php echo htmlspecialchars($user['username']); ?></h5>
                        <p class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></p>
                        <hr>
                        <div class="text-start small">
                            <strong>Status:</strong> <span class="badge bg-success">Aktif</span><br>
                            <strong>Bergabung:</strong> <?php echo date('d M Y', strtotime($user['created_at'] ?? 'now')); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold">
                        Keamanan (Ubah Password)
                    </div>
                    <div class="card-body">
                        
                        <?php echo $message; ?>

                        <form method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">

                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="current_password" class="form-control" required>
                                <div class="form-text">Masukkan password saat ini untuk verifikasi.</div>
                            </div>
                            
                            <hr>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <input type="password" name="new_password" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ulangi Password Baru</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Simpan Password Baru</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>