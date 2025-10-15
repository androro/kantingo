<?php
session_start();

// Hanya proses jika metodenya POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'db.php';

    // Periksa apakah koneksi database berhasil
    if (!isset($mysqli) || $mysqli->connect_error) {
        // Hentikan eksekusi jika koneksi gagal
        die("Koneksi ke database gagal. Periksa file db.php.");
    }

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Gunakan prepared statement untuk mencegah SQL Injection
    $stmt = $mysqli->prepare("SELECT id, username, password, is_verified FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verifikasi password yang di-hash
        if (password_verify($password, $user['password'])) {
            
            // Periksa apakah akun sudah diverifikasi
            if ($user['is_verified'] == 1) {
                // Set session dan redirect ke halaman utama
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit();
            } else {
                // Akun belum diverifikasi
                header("Location: login.php?error=notverified");
                exit();
            }
        } else {
            // Password salah
            header("Location: login.php?error=wrongpassword");
            exit();
        }
    } else {
        // Username tidak ditemukan
        header("Location: login.php?error=nouser");
        exit();
    }

    $stmt->close();
    $mysqli->close();

} else {
    // Jika bukan metode POST, redirect kembali ke halaman login
    header("Location: login.php");
    exit();
}
?>

