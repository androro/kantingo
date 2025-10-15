<?php
include 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $new_password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 1. Cek jika password dan konfirmasi cocok
    if ($new_password !== $confirm_password) {
        header("Location: reset_password.php?token=$token&error=passwordmismatch");
        exit();
    }   

    // 2. Cek token lagi dan ambil password lama
    $stmt = $mysqli->prepare("SELECT id, password FROM users WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $user_id = $user['id'];
        $old_hashed_password = $user['password'];

        // 3. Cek jika password baru sama dengan password lama
        if (password_verify($new_password, $old_hashed_password)) {
            header("Location: reset_password.php?token=$token&error=samepassword");
            exit();
        }

        // Jika semua validasi lolos, hash password baru
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password dan hapus token
        $stmt_update = $mysqli->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE id = ?");
        $stmt_update->bind_param("si", $hashed_password, $user_id);
        $stmt_update->execute();

        $_SESSION['message'] = "Password Anda berhasil direset! Silakan login.";
        header("Location: login.php");
        exit();
    } else {
        header("Location: reset_password.php?token=$token&error=invalidtoken");
        exit();
    }
}

