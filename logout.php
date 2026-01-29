<?php
// logout.php
session_start();
session_destroy(); // Hapus sesi login saat ini

// JANGAN HAPUS COOKIE 'remember_2fa' AGAR PERANGKAT TETAP DIINGAT
// setcookie('remember_2fa', '', time() - 3600, "/");  <-- Hapus atau Komentari baris ini

header("Location: login.php");
exit;
?>