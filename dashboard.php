<?php
// dashboard.php
session_start();

// CEK KEAMANAN: Jika belum login, tendang ke login.php
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Aplikasi Aman</a>
            <div class="d-flex">
                <span class="navbar-text text-white me-3">
                    Halo, <?php echo htmlspecialchars($_SESSION['username']); ?>!
                </span>
                <a href="profile.php" class="btn btn-primary">Kelola Profil & Password</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="card">
            <div class="card-header">
                Dashboard User
            </div>
            <div class="card-body">
                <h5 class="card-title">Selamat Datang di Area Member</h5>
                <p class="card-text">
                    Jika Anda melihat halaman ini, berarti Anda telah berhasil melewati:
                </p>
                <ul class="list-group list-group-flush mb-3">
                    <li class="list-group-item">✅ Login Username & Password</li>
                    <li class="list-group-item">✅ Verifikasi Google Authenticator (2FA)</li>
                </ul>
                <a href="profile.php" class="btn btn-primary">Lihat Profil</a>
            </div>
        </div>
    </div>
</body>
</html>