<?php
// Konfigurasi Database untuk Lingkungan Lokal (XAMPP)

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'kantingo_db';

// Membuat koneksi ke database
$mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Memeriksa koneksi
if ($mysqli->connect_error) {
    // Jika koneksi gagal, hentikan eksekusi dan tampilkan pesan error
    die("Connection failed: " . $mysqli->connect_error);
}

// Mengatur charset ke utf8mb4 untuk mendukung berbagai karakter
$mysqli->set_charset("utf8mb4");

?>