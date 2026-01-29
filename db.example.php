<?php
// db.example.php
// Ubah nama file ini menjadi db.php dan sesuaikan kredensialnya
$host = 'localhost';
$db   = 'auth_db';
$user = 'root'; 
$pass = ''; // Isi password database Anda di sini

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>